<?php


Cogumelo::load('coreModel/Model.php');

class FilegroupModel extends Model
{
  static $tableName = 'filedata_filedata';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'idGroup' => array(
      'type' => 'INT'
    ),    
    'filedataId' => array(
      'type'=>'FOREIGN',
      'vo' => 'FiledataModel',
      'key' => 'id'
    ),    
  );

  function __construct($datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }

}