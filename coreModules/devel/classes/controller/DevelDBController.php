<?php

Cogumelo::load('coreController/DataController.php');


//
// DevelUtilsDB Controller Class
//
class  DevelDBController extends DataController
{
  var $data;

  var $voReferences = array();


  function __construct($usuario=false, $password = false, $DB = false)
  {
    $this->data = new Facade("DevelDB", "devel");

    if($usuario) {
      $this->data->develMode($usuario, $password, $DB);
    }
  }


  function createTables(){

    $returnStrArray = array();
    foreach($this->listVOs() as $vo) {
      $returnStrArray[] = $this->data->dropTable($vo);
      $returnStrArray[] = $this->data->createTable($vo);
      $returnStrArray[] = $this->data->insertTableValues($vo);
    }

    return $returnStrArray;
  }


  function getTablesSQL(){
    $returnStrArray = array();

    foreach($this->listVOs() as $vo) {
      $returnStrArray[] = "#VO File: ".$this->voReferences[$vo].$vo.".php";
      $returnStrArray[] = $this->data->getDropSQL($vo, $this->voReferences[$vo]);
      $returnStrArray[] = $this->data->getTableSQL($vo, $this->voReferences[$vo].$vo.".php");

      $resInsert = $this->data->getInsertTableSQL($vo, $this->voReferences[$vo].$vo.".php");

      if(!empty($resInsert)) {
        foreach ($resInsert as $resInsertKey => $resInsertValue) {
          $returnStrArray[] = $resInsertValue['infoSQL'];
        }
      }

    }

    return $returnStrArray;
  }


  // list VOs with priority
  function listVOs() {
    $voarray = array();

    // VOs into APP
    $voarray = array_merge($voarray, $this->scanVOs( SITE_PATH.'classes/model/') ) ; // scan app model dir

    global $C_ENABLED_MODULES;
    foreach($C_ENABLED_MODULES as $modulename) {
      // modules into APP
      $voarray = array_merge($voarray, $this->scanVOs( SITE_PATH.'../modules/'.$modulename.'/classes/model/'));
      // modules into DIST
      $voarray = array_merge($voarray, $this->scanVOs( COGUMELO_LOCATION.'/distModules/'.$modulename.'/classes/model/'));
      // modules into COGUMELO 
      $voarray = array_merge($voarray, $this->scanVOs( COGUMELO_LOCATION.'/coreModules/'.$modulename.'/classes/model/'));
    }

    return array_unique($voarray);
  }

  function scanVOs($dir) {
    //cogumelo::debug($dir);
    $vos = array();


    if(!file_exists($dir))
      return $vos;

    // VO's from APP
    if ($handle = opendir( $dir )) {
      while (false !== ($file = readdir($handle))) {
          if ($file != "." && $file != "..") {
            if(substr($file, -6) == 'VO.php'){
              $class_vo_name = substr($file, 0,-4);

              // prevent reload an existing vo in other place
              if (!array_key_exists( $class_vo_name, $this->voReferences )) {
                  require_once($dir.$file);
                  $vos[] =  $class_vo_name;
                  $this->voReferences[$class_vo_name] = $dir;
                }
              }
          }
      }
      closedir($handle);
    }


    return $vos;
  }

  function createSchemaDB() {
    return $this->data->createSchemaDB();
  }

}
