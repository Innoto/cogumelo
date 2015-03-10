<?php

Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');

class PermissionModel extends Model
{
  static $tableName = 'user_permission';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'name' => array(
      'type' => 'CHAR',
      'size' => '100',
      'unique' => true
    )
  );

  function __construct($datarray = array(),  $otherRelObj= false )
  {
    parent::__construct($datarray, $otherRelObj );
  }

}