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
//      $aditionalRcSQL .= $this->getModelDeploySQL($voKey, $evo, true); // get deploys that specify model


      if( !$evo->notCreateDBTable ) {
        $returnStrArray[] = $this->data->dropTable($voKey);
        $returnStrArray[] = $this->data->createTable($voKey);
        $returnStrArray[] = $this->data->insertTableValues($voKey);
      }
    }

    // add all rc custom SQL at bottom
    if( $aditionalRcSQL !== '' ) {
      //$returnStrArray[] = $this->data->aditionalExec( $aditionalRcSQL );
    }

    return $returnStrArray;
  }

  public function deployModels(  $getOnlyGenerateModelSQL = false ) {
    $returnStrArray = array();
    //$aditionalRcSQL = '';

    foreach( VOUtils::listVOs() as $voKey => $vo ) {

      $evo = new $voKey();

      $aditionalRcSQL = $this->getModelDeploySQL($voKey, $evo, $getOnlyGenerateModelSQL);
      if( $aditionalRcSQL !== '' ) {
        echo "\nDeploy model for " . $evo->name. "\n";
        $this->data->aditionalExec( $aditionalRcSQL );
        Cogumelo::log( $aditionalRcSQL ,'cogumelo_deploy');
      }
    }

    // add all rc custom SQL at bottom
    if( $aditionalRcSQL !== '' ) {
      //$returnStrArray[] = $this->data->aditionalExec( $aditionalRcSQL );
    }
/*
    foreach(explode( "\n", $aditionalRcSQL ) as $dLine ) {
      Cogumelo::log( $dLine ,'cogumelo_deploy');
    }
*/


    return $returnStrArray;
  }


  private function getModelDeploySQL( $modelName, $model, $getOnlyGenerateModelSQL = false ) {
    $retSQL = '';



    if( sizeof( $model->deploySQL ) > 0 ){
      $retSQL .= "\n## Deploy SQL for ".$modelName.".php\n";


      foreach( $model->deploySQL as $d) {

        $sqlToExecute = $this->renderRichSql( $d['sql'] );

        if($getOnlyGenerateModelSQL === true ) {
          if( isset($d['executeOnGenerateModelToo']) && $d['executeOnGenerateModelToo'] === true ) {
            // GENERATEMODEL
            $retSQL .= $sqlToExecute;
          }
        }
        else {
          // DEPLOY
          if( preg_match( '#^(.*)\#(\d{1,10}(.\d{1,10})?)#', $d['version'], $matches ) ) {
            $deployModuleName = $matches[1];

            eval( '$currentModuleVersion = (float) '.$deployModuleName.'::checkCurrentVersion();' );
            eval( '$registeredModuleVersion = (float) '.$deployModuleName.'::checkRegisteredVersion();' );

//var_dump(array($currentModuleVersion ,$registeredModuleVersion ))

            $deployModuleVersion = (float) $matches[2];

            if( class_exists( $deployModuleName ) ) {

              //echo( '$registeredVersion = (float) '.$moduleName.'::checkRegisteredVersion();' );
              //eval( '$currentModuleVersion = (float) '.$deployModuleName.'::v();' );
              //echo "VERSION:".$deployModuleVersion." - ".$registeredVersion;

              if(
                $deployModuleVersion > $registeredModuleVersion  &&
                $deployModuleVersion <= $currentModuleVersion &&
                isset($d['sql'])
              ) {
                //var_dump( $deployModuleVersion );
                //var_dump( $currentModuleVersion );

                $retSQL .= "# Module $deployModuleName deploy code from versions: ( $registeredModuleVersion ) to ( $currentModuleVersion ) \n";
                $retSQL .= $sqlToExecute;

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


  public function renderRichSql( $sql ) {


    // Multilang expression
    preg_match_all( "#[\{]\s*multilang\s*\:\s*(.*?)\s*[\}]#", $sql, $matches);

    if( count($matches[0]) ) {
      for($mi=0; count($matches[0]) > $mi; $mi++ ) {

        foreach( array_keys( cogumeloGetSetupValue( 'lang:available')) as $lang  ){
          $multilangLines .= str_replace('$lang', $lang, $matches[1][$mi]);
        }

        $sql = str_replace($matches[0][$mi], $multilangLines, $sql);
      }
    }

    //$debug = var_export($matches, true);

    return $sql;
  }

}
