<?php 

  global $COGUMELO_RELATIONSHIP_MODEL;
  $COGUMELO_RELATIONSHIP_MODEL = array();

  /**
  * Utils for VO objects and relationship
  *
  * @package Cogumelo Model
  */
  class VOUtils {


  /**
  * List and include all Models and VOs from project
  * 
  * @return array
  */
  static function listVOs() {

    $voarray = array();

    // VOs into APP
    $voarray = self::mergeVOs($voarray, SITE_PATH.'classes/model/' ); // scan app model dir

    global $C_ENABLED_MODULES;
    foreach($C_ENABLED_MODULES as $modulename) {
      // modules into APP
      $voarray = self::mergeVOs($voarray, SITE_PATH.'../modules/'.$modulename.'/classes/model/', $modulename );
      // modules into DIST
      $voarray = self::mergeVOs($voarray, COGUMELO_DIST_LOCATION.'/distModules/'.$modulename.'/classes/model/', $modulename );
      // modules into COGUMELO 
      $voarray = self::mergeVOs($voarray, COGUMELO_LOCATION.'/coreModules/'.$modulename.'/classes/model/', $modulename );
    }


    return $voarray;
  }


  /**
  * Alias for listVOs method
  * 
  * @return array
  */
  static function includeVOs() {
    return self::listVOs();
  }


  /**
  * Merge into original array the new (VOs or Models) that find the directory passed and returns it merged
  * 
  * @param array $voarray original array
  * @param string $dir path to search new Models or VOs to merge with original array
  * @param string $modulename name of module to search (default is the appplication)
  * 
  * @return array
  */
  static function mergeVOs($voarray, $dir, $modulename='app') {
    $vos = array();

    // VO's from APP
    if ( is_dir($dir) && $handle = opendir( $dir )) {

      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {

          if(substr($file, -9) == 'Model.php' || substr($file, -6) == 'VO.php'){
            $classVoName = substr($file, 0,-4);

            // prevent reload an existing vo in other place
            if (!array_key_exists( $classVoName, $voarray )) {
              require_once($dir.$file);
              $vos[ $classVoName ] = array('path' => $dir, 'module' => $modulename );
            }
          }
        }
      }
      closedir($handle);
    }

    return array_merge( $voarray , $vos );
  }



  /**
  * Get VO or Model Cols
  * 
  * @param string $voName
  * 
  * @return array
  */
  static function getVOCols($voName) {
    $retCols = array();

    $vo = new $voName();

    foreach( $vo->getCols() as $colK => $col ) {
        $retCols[] = $colK;
    }

    return $retCols;
  }



  /**
  * Get basic VO or Model relationship with other VOs or Models
  * 
  * @param object $VOInstance 
  * @param boolean $includeKeys 
  *  
  * @return array
  */
  static function getVOConnections( $VOInstance, $includeKeys= false ) {
    $relationships = array();

    if( sizeof( $VOInstance->getCols() ) > 0 ) {
      foreach ( $VOInstance->getCols() as $attrKey=>$attr ) {
        if( array_key_exists( 'type', $attr ) && $attr['type'] == 'FOREIGN' ){

          if( !$includeKeys ) {
            $relationships[] =  $attr['vo'];
          }
          else {
            $relationships[ $attr['vo'] ] =  array('parent' => $attrKey, 'related'=>$attr['key'] );
          }
        }
      }
    }

    return $relationships;
  }



  /**
  * Get relationship scheme from all VOs and Models
  *  
  * @return array
  */
  static function getAllRelScheme() {

    $ret = array();

    foreach ( self::listVOs() as $voName=>$voDef) {
      $vo = new $voName();
      $ret[ $voName ] = array( 
                      'name' => $voName, 
                      'relationship' => self::getVOConnections( $vo ), 
                      'extendedRelationship' => self::getVOConnections( $vo, true ),
                      'elements' => sizeof( $vo->getCols() ),
                      'module' => $voDef['module']
                    );
    }

    return $ret;
  }



  /**
  * Get relationship scheme from VO or Models, resolving son VOs and Models
  *  
  * @param string $voName Name of VO or Model
  * @param array $parentInfo parent VO info 
  * 
  * @return array
  */
  static function getVORelationship( $voName, $parentInfo=array( 'parentVO' => false, 'parentTable'=>false, 'parentId'=>false, 'relatedWithId'=>false ) ) {

    $vo = new $voName();
    $relArray = array(
                        'vo' => $voName, 
                        'table' => $vo::$tableName
                      );
    $relArray = array_merge( $relArray, $parentInfo);

    $relArray['cols'] = self::getVOCols( $voName );
    $relArray['relationship'] = array();


    $allVOsRel = self::getAllRelScheme();

    if( sizeof( $allVOsRel ) > 0) {
      foreach( $allVOsRel as $roRel ) {
        if(  
          (
            in_array( $roRel['name'], $allVOsRel[$voName]['relationship']) ||   // relation from this to other VO
            in_array( $voName, $roRel['relationship'] )                         // relation fron other to this VO
          ) && 
          ($parentInfo['parentVO'] == false || $roRel['name'] != $parentInfo['parentVO'])
        ) {


            $sonParentArray = array(
             'parentVO' => $voName, 
             'parentTable'=> $vo::$tableName, 
             'parentId'=> 'NO', 
             'relatedWithId'=> 'NO' 
            );


            if( array_key_exists( $roRel['name'], $allVOsRel[$voName]['extendedRelationship'] ) ) {
              $sonParentArray['parentId'] = $allVOsRel[$voName]['extendedRelationship'][$roRel['name']]['parent'];
              $sonParentArray['relatedWithId'] = $allVOsRel[$voName]['extendedRelationship'][$roRel['name']]['related'];
            }
            else {
              $sonParentArray['parentId'] = $roRel['extendedRelationship'][$voName]['related'];
              $sonParentArray['relatedWithId']  = $roRel['extendedRelationship'][$voName]['parent'];
            }
            
            $relArray['relationship'][] = self::getVORelationship( $roRel['name'], $sonParentArray );
          }
      }
    }

    return $relArray;
  }



  /**
  * Generate temporal json files with relationship descriptions
  *  
  * 
  * @return void
  */
  static function createModelRelTreeFiles() {
    Cogumelo::load('coreModel/'.DB_ENGINE.'/'.ucfirst( DB_ENGINE ).'DAORelationship.php');

    eval( '$mrel = new '.ucfirst( DB_ENGINE ).'DAORelationship();' );

    foreach( self::listVOs() as $voName => $vo) {
      file_put_contents( APP_TMP_PATH.'/modelRelationship/'.$voName.'.json' , json_encode(self::getVORelationship($voName)) );
    }
  }



  /**
  * Get relationship keys from VO or Model name
  *  
  * @param string $nameVO name of VO or Model
  * 
  * @return array
  */
  static function getRelkeys( $nameVO, $tableAsKey = false ) {

    return self::getRelKeysByRelObj( self::getRelObj( $nameVO ), $tableAsKey );
  }



  /**
  * Get relationship keys from relationship object
  *  
  * @param object $voRel relationship object (readed from temporal json files)
  * @param boolean tableAsKey table name as array key when true, else return VO name as keys
  * 
  * @return array
  */
  static function getRelKeysByRelObj( $voRel, $tableAsKey= false ) {
    $relKeys = false;

    if($voRel) {
      $relKeys = array();

      if( sizeof($voRel->relationship) > 0 ) {
        foreach ($voRel->relationship as $voName => $rel) {
          if( $tableAsKey ){
            $relKeys[$rel->table] = $rel->vo;  
          }
          else {
            $relKeys[$rel->vo] = $rel->table;
          }
        }
      }
    }

    return $relKeys;
  }



  /**
  * gets Relationship object from global array. If not exist this global array reads it from temporal .json
  *  
  * @param string $nameVO VO or Model name
  * 
  * @return object
  */
  static function getRelObj($nameVO) {
    global $COGUMELO_RELATIONSHIP_MODEL;

    $ret = false;

    if( !array_key_exists($nameVO, $COGUMELO_RELATIONSHIP_MODEL) ) {
      if(file_exists( APP_TMP_PATH.'/modelRelationship/'.$nameVO.'.json' )){
        $COGUMELO_RELATIONSHIP_MODEL[ $nameVO ] = json_decode( 
                    file_get_contents(APP_TMP_PATH.'/modelRelationship/'.$nameVO.'.json') 
              );
        $ret = $COGUMELO_RELATIONSHIP_MODEL[ $nameVO ];
      }

    }
    else {
      $ret = $COGUMELO_RELATIONSHIP_MODEL[ $nameVO ];
    }


    return $ret;
  }


  /**
  * Look if exist VO or Model name into relationship object
  *  
  * @param string $voName VO or Model name
  * @param object $relObj relationship Object (readed from tmp .json relationship file)
  * 
  * @return object
  */
  static function searchVOinRelObj($voName, $relObj) {
    $relObjSon = -1;

    if( sizeof($relObj->relationship ) > 0 ){
      foreach ($relObj->relationship as $candidate) {
        if( $candidate->vo == $voName ){
          $relObjSon = $candidate;
          break;
        }
      }
    }


    return $relObjSon;
  }

}


