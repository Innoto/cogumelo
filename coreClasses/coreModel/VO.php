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
      Cogumelo::debug("key '". $setterkey ."' doesn't exist in VO::". $setterVO::$tableName);
    }
  }



  // get an attribute
  function getter($getterkey)
  {

    $value = null;

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
      if( array_key_exists($columnKey, $getterVO->attributes) ) {
        $value = $getterVO->attributes[$columnKey];
      }
    }
    else{
      //Cogumelo::debug("key '". $getterkey ."' doesn't exist in VO::". $setterVO::$tableName);
    }

    return $value;

  }



  function toString(){
    $str = "\n " . $keyId. ': ' .$this->getter($keyId);
    foreach(array_keys($this->cools) as $k) {
      $str .= "\n " . $this->cools[$k] . ': ' .$this->getter($k);
    }

    return $str;
  }

}

