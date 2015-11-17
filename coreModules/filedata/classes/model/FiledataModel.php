<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');

class FiledataModel extends Model
{
  static $tableName = 'filedata_filedata';

  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'name' => array(
      'type' => 'VARCHAR',
      'size' => 250
    ),
    'originalName'=> array(
      'type' => 'VARCHAR',
      'size' => 250
    ),
    'absLocation'=> array(
      'type' => 'VARCHAR',
      'size' => 2000
    ),
    'type'=> array(
      'type' => 'VARCHAR',
      'size' => 60
    ),
    'size'=> array(
      'type' => 'BIGINT'
    ),
    'title' => array(
      'type' => 'VARCHAR',
      'size' => 150,
      'multilang' => true
    )
  );

  static $extraFilters = array();

  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }

}
