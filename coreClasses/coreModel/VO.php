<?php

// load all VO's
Cogumelo::load('coreModel/VOUtils.php');
VOUtils::includeVOs();


/**
 * Value Object (Used by the model)
 *
 * @package Cogumelo Model
 */
Class VO
{
  var $name = '';
  var $data = array();
  var $depData = array();
  var $depKeys = array();
  var $relObj = false;

  function __construct(array $datarray, $otherRelObj= false ) {
    $this->setData( $datarray, $otherRelObj );
  }

  /**
   * Sets data of VO
   *
   * @param array $datarray array (data referenced by keys)
   * @param object $otherRelObj internal use only
   *
   * @return void
   */
  function setData(array $datarray, $otherRelObj= false ){

    // get class name
    $this->name = get_class( $this );

    // Common developer errors
    if(!isset($this::$tableName)){
      Cogumelo::error('all VO Must have declared an $this::$tableName');
      return false;
    }
    if(!isset($this::$cols)){
      Cogumelo::error($this::$tableName.'VO Must have an self::$cols array (See Cogumelo documentation)');
      return false;
    }
    if(!$this->getFirstPrimarykeyId()){
      Cogumelo::error($this::$tableName.'VO Must be declared at least one primary key in $this::$cools array (See Cogumelo documentation)');
      return false;
    }


    // seting relationship keys
    if( $otherRelObj == false ) { 
      $this->relObj = VOUtils::getRelObj( $this->name ) ;
    }
    else if( is_object( $otherRelObj ) ) {
      $this->relObj = $otherRelObj;
    }

    $this->depKeys = VOUtils::getRelKeysByRelObj( $this->relObj, true );
    $this->setVarList( $datarray );
  }


  /**
   * set variable list (initializes entity)
   *
   * @param array $datarray array (data referenced by keys)
   *
   * @return object
   */
  function setVarList(array $datarray) {

    // rest of variables
    foreach($datarray as $k=>$data) {

      if( preg_match('#(.*)\.(.*)#', $k ,$tAndkey) ){
        $datakey = $tAndkey[2];
      }
      else {
        $datakey = $k;
      }

      // set dependence VOs
      if( array_key_exists( $datakey , $this->depKeys) ){
        if( $data ) {
          $this->setDepVOs( $data, $this->depKeys[$datakey], VOUtils::searchVOinRelObj( $this->depKeys[$datakey], $this->relObj) );
        }
      }
      // set cols
      else {
        $this->setter( $datakey, $data );
      }
    }
  }


  /**
   * set dependence VOs from data
   *
   * @param array $data 
   * @param string $voName name of VO or Model
   * @param object $relObj related object
   *
   * @return void
   */
  function setDepVOs( $data, $voName, $relObj ) {


    if( is_array($data) ) {
      foreach( $data as $d ) {
        $this->setDepVO($d, $voName, $relObj);
      }
    }
    else
    {
      // when is first rel decode it
      if(! $d = json_decode($data) ){
        Cogumelo::error('Problem decoding VO JSON in '.$this->name.'. Provably the result is truncated, try to increase DB_MYSQL_GROUPCONCAT_MAX_LEN constant in configuration or optimize query.');
      }

      $this->setDepVO($d, $voName, $relObj);
    }
  }


  /**
   * set dependence VO from data
   *
   * @param object $dataVO 
   * @param string $voName name of VO or Model
   * @param object $relObj related object
   *
   * @return object
   */
  function &setDepVO( $dataVO, $voName, $relObj  ) {
    $retvO = false;
    $attribute =  $relObj->parentId;

    if( $this->isForeignKey( $attribute ) ){
      $retVO = new $voName( (array) $dataVO, $relObj );
      $this->depData[ $attribute ] = $retVO;
    }
    else {
      $retVO = new $voName( (array) $dataVO, $relObj );
      $this->depData[ $attribute] = array( $retVO );
    }

    return $retVO;
  }


  /**
   * get VO or Model Name
   *
   * @return string
   */
  function getVOClassName() {
    return $this->name;
  }


  /**
   * gets primary key id
   *
   * @return string
   */
  function getFirstPrimarykeyId() {

    foreach($this::$cols as $cid => $col) {
      if(array_key_exists('primarykey', $this::$cols[$cid])){
        if($this::$cols[$cid]['primarykey'] == true){
          return $cid;
        }
      }
    }

    return false;
  }


  /**
   * get columns list
   *
   * @return array
   */
  function getCols(){
    return $this::$cols;
  }


  /**
   * get BBDD table name
   *
   * @return string
   */
  function getTableName(){
    return $this::$tableName;
  }


  /**
   * set any data attribute by key
   *
   * @param mixed $setterkey key or array 
   * @param mixed $value 
   * 
   * @return void
   */
  function &setter( $setterkey, $value = false ) {
    $retObj = false;

    if( is_array($setterkey) && $value == false ) {
      foreach( $setterkey as $k => $e) {
        $this->setter($k, $e);
      }
      $retObj = true;
    }


    if( array_key_exists($setterkey, $this->getCols()) ) {
      // set values
      $this->data[$setterkey] = $value;
      $retObj = $this;
    }
    else{
      Cogumelo::debug("key '". $setterkey ."' not exist in VO::". $this::$tableName);
    }

    return $retObj;
  }


  /**
   * set data dependence
   *
   * @param object $voObj VO or Model
   * @param string $fk attribute name
   * 
   * @return void
   */
  function setterDependence( $voObj, $fk = false ){
    $retVO = false;
    $voName = $voObj->getVOClassName();

    $references = array();
    foreach( $this->relObj->relationship as $rel ){
      if( $rel->vo == $voName ) {
        $references[ $voName ] = $rel;
      }
    }
    
    // Dependence not exist
    if( sizeof($references) == 0 ) {
      Cogumelo::error( $voObj->getVOClassName() .' is not dependence of: '.$this->getVOClassName() );
    }
    else
    if( sizeof($references) > 1 ) {

      // Dependence must be referenced by key id ( Same VO as dependency of two different keys )
      if( !$fk ) {
        Cogumelo::error( 'Various '.$voObj->getVOClassName() .' dependences into '.$this->getVOClassName().'. You must specify the foreign key reference.' );
      }
      else {
        // developer specified id key, search for it into relationship reference object
      }
    }
    else {
      // dependence exist, we doesn't need specify by key
      $retVO = $this->setDepVO( $voObj->data, $voName, array_pop( $references) );
    }


    //$this->refreshRelationshipKeyIds();

    return $retVO;
  }


  /**
   * get any data attribute by key
   *
   * @return mixed
   */
  function getter($getterkey) {

    $value = null;

    // get values
    if( array_key_exists($getterkey, $this->data) ) {
        $value = $this->data[$getterkey];
    }
    else {
      Cogumelo::debug("key '". $getterkey ."' not exist in VO::". $this::$tableName);
    }

    return $value;
  }

  /**
   * dependence getter
   * 
   * @param string $reference reference key 
   *
   * @return array
   */
  function getterDependence( $reference ) {
    $depReturn = false;

    if( array_key_exists($reference, $this->depData) ){
      $depReturn = &$this->depData[ $reference ];
    }

    return $depReturn;
  }



  function getKeys() {

    $retArray = array();

    foreach( $this->getCols() as $cK => $c) {
      $retArray[] = $cK;
    }

    return $retArray;
  }

  /**
   * get key list into string
   *
   * @return string
   */
  function getKeysToString( $fields, $resolveDependences=false ) {
    $retFields = array();

    // main vo Fields
    if( !$fields ) {
      $retFields = array_merge($retFields, array_keys( $this->getCols() ) );
    }
    else {
      $retFields = array_merge($retFields, $fields );
    }

    foreach($retFields as $fkey => $retF )  {
      $retFields[$fkey] = $this->getTableName().'.'.$retF;
    }

    // relationship cols
    if( $resolveDependences ) {
      $retFields = array_merge($retFields, VOUtils::getRelKeys(  $this->name ) );
    }


    return implode(', ', $retFields);
  }


  /**
   * dependence data 
   *
   * @return array
   */
  function &getDependences() {
    return $this->depData;
  }



  /**
   * get all dependences in no nested array
   *
   * @return array
   */
  function getDepInLinearArray( &$vo = false, $vosArray = array() ) {

    if(!$vo){
      $vo = $this;
    }

    if( sizeof( $vosArray)>0 ) {
      $voArrayKeys = array_keys( $vosArray );
      $vosArray[] = array( 'ref' => $vo, 'parentKey' => end( $voArrayKeys ) );
    }
    else {
      $vosArray[] = array( 'ref' => $vo, 'parentKey' => false );
    }

    $depData = $vo->depData;
    if( sizeof($depData) > 0  ) {
      foreach( $depData as $depVO ){
        if( is_array($depVO) ) {
          foreach($depVO as $dVO) {
            $vosArray = $vo->getDepInLinearArray( $dVO, $vosArray );
          }
        }
        else {
          $vosArray = $vo->getDepInLinearArray( $depVO, $vosArray );
        }
      }
    }

    return $vosArray;
  }


  function isForeignKey( $key ) {
    $res = false;
    if( array_key_exists( 'type', $this::$cols[ $key ]) &&  $this::$cols[ $key ]['type'] == 'FOREIGN') {
      $res = true;
    }


    return $res;
  }


  /**
   * get nested array with data (including loaded dependences)
   *
   * @return array
   */
  function getAllData() {

    $relationshipArrayData = array();

    foreach ( $this->getDependences()  as $dep ){

      if( is_array( $dep ) ){
        $depA = array();
        foreach( $dep as $d  ) {
          $depA[] = $d->getAllData();
        }
        $relationshipArrayData[] = $depA;
      }
      else {
        $relationshipArrayData[] = $dep->getAllData();
      }

    }

    return array( 'modelName' => $this->name, 'data' => $this->data, 'relationship' =>$relationshipArrayData);
  }



  /**
   * refresh all relationship ids from sons to parents
   *
   * @return void
   */
  function refreshRelationshipKeyIds() {
    $deps = $this->getDepInLinearArray();

    while( $dep = array_pop( $deps ) ){


      if( 
        array_key_exists('ref', $dep) &&
        array_key_exists('parentKey' , $dep) &&
        $dep['parentKey'] !== false &&
        array_key_exists( $dep['parentKey'], $deps) &&
        array_key_exists('ref', $deps[ $dep['parentKey'] ])
      ) {

        $vo = $dep['ref'];
        $voParent =$deps[ $dep['parentKey'] ]['ref'];


        if( $voParent->getter( $vo->relObj->parentId) ) {
          $vo->setter( $vo->relObj->relatedWithId , $voParent->getter( $vo->relObj->parentId) );
        }
        else {
          $voParent->setter( $vo->relObj->parentId , $vo->getter( $vo->relObj->relatedWithId  ) );
        }

        echo "parent ".$voParent->getVOClassName().".". $vo->relObj->parentId.": ". $voParent->getter( $vo->relObj->parentId  ).'<br>';
        echo "this ".$vo->getVOClassName().".".$vo->relObj->relatedWithId.": ". $vo->getter( $vo->relObj->relatedWithId  ).'<br><br>';
        
      }
    }
  }

}

