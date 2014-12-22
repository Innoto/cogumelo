<?php

// load all VO's
Cogumelo::load('coreModel/VOUtils.php');
VOUtils::includeVOs();

Class VO
{

  var $data = array();
  var $dataRelationship = array();

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


  function getRelData() {
    return $this->dataRelationship;
  }


  function getRelScheme() {
    return VOUtils::getVORelationship();
  }

  // Is NM Table when only have other VO references or primary key in cols
  function isNMTable() {

  }


  function toString(){
    $str = "\n " . $keyId. ': ' .$this->getter($keyId);
    foreach(array_keys($this->cools) as $k) {
      $str .= "\n " . $this->cools[$k] . ': ' .$this->getter($k);
    }

    return $str;
  }

}

