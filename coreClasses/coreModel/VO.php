<?php

// load all VO's
Cogumelo::load('coreModel/VOUtils.php');
VOUtils::includeVOs();

Class VO
{

  var $data = array();

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


  function getCols(){
    return $this::$cols;
  }

  function getTableName(){
    return $this::$tableName;
  }

  // set an data attribute
  function setter($setterkey, $value = false)
  {


    if( array_key_exists($setterkey, $this->getCols()) ) {
      // set values
      $setterVO->data[$setterkey] = $value;
    }
    else{
      Cogumelo::debug("key '". $setterkey ."' not exist in VO::". $this::$tableName);
    }
  }



  // get an attribute
  function getter($getterkey)
  {

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
      $retFields = array_merge($retFields, VOUtils::getRelKeys( get_class( $this ) ) );
    }


    return implode(', ', $retFields);
  }


}

