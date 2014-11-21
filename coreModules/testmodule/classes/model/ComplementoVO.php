<?php

Cogumelo::load('coreModel/VO.php');

class ComplementoVO extends VO
{ 
  static $tableName = 'complemento';
  static $cols = array(
    'id' => array(
      'type'=>'INT', 
      'primarykey'=>true,
      'autoincrement'=>true
    ),
    'relid' => array(
      'type'=>'INT', 
    ),    
    'name' => array(
      'desc' => 'Nome', 
      'type'=>'VARCHAR', 
      'size'=>30
    )
  );



  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }
  


}