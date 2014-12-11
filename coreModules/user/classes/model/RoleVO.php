<?php

Cogumelo::load('coreModel/VO.php');
user::load('model/RolePermissionVO.php');

define("ROLE_SUPERADMIN", "10");
define("ROLE_USER", "11");

class RoleVO extends VO
{
  static $relatedVOs = array('UserRoleVO', 'RolePermissionVO');
  static $tableName = 'user_role';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'name' => array(
      'name' => 'Name',
      'type' => 'CHAR',
      'size' => '10'
    ),
    'description'=> array(
      'name' => 'Descripción',
      'type' => 'TEXT',
      'size' => '300'
    )
  );

  static $insertValues = array(
    array('name' => 'superAdmin', 'description' => 'SuperAdmin'),
    array('name' => 'user', 'description' => 'User'),
  );

  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}