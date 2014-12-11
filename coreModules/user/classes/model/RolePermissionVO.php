<?php

Cogumelo::load('coreModel/VO.php');
user::load('model/PermissionVO.php');

class RolePermissionVO extends VO
{
  static $relatedVOs = array('RoleVO', 'PermissionVO');
  static $tableName = 'user_rolePermission';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),

    'role' => array(
      'name' => 'Role',
      'type'=>'FOREIGN',
      'vo' => 'RoleVO',
      'key' => 'id'
    ),

    'permission' => array(
      'name' => 'Permission',
      'type'=>'FOREIGN',
      'vo' => 'PermissionVO',
      'key' => 'id'
    )    
  );

  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}