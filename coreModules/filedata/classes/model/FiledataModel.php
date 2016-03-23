<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');

class FiledataModel extends Model {
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

  static $extraFilters = array(
    'notInId' => ' filedata_filedata.id NOT IN (?) '
  );

  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }

  public function garbageCollector() {
    Cogumelo::load( 'coreModel/VOUtils.php' );

    $idsInUse = VOUtils::getIdsInUse( get_class($this) );

    $listModel = $this->listItems( array( 'filters' => array( 'notInId' => $idsInUse ) ) );
    while( $objElem = $listModel->fetch() ) {
      echo "Borramos obj. id:".$objElem->getter('id')."\n";
      $objElem->delete();
    }
  }


  /**
   * Delete item (This method is a mod from Model::delete)
   *
   * @param array $parameters array of filters
   *
   * @return boolean
   */
  public function delete( array $parameters = array() ) {
    // Eliminamos ficheros en disco
    filedata::load('controller/FiledataController.php');
    $filedataCtrl = new FiledataController();
    $filedataCtrl->removeServerFiles( $this );

    // Eliminamos el objeto con el delete original
    Cogumelo::debug( 'Called custom delete on '.get_called_class().' with "'.
      $this->getFirstPrimarykeyId().'" = '. $this->getter( $this->getFirstPrimarykeyId() ) );
    $this->dataFacade->deleteFromKey( $this->getFirstPrimarykeyId(), $this->getter( $this->getFirstPrimarykeyId() ) );
  }
}
