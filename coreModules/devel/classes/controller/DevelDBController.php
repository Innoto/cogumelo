<?php

Cogumelo::load('coreModel/VOUtils.php');
Cogumelo::load('coreModel/Facade.php');

//
// DevelUtilsDB Controller Class
//
class  DevelDBController
{
  var $data;
  var $voUtilControl;

  public function __construct( $usuario = false, $password = false, $DB = false ) {
    $this->data = new Facade(false, "DevelDB", "devel");

    if($usuario) {
      $this->data->develMode($usuario, $password, $DB);
    }
    else {
      $this->data->getConnection();
    }
  }


  public function createTables() {

    $returnStrArray = array();
    $aditionalRcSQL = '';
    foreach( VOUtils::listVOs() as $voKey => $vo ) {

      $evo = new $voKey();

      // rc custom SQL
      if( $evo->rcSQL ){
        $aditionalRcSQL .= "\n# Aditional rcSQL for ".$voKey.".php\n";
        $aditionalRcSQL .= $evo->rcSQL;
      }

      if( !$evo->notCreateDBTable ) {
        $returnStrArray[] = $this->data->dropTable($voKey);
        $returnStrArray[] = $this->data->createTable($voKey);
        $returnStrArray[] = $this->data->insertTableValues($voKey);
      }
    }

    // add all rc custom SQL at bottom
    if( $aditionalRcSQL !== '' ) {
      $returnStrArray[] = $this->data->aditionalExec( $aditionalRcSQL );
    }

    return $returnStrArray;
  }


  public function getTablesSQL() {
    $returnStrArray = array();
    $aditionalRcSQL = '';

    foreach( VOUtils::listVOs() as $voKey => $vo ) {

      $evo = new $voKey();

      // rc custom SQL
      if( $evo->rcSQL ){
        $aditionalRcSQL .= "\n# Aditional rcSQL for ".$voKey.".php\n";
        $aditionalRcSQL .= $evo->rcSQL;
      }

      // tables Creation
      if( !$evo->notCreateDBTable ) {
        $returnStrArray[] = "#VO File: ".$vo['path'].$voKey.".php";
        $returnStrArray[] = $this->data->getDropSQL( $voKey, $vo['path'].$voKey.".php" );
        $returnStrArray[] = $this->data->getTableSQL( $voKey, $vo['path'].$voKey.".php");

        $resInsert = $this->data->getInsertTableSQL( $voKey, $vo['path'].$voKey.".php");

        if(!empty($resInsert)) {
          foreach( $resInsert as $resInsertKey => $resInsertValue ) {
            $returnStrArray[] = $resInsertValue['infoSQL'];
          }
        }
      }

    }

    // add all rc custom SQL at bottom
    $returnStrArray[] = $aditionalRcSQL;

    return $returnStrArray;
  }

  public function createSchemaDB() {
    return $this->data->createSchemaDB();
  }

}
