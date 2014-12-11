<?php

Cogumelo::load('coreModel/VO.php');

class PermissionVO extends VO
{
  static $relatedVOs = array('RolePermissionVO');  
  static $tableName = 'user_permission';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'name' => array(
      'name' => 'Name',
      'type' => 'CHAR',
      'size' => '100',
      'unique' => true
    )
  );

  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}