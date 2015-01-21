<?php 

  global $COGUMELO_RELATIONSHIP_MODEL;
  $COGUMELO_RELATIONSHIP_MODEL = array();


  class VOUtils {

  // list VOs with priority
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

  
  static function mergeVOs($voarray, $dir, $modulename='app') {
    $vos = array();

    // VO's from APP
    if ( is_dir($dir) && $handle = opendir( $dir )) {

      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {

          if(substr($file, -6) == 'VO.php'){
            $class_vo_name = substr($file, 0,-4);

            // prevent reload an existing vo in other place
            if (!array_key_exists( $class_vo_name, $voarray )) {
              require_once($dir.$file);
              $vos[ $class_vo_name ] = array('path' => $dir, 'module' => $modulename );
            }
          }
        }
      }
      closedir($handle);
    }




    return array_merge( $voarray , $vos );
  }


  static function getVOCols($voName) {
    $retCols = array();

    $vo = new $voName();

    foreach( $vo->getCols() as $colK => $col ) {
        $retCols[] = $colK;
    }

    return $retCols;
  }

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



  static function createModelRelTreeFiles() {
    Cogumelo::load('coreModel/mysql/MysqlDAORelationship.php');

    $mrel = new MysqlDAORelationship();

    foreach( self::listVOs() as $voName => $vo) {
      file_put_contents( APP_TMP_PATH.'/modelRelationship/'.$voName.'.json' , json_encode(self::getVORelationship($voName)) );
    }
  }



  static function getRelTree( $vo ) {
    $ret = false;

    $voJSONPath = APP_TMP_PATH.'/modelRelationship/'.$voName.'.json';

    if( file_exists( $voJSONPath ) ) {
      $ret = json_decode( file_get_contents( $voJSONPath ) );
    }
    else{
      Cogumelo::error('No dependence file:('.$voJSONPath.') for "'.$vo.'", please execute ./cogumelo createRelSchemes');
    }

    return $ret;
  }


  static function includeVOs() {
    self::listVOs();
  }


  static function getRelkeys($nameVO) {

    $voRel = self::getRelObj($nameVO);
    $relKeys = array();


    if( sizeof($voRel->relationship) > 0 ) {
      foreach ($voRel->relationship as $voName => $rel) {
        $relKeys[] = $rel->table;
      }
    }

    return $relKeys;
  }

  static function getRelObj($nameVO) {
    global $COGUMELO_RELATIONSHIP_MODEL;
    

    if( !array_key_exists($nameVO, $COGUMELO_RELATIONSHIP_MODEL) ) {
      $COGUMELO_RELATIONSHIP_MODEL[ $nameVO ] = json_decode( 
                    file_get_contents(APP_TMP_PATH.'/modelRelationship/UserVO.json') 
                  );
    }

    return $COGUMELO_RELATIONSHIP_MODEL[ $nameVO ];
  }

}


