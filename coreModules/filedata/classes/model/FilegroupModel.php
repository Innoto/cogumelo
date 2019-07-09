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


  public function getFiledataIds( $idGroups ) {
    $filedataIds = [];
    $filegroupList = false;

    if( !empty( $idGroups ) ) {
      if( is_array( $idGroups ) ) {
        $filegroupList = $this->listItems( [ 'filters' => [ 'idGroupIn' => $idGroups ] ] );
      }
      else {
        $filegroupList = $this->listItems( [ 'filters' => [ 'id' => $idGroups ] ] );
      }

      if( is_object( $filegroupList ) ) {
        while( $filegroupObj = $filegroupList->fetch()  ) {
          $filedataIds[] = $filegroupObj->getter('filedataId');
        }
      }
    }

    return( $filedataIds );
  }


  /**
   * Delete items by ids
   *
   * @param array $idGroups array of filters
   */
  public function deleteById( $idGroups ) {
    $filegroupList = false;

    if( !empty( $idGroups ) ) {
      if( is_array( $idGroups ) ) {
        $filegroupList = $this->listItems( [ 'filters' => [ 'idGroupIn' => $idGroups ], 'groupBy' => 'idGroup' ] );
      }
      else {
        $filegroupList = $this->listItems( [ 'filters' => [ 'id' => $idGroups ], 'groupBy' => 'idGroup' ] );
      }

      // $deleted = [];
      if( is_object( $filegroupList ) ) {
        while( $filegroupObj = $filegroupList->fetch()  ) {
          error_log( __METHOD__.' Vamos a eliminar idGroup:'.$filegroupObj->getter('idGroup') );
          $filegroupObj->delete();
        }
      }
    }
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