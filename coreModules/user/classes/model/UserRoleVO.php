<?php


Cogumelo::load('coreModel/VO.php');
user::load('model/RoleVO.php');


class UserRoleVO extends VO
{
  static $tableName = 'user_userRole';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'userId' => array(
      'name' => 'User',
      'type' => 'INT'
    )
  );


  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}