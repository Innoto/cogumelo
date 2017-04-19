<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');

define("ROLE_SUPERADMIN", "10");
define("ROLE_USER", "11");

class RoleModel extends Model {
  static $tableName = 'user_role';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'name' => array(
      'type' => 'CHAR',
      'size' => '30'
    ),
    'description'=> array(
      'type' => 'TEXT',
      'size' => '300'
    )
  );

  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct($datarray, $otherRelObj );
  }

}
