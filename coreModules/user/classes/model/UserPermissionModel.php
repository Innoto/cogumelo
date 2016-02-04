<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');


class UserPermissionModel extends Model {

  static $tableName = 'user_userPermission';

  static $cols = array(
    /*'id' => array(
      'type' => 'INT',
      'primarykey' => true
    ),*/
    'user' => array(
      'type'=>'FOREIGN',
      'vo' => 'UserModel',
      'key' => 'id',
      'primarykey' => true
    ),
    'permission' => array(
      'type' => 'CHAR',
      'size' => '100',
    )

  );

  static $extraFilters = array();

  var $notCreateDBTable = true;

  var $rcSQL = '
    DROP VIEW IF EXISTS user_userPermission;
    CREATE VIEW user_userPermission AS
      SELECT DISTINCT
        user_userRole.user AS user,
        user_rolePermission.permission AS permission
      FROM `user_userRole`
        JOIN `user_rolePermission`
      WHERE user_rolePermission.role = user_userRole.role
      ;
  ';


  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }
}
