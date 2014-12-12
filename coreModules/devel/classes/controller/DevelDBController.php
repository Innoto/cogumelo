<?php

Cogumelo::load('coreController/DataController.php');
Cogumelo::load('coreModel/VOUtils.php');

//
// DevelUtilsDB Controller Class
//
class  DevelDBController extends DataController
{
  var $data;
  var $voUtils;

  function __construct($usuario=false, $password = false, $DB = false)
  {
    $this->voUtils = new VOUtils();
    $this->data = new Facade("DevelDB", "devel");

    if($usuario) {
      $this->data->develMode($usuario, $password, $DB);
    }
  }


  function createTables(){

    $returnStrArray = array();
    foreach( $this->voUtils->listVOs() as $vo) {
      $returnStrArray[] = $this->data->dropTable($vo);
      $returnStrArray[] = $this->data->createTable($vo);
      $returnStrArray[] = $this->data->insertTableValues($vo);
    }

    return $returnStrArray;
  }


  function getTablesSQL(){
    $returnStrArray = array();

    foreach( $this->voUtils->listVOs() as $vo) {
      $returnStrArray[] = "#VO File: ".$this->voUtils->voReferences[$vo].$vo.".php";
      $returnStrArray[] = $this->data->getDropSQL($vo, $this->voUtils->voReferences[$vo]);
      $returnStrArray[] = $this->data->getTableSQL($vo, $this->voUtils->voReferences[$vo].$vo.".php");

      $resInsert = $this->data->getInsertTableSQL($vo, $this->voUtils->voReferences[$vo].$vo.".php");

      if(!empty($resInsert)) {
        foreach ($resInsert as $resInsertKey => $resInsertValue) {
          $returnStrArray[] = $resInsertValue['infoSQL'];
        }
      }

    }

    return $returnStrArray;
  }

  function createSchemaDB() {
    return $this->data->createSchemaDB();
  }

}
