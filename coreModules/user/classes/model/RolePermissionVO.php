<?php

Cogumelo::load('coreModel/VO.php');
user::load('model/PermissionVO.php');

class RolePermissionVO extends VO
{
  static $tableName = 'user_rolePermission';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'roleId' => array(
      'name' => 'Role',
      'type' => 'INT'
    )

    /*,

    'permissions' => array(
      'name' => 'Permissions',
      'type'=>'FOREIGN',
      'vo' => 'PermissionVO',
      'key' => 'id'
    )
    */
  );

  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}