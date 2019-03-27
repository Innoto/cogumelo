<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');


class ModuleRegisterModel extends Model {

  static $tableName = 'module_registers';

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
      'type' => 'FLOAT'
    ),
    'deployVersion' => array(
      'type' => 'FLOAT'
    )
  );

  static $extraFilters = array(
    'searchByName'=> ' module_registers.name = ? '
  );

  static $MigrateSQLChangeColumns = "ALTER TABLE model_registers MODIFY COLUMN firstVersion VARCHAR(100);ALTER TABLE model_registers MODIFY COLUMN deployVersion VARCHAR(100); ";


  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }


}
