<?php

Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');

class RolePermissionModel extends Model
{
  static $tableName = 'user_rolePermission';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),

    'role' => array(
      'type'=>'FOREIGN',
      'vo' => 'RoleModel',
      'key' => 'id'
    ),

    'permission' => array(
      'type'=>'FOREIGN',
      'vo' => 'PermissionModel',
      'key' => 'id'
    )    
  );

  function __construct($datarray = array(),  $otherRelObj= false )
  {
    parent::__construct($datarray, $otherRelObj );
  }

}