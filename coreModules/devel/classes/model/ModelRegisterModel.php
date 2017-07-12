<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');


class ModelRegisterModel extends Model {

  static $tableName = 'model_registers';

  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'name' => array(
      'type' => 'VARCHAR',
      'unique' => true,
      'size' => 100
    ),
    'firstVersion' => array(
      'type' => 'VARCHAR',
      'size' => 100
    ),
    'deployVersion' => array(
      'type' => 'VARCHAR',
      'size' => 100
    )
  );



  static $extraFilters = array(
    'searchByName'=> ' name = ? '
  );

  static $MigrateSQLChangeColumns = "ALTER TABLE model_registers MODIFY COLUMN firstVersion VARCHAR(100);ALTER TABLE model_registers MODIFY COLUMN deployVersion VARCHAR(100); ";


  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }
}
