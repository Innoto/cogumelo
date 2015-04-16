<?php


Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');


class UserRoleModel extends Model
{
  static $tableName = 'user_userRole';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'user'=> array(
      'type'=>'FOREIGN',
      'vo' => 'UserModel',
      'key' => 'id'
    ),
    'role'=> array(
      'type'=>'FOREIGN',
      'vo' => 'RoleModel',
      'key' => 'id'
    )
  );

  static $extraFilters = array();  

  function __construct($datarray = array(),  $otherRelObj= false )
  {
    parent::__construct($datarray, $otherRelObj );
  }

}