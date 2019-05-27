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
      'type' => 'INT',
      'index' => true
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


  var $deploySQL = [
    [
      'version' => 'filedata#3',
      'sql'=> '
        CREATE INDEX idx_filedata_filedatagroup_idGroup
        ON filedata_filedatagroup (idGroup)
        ALGORITHM DEFAULT
        LOCK DEFAULT;
      '
    ],
  ];



  public function __construct( $datarray = [], $otherRelObj = false ) {
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