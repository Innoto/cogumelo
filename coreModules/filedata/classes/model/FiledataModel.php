<?php


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
      'name' => 'Name',
      'type' => 'CHAR',
      'size' => '250'
    ),
    'originalName'=> array(
      'name' => 'Nombre original',
      'type' => 'CHAR',
      'size' => '250'
    ),
    'absLocation'=> array(
      'name' => 'Nombre original',
      'type' => 'CHAR',
      'size' => '250'
    ),
    'type'=> array(
      'name' => 'Tipo',
      'type' => 'CHAR',
      'size' => '60'
    ),
    'size'=> array(
      'name' => 'Tamaño',
      'type' => 'BIGINT'
    )
  );

  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}