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

  function __construct($usuario=false, $password = false, $DB = false)
  {
    $this->data = new Facade(false, "DevelDB", "devel");

    if($usuario) {
      $this->data->develMode($usuario, $password, $DB);
    }
  }


  function createTables(){

    $returnStrArray = array();
    foreach( VOUtils::listVOs() as $vo) {
      $returnStrArray[] = $this->data->dropTable($vo);
      $returnStrArray[] = $this->data->createTable($vo);
      $returnStrArray[] = $this->data->insertTableValues($vo);
    }

    return $returnStrArray;
  }


  function getTablesSQL(){
    $returnStrArray = array();

    foreach( VOUtils::listVOs() as $voKey => $vo) {
      $returnStrArray[] = "#VO File: ".$vo['path'].".php";
      $returnStrArray[] = $this->data->getDropSQL( $voKey, $vo['path'].".php" );
      $returnStrArray[] = $this->data->getTableSQL( $voKey, $vo['path'].".php");

      $resInsert = $this->data->getInsertTableSQL( $voKey, $vo['path'].".php");

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
