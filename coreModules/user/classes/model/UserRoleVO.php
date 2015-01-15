<?php


Cogumelo::load('coreModel/VO.php');
user::load('model/RoleVO.php');


class UserRoleVO extends VO
{
  static $tableName = 'user_userRole';
  static $isM2M = true;  
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'user'=> array(
      'name' => 'User',
      'type'=>'FOREIGN',
      'vo' => 'UserVO',
      'key' => 'id'
    ),
    'role'=> array(
      'name' => 'Role',
      'type'=>'FOREIGN',
      'vo' => 'RoleVO',
      'key' => 'id'
    )
  );

  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}