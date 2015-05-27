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
      'type' => 'CHAR',
      'size' => '250'
    ),
    'originalName'=> array(
      'type' => 'CHAR',
      'size' => '250'
    ),
    'absLocation'=> array(
      'type' => 'CHAR',
      'size' => '250'
    ),
    'type'=> array(
      'type' => 'CHAR',
      'size' => '60'
    ),
    'size'=> array(
      'type' => 'BIGINT'
    )
  );

  static $extraFilters = array();

  function __construct($datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }

}