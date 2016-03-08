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

      $aditionalRcSQL .= $this->getModelDeploySQL($voKey, $evo);

    }

    // add all rc custom SQL at bottom
    if( $aditionalRcSQL !== '' ) {
      $returnStrArray[] = $this->data->aditionalExec( $aditionalRcSQL );
    }

    foreach(explode( "\n", $aditionalRcSQL ) as $dLine ) {
      Cogumelo::log( $dLine ,'deploy');
    }



    return $returnStrArray;
  }


  private function getModelDeploySQL( $modelName, $model, $getOnlyGenerateModelSQL = false ) {
    $retSQL = '';

    if( sizeof( $model->deploySQL ) > 0 ){
      $retSQL .= "\n## Deploy SQL for ".$modelName.".php\n";


      foreach( $model->deploySQL as $dKey => $d) {
        if($getOnlyGenerateModelSQL === true && isset($d['executeOnGenerateModelToo']) && $d['executeOnGenerateModelToo'] === true) {
          // GENERATEMODEL
          $retSQL .= $d['sql'];
        }
        else {
          // DEPLOY
          if( preg_match( '#^(.*)\#(\d{1,10}(.\d{1,10})?)#', $dKey, $matches ) ) {
            $deployModuleName = $matches[1];

            eval( '$currentModuleVersion = (float) (new '.$deployModuleName.')->version;' );
            eval( '$registeredModuleVersion = (float) '.$deployModuleName.'::checkRegisteredVersion();' );

            $deployModuleVersion = (float) $matches[2];

            if( class_exists( $deployModuleName ) ) {

              //echo( '$registeredVersion = (float) '.$moduleName.'::checkRegisteredVersion();' );
              //eval( '$currentModuleVersion = (float) '.$deployModuleName.'::v();' );
              //echo "VERSION:".$deployModuleVersion." - ".$registeredVersion;

              if( $deployModuleVersion > $registeredModuleVersion  &&  $deployModuleVersion <= $currentModuleVersion && isset($d['sql']) ) {
                $retSQL .= "# Module $deployModuleName deploy code from versions: ( $registeredModuleVersion ) to ( $currentModuleVersion ) \n";
                $retSQL .= $d['sql'];

              }
            }

          }
        }
      }
    }

    return $retSQL;
  }


  public function getDeploysSQL() {
    $sqlStr = '';

    global $C_ENABLED_MODULES;

    foreach( $C_ENABLED_MODULES as $moduleName ) {
      require_once( ModuleController::getRealFilePath( $moduleName.'.php' , $moduleName) );
    }

    foreach( VOUtils::listVOs() as $voKey => $vo ) {

      $evo = new $voKey();

      $sqlStr .= $this->getModelDeploySQL($voKey, $evo);

    }


    return $sqlStr;
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
