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
/*      if( $evo->rcSQL ){
        $aditionalRcSQL .= "\n# Aditional rcSQL for ".$voKey.".php\n";
        $aditionalRcSQL .= $evo->rcSQL;
      }*/
      $aditionalRcSQL .= $this->getModelDeploySQL($voKey, $evo, true); // get deploys that specify model


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

  public function deployModels() {
    $returnStrArray = array();
    $aditionalRcSQL = '';

    foreach( VOUtils::listVOs() as $voKey => $vo ) {

      $evo = new $voKey();

      // deploy SQL
/*      if( sizeof( $evo->deploySQL ) > 0 ){
        $aditionalRcSQL .= "\n# deploy SQL for ".$voKey.".php\n";
        //$aditionalRcSQL .= $evo->rcSQL;
        var_dump($evo->deploySQL);
      }*/
      $aditionalRcSQL .= $this->getModelDeploySQL($voKey, $evo);

    }

    // add all rc custom SQL at bottom
    if( $aditionalRcSQL !== '' ) {
      $returnStrArray[] = $this->data->aditionalExec( $aditionalRcSQL );
    }

    return $returnStrArray;
  }


  private function getModelDeploySQL( $modelName, $model, $getGenerateModelSQL = false ) {
    $retSQL = '';

    if( sizeof( $model->deploySQL ) > 0 ){
      $retSQL .= "\n# deploy SQL for ".$modelName.".php\n";


      foreach( $model->deploySQL as $dKey => $d) {
        if($getGenerateModelSQL === true && isset($d['executeOnGenerateModelToo']) && $d['executeOnGenerateModelToo'] === true) {
          // GENERATEMODEL
          $retSQL .= $d['sql'];
        }
        else {
          // DEPLOY

        }
      }
    }
    return $retSQL;
  }


  public function getTablesSQL() {
    $returnStrArray = array();
    $aditionalRcSQL = '';

    foreach( VOUtils::listVOs() as $voKey => $vo ) {

      $evo = new $voKey();

      // rc custom SQL
/*      if( $evo->rcSQL ){
        $aditionalRcSQL .= "\n# Aditional rcSQL for ".$voKey.".php\n";
        $aditionalRcSQL .= $evo->rcSQL;
      }*/
      $aditionalRcSQL .= $this->getModelDeploySQL($voKey, $evo, true); // get deploys that specify model

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
