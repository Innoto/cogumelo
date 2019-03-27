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
      'type' => 'FLOAT'
    ),
    'deployVersion' => array(
      'type' => 'FLOAT'
    ),
    'executedRcDeploy' => array(
      'type' => 'BOOLEAN'
    )
  );



  static $extraFilters = array(
    'searchByName'=> ' model_registers.name = ? '
  );




  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }
}
