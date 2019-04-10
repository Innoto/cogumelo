<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');

class FilegroupModel extends Model {
  static $tableName = 'filedata_filedatagroup';

  static $cols = [
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
  ];


  static $extraFilters = [
    'idGroupIn' => ' filedata_filedatagroup.idGroup IN (?) ',
    'idGroupNotIn' => ' filedata_filedatagroup.idGroup NOT IN (?) ',
  ];


  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }

  public function garbageCollector() {
    Cogumelo::debug( __METHOD__ );

    Cogumelo::load( 'coreModel/VOUtils.php' );

    // $idsInUse = VOUtils::getIdsInUse( get_class($this) );

    // $listModel = $this->listItems( array( 'filters' => array( 'notInId' => $idsInUse ) ) );
    // while( $objElem = $listModel->fetch() ) {
    //   echo "Borramos obj. id:".$objElem->getter('id')."\n";
    //   $objElem->delete();
    // }
  }
}