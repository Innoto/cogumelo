<?php

Class VO
{

  var $attributes = array();
  var $relationship = array();

  function __construct(array $datarray){

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

    $this->setRelationshipVOs();
    $this->setVarList($datarray);

  }

  // set variable list (initializes entity)
  function setVarList(array $datarray) {
    // rest of variables
    foreach($datarray as $datakey=>$data) {
        $this->setter($datakey, $data);
    }
  }
  

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


  function setRelationshipVOs() {
    foreach( $this::$cols as $colKey => $col ) {
      if( $col['type'] == 'FOREIGN' ){
        $colVO = new $col['vo']();

        $this->relationship[$colKey] = array( 
                                              'parent_table' => $this::$tableName, 
                                              'parent_key' => $this::$tableName.'.'.$colKey,
                                              'vo_key' => $colVO::$tableName.'.'.$col['key'], 
                                              'VO' => $colVO, 
                                              'used' => false 
                                            );


        // look for circular relationship
        if( count( 
              array_intersect( 
                array_keys( $this->relationship ), 
                array_keys( $colVO->relationship ) 
              )
            ) == 0 
        ){
          $this->relationship = array_merge( $this->relationship, $colVO->relationship );
        }
        else 
        {
          Cogumelo::error('Circular relationship on VO "'.$this->tableName.'", column: '.$colKey);
          exit; // exits to prevent infinite loop
        }
      }
    }
  }

  function getDependenceVO( $tableName ) {

    $dependenceVO = false;

    if( $this::$tableName == $tableName){
      $dependenceVO = $this;  
    }
    else {
      if(! $dependenceVO = $this->relationship[$tableName]['VO']){
        Cogumelo::error('VO relationship "'.$tableName.'" doesnt exist in VO::'.$this::$tableName);
      }
    }
    return $dependenceVO;
  }

  function markRelationshipAsUsed( $tableName ) {
    if( in_array($tableName, array_keys($this->relationship)) ){
      $this->relationship[$tableName]['used'] = true;
    }
  }


  // set an attribute
  function setter($setterkey, $value = false)
  {

    if( preg_match('#^(.*?)\.(.*)$#', $setterkey, $setter_data) ) {
      $tableName = $setter_data[1];
      $columnKey = $setter_data[2];
    }
    else { 
      $tableName = $this::$tableName;
      $columnKey = $setterkey;
    }

    // choose VO
    $setterVO = $this->getDependenceVO($tableName);

    // set values
    if( $tableName == $setterVO::$tableName && in_array($columnKey, array_keys($setterVO::$cols)) ){
      $this->markRelationshipAsUsed( $tableName ); 
      $setterVO->attributes[$columnKey] = $value;
    }
    else{
      Cogumelo::error("key '". $setterkey ."' doesn't exist in VO::". $setterVO::$tableName);
    }
  }



  // get an attribute
  function getter($getterkey)
  {

    $value = false;

    if( preg_match('#^(.*?)\.(.*)$#', $getterkey, $getter_data) ) {
      $tableName = $getter_data[1];
      $columnKey = $getter_data[2];
    }
    else { 
      $tableName = $this::$tableName;
      $columnKey = $getterkey;
    }

    // choose VO
    $getterVO = $this->getDependenceVO($tableName);

    // get values
    if( $tableName == $getterVO::$tableName && in_array($columnKey, array_keys($getterVO::$cols)) ){
      $this->markRelationshipAsUsed( $tableName ); 

      $value = $getterVO->attributes[$columnKey];
    }
    else{
      Cogumelo::error("key '". $getterkey ."' doesn't exist in VO::". $setterVO::$tableName);
    }

    return $value;
/*
    if( $getter_data = preg_match('#^(.*?)\.(.*)$#', $getterkey) ) {
      $tableName = $getter_data[0];
      $columnKey = $getter_data[1];
    }
    else { 
      $tableName = $this::$tableName;
      $columnKey = $getterkey;
    }
    
    // choose VO
    $getterVO = $this->getDependenceVO($tableName);

    if( array_key_exists( $getterkey, $getterVO->attributes) ){
      $ret = $this->attributes[$getterkey];
    }
    else{
      $ret = null;
    }

    return $ret;*/
  }


  function getJoinArray() {

    $ret = array();
    foreach ( $this->relationship as $rel ) {
      array_push($ret, 
        array(
          'table' => $rel['VO']::$tableName, 
          'relationship' => array( $rel['parent_key'], $rel['vo_key'] ) 
        ) 
      );
    }
     //array('table' => t, $relationship => r);
    return $ret;
  }

  function getKeys($resolverelationship = false) {
    $keys = array();

    if( $resolverelationship ) {
      $keys = $this->getDependenceKeys();
    }
    else {
      $keys = array_keys($this::$cols);
    }

    return $keys;
  }

  function getKeysToString($resolverelationship = false) {

    $strKeys = '';
    $comma = '';

    foreach( $this->getKeys($resolverelationship) as $k ) {
      $strKeys .= $comma . $k . ' as `' . $k . '`';
      $comma = ', ';
    }

    return $strKeys;
  }

  function getDependenceKeys() {

    // get keys from this VO
    $keys =  $this->dependenceKeys( $this ) ;

    // get keys from relationship VO's
    foreach( $this->relationship as $dependence) {
      $keys = array_merge($keys, $this->dependenceKeys( $dependence['VO']));
    }

    return $keys;
  }


  function dependenceKeys($voObj) {

    $keys = array();


    foreach($voObj::$cols as $colKey => $col) {
      array_push( $keys, $voObj::$tableName . '.' . $colKey );
    }



    return $keys;
  }

  function toString(){
    $str = "\n " . $keyId. ': ' .$this->getter($keyId);
    foreach(array_keys($this->cools) as $k) {
      $str .= "\n " . $this->cools[$k] . ': ' .$this->getter($k);
    }

    return $str;
  }

}

