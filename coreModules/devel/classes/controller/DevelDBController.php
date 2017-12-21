<?php

Cogumelo::load('coreModel/VOUtils.php');
Cogumelo::load('coreModel/Facade.php');

//
// DevelUtilsDB Controller Class
//
class  DevelDBController {

  var $data;
  var $voUtilControl;
  var $noExecute = true;

  public function __construct( $usuario = false, $password = false, $DB = false ) {
    $this->data = new Facade(false, "DevelDB", "devel");

    if($usuario) {
      $this->data->develMode($usuario, $password, $DB);
    }
    else {
      $this->data->getConnection();
    }
  }

  private function setNoExecutionMode($noExecute = true) {
    $this->noExecute = $noExecute;
  }

  public function scriptGenerateModel() {
    $this->setNoExecutionMode(false);
    $this->dropAllTables();

    $this->VOcreateTable( get_class(new ModelRegisterModel()) );
    $this->VOcreateTable( get_class(new ModuleRegisterModel()) );

    $this->deploy();
  }

  public function scriptDeploy() {

    $this->setNoExecutionMode(false);

    devel::load('model/ModelRegisterModel.php');

    if( $this->VOTableExist( new ModelRegisterModel() ) === false ) {
      echo "\n\nMIGRATING FROM OLD DEPLOYING SYS...\n";

      $this->VOcreateTable( get_class(new ModelRegisterModel()) );
      //forzar actualizar todas as versións de model
      $modules = $this->getModules();
      foreach( $modules as $module ) {
        foreach( $this->getModelsInModule($module) as $model=>$modelRef ) {
          $this->registerModelVersion( $model , (new $module)->version );
        }
      }
      echo "\nNow you can enjoy new deploy system\n";
    }
    else {

      $this->deploy();
    }
  }



  public function simulationCreateTablesOnView( ) {
    $ctA = [];
    $moduleDeploysCreateTable = [];

    $modules = $this->getModules();


    foreach( $modules as $module ) {

      foreach( $this->getModelsInModule($module) as $model=>$modelRef ) {

        $nct = ( (new $model() )->notCreateDBTable !== null )?(new $model())->notCreateDBTable : false;
        if( $nct !== true ) {
          $moduleDeploysCreateTable = array_merge($moduleDeploysCreateTable, $this->VOgetCreateTableAsdeploy($model, $module) );
        }

      }

    }

    foreach( $modules as $module ) {
      //deploy de modelos
      foreach( $this->getModelsInModule($module) as $model=>$modelRef ) {
        $moduleDeploysCreateTable = array_merge($moduleDeploysCreateTable, $this->VOgetDeploys( $model, ['onlyRC'=>true] ) );
      }
    }


    if(sizeof($moduleDeploysCreateTable)>0) {
      foreach($moduleDeploysCreateTable as $d) {
        $ctA[] = "/*MODEL ".$d['voName']."  */\n".$d['sql'];
      }
    }


    return $ctA;
  }



  public function deploy( ) {
    $modules = $this->getModules();
    $modules[] = 'Cogumelo';

    // create tables
    foreach( $modules as $module ) {
      $moduleDeploysCreateTable = [];
      foreach( $this->getModelsInModule($module) as $model=>$modelRef ) {

        $modelCurrentVersion = $this->VOIsRegistered( $model );
        if( $modelCurrentVersion === false ) {
          $nct = ( (new $model() )->notCreateDBTable !== null )?(new $model())->notCreateDBTable : false;
          if( $nct !== true ) {
            $moduleDeploysCreateTable = array_merge($moduleDeploysCreateTable, $this->VOgetCreateTableAsdeploy($model, $module) );
          }
        }
      }
      if(sizeof($moduleDeploysCreateTable)>0) {
        echo "\n /*Creating tables in module '".$module."'*/\n";
        $this->executeDeployList( $moduleDeploysCreateTable , $module );
      }
    }



    // deploys
    foreach( $modules as $module ) {
      $moduleDeploys = [];

      //deploy de modelos
      foreach( $this->getModelsInModule($module) as $model=>$modelRef ) {

        $modelCurrentVersion = $this->VOIsRegistered( $model );
        if($module == 'app') {
          $moduleObj = Cogumelo::get();
          $toVersion = ( isset( $moduleObj::$version ) )? $moduleObj::$version : false;
        }
        else {
          $moduleObj = new $module();
          $toVersion = ( isset( $moduleObj->version ) )? $moduleObj->version : false;
        }


        //echo $model.' = '.$modelCurrentVersion."\n";

        if( $modelCurrentVersion ) {
          // deploy modelo
          $voDeploys = $this->VOgetDeploys(
            $model,
            [
              'from'=> $modelCurrentVersion ,//última versión rexistrada do modulo,
              'to'=> $toVersion //versión actual do módulo en código
            ]
          );

          var_dump([
            'model'=>$model,
            'from'=> $modelCurrentVersion ,//última versión rexistrada do modulo,
            'to'=> $toVersion //versión actual do módulo en código
          ]);

          if( sizeof($voDeploys) > 0 ) {
            echo "\n/*Getting ".sizeof($voDeploys)." deploys in'".$model."'*/";
            $moduleDeploys = array_merge( $moduleDeploys, $voDeploys);
          }



        }

        if( $this->VoIsExecutedRC( $model ) === false ) {
          echo "\n/*Getting RC deploys for'".$model."'*/";
          $moduleDeploys = array_merge($moduleDeploys, $this->VOgetDeploys( $model, ['onlyRC'=>true] ) );


        }


      }




      //
      // deploy dos modelos do módulo, en orden de versión
      //echo "\nEXEC DEPLOY CODES in module $module ";
      $deployWorks = $this->executeDeployList(
        $this->orderDeploysByVersion( $moduleDeploys ), $module
      );



      if($this->noExecute === false) {
        //
        // deploy do módulo
        if( $deployWorks === true ) {

          if( $this->moduleIsRegistered( $module ) === false ) {
            $this->execModuleRC( $module );
            $this->execModuleDeploy($module, true);
          }
          else {
            $this->execModuleDeploy($module, false);
          }


          $this->registerModuleVersion($module);
        }
        else {
          echo "\n!!!! FAILURE: Please, check your deploys in module '$module' before next execution\n\n";
          exit;

        }

      }

    }

  }




  public function VOTableExist( $voObj ) {
    $ret = true;
    $tER = $this->data->checkTableExist( $voObj );

    if( $tER  === COGUMELO_ERROR ) {
      $ret = false;
    }

    return $ret;
  }


  public function VOIsRegistered( $voClass ) {
    $ret = false;

    $modelReg = new ModelRegisterModel();

    $v = $modelReg->listItems( ['filters'=>['searchByName'=> $voClass ] ]);
    if( $regInfo=$v->fetch() ) {

        $ret = $regInfo->getter('deployVersion');

    }

    return $ret;
  }

  function VoIsExecutedRC($voClass) {
    $ret = false;

    $modelReg = new ModelRegisterModel();

    $v = $modelReg->listItems( ['filters'=>['searchByName'=> $voClass ] ]);
    if( $regInfo=$v->fetch() ) {

        $ret = $regInfo->getter('executedRcDeploy');

    }

    return $ret;
  }



  public function VOgetCreateTableAsdeploy( $voKey, $module ) {
    return [[ 'version' => (new $module())->version, 'sql'=> $this->data->getTableSQL($voKey),  'voName'=>$voKey]];
  }


  public function VOcreateTable( $voClass ) {
    $this->data->createTable( $voClass, $this->noExecute );
  }

  private function VOdropTable( $voKey ) {

    $voObj = new $voKey();
    if( !isset($voObj->notCreateDBTable) || $voObj->notCreateDBTable !== true){
      echo "\n Drop table ".$voKey. "";
      $this->data->dropTable( $voKey, $this->noExecute );
    }
  }

  public function VOgetDeploys( $voKey, $paramFilters = [] ) {

    $deploys = [];

    $f =  [
      'onlyRC' => false,
      'from' => false, // get from version
      'to' => false // get To version
    ];

    $filters = array_merge( $f, $paramFilters);

    $vo = new $voKey();

    if( count( $vo->deploySQL ) > 0 ){


      if( $filters['onlyRC'] == true  ){
        foreach( $vo->deploySQL as $deployElement) {

          // RC
          if( isset($deployElement['executeOnGenerateModelToo']) && $deployElement['executeOnGenerateModelToo']=== true ) {

            if( isset($deployElement['version'])) {
              $deployElement['version'] = $this->getOnlyVersionFromVersionString( $deployElement['version'] );
            }

            $deployElement['sql'] = $this->renderRichSql( $deployElement['sql'] );
            $deployElement['voName'] = $voKey;
            array_push( $deploys, $deployElement );
          }
        }
      }
      else {
        foreach( $vo->deploySQL as $deployElement ) {
          if( isset($deployElement['version'])) {
            $deployElement['version'] = $this->getOnlyVersionFromVersionString( $deployElement['version'] );
          }
          if( isset($filters['from'])) {
            $filters['from'] =  round((float) $filters['from'],2);
          }
          if( isset($filters['to'])) {
            $filters['to'] = (float) $filters['to'];
          }



          if(


            ($filters['from'] < $deployElement['version']) && ($deployElement['version'] <= $filters['to'])

          ) {



            $deployElement['sql'] = $this->renderRichSql( $deployElement['sql'] );
            $deployElement['voName'] = $voKey;
            array_push( $deploys, $deployElement );
          }




        }

      }


    }


    // return
    return $deploys;
  }



  public function getModules( ) {

    global $C_ENABLED_MODULES;
    $retModules = [];
    foreach( $C_ENABLED_MODULES as $moduleName ) {
      if( $moduleName != 'devel' ) {
        require_once( ModuleController::getRealFilePath( $moduleName.'.php' , $moduleName) );
        eval('$retModules[] = "' . $moduleName .'";');
      }
    }

    array_push($retModules, 'app');

    return $retModules;
  }



  public function getModelsInModule( $module ) {

    $retArray = [];
    if( $module !== 'devel') {
      $retArray = VOUtils::listVOsByModule( $module );
    }

    return $retArray;
  }



  public function dropAllTables() {
    $modulos = $this->getModules();

    foreach( $modulos as $modulo ) {
      $models = $this->getModelsInModule($modulo);
       if( sizeof($models)>0 ) {
         foreach($models as $voKey=>$vo) {
           $this->VOdropTable( get_class(new $voKey()) );
         }
       }

    }

    $this->VOdropTable( get_class(new ModelRegisterModel()) );
    $this->VOdropTable( get_class(new ModuleRegisterModel()) );

  }


  private function executeDeployList( $deployArrays, $module ) {
    $ret  = true;
    if( sizeof($deployArrays)>0 ) {
      foreach ( $deployArrays as $deploy ) {

        $exec = $this->data->aditionalExec( $deploy['sql'], $this->noExecute  );



        if( $exec !== COGUMELO_ERROR ) {
          //  update model version

          if( isset($deploy['executeOnGenerateModelToo']) && $deploy['executeOnGenerateModelToo']=== true ) {
            $isRCDeploy = true;
          }
          else {
            $isRCDeploy = false;
          }
          // register current deploy version
          $this->registerModelVersion( $deploy['voName'] ,$deploy['version'], $isRCDeploy );

        }
        else {
          echo "\n ---- Deploy FAIL in ".$deploy['voName']." - ".$deploy['version']." ---- \n";
          $ret = false;
          break;
        }



      }
    }

    return $ret;
  }


  private function orderDeploysByVersion( $deploys ) {
    $retDeploys = [];

    while( sizeof($deploys) > 0 ) {
      //firt element
      foreach ($deploys as $lowerKey => $lowerVal) break;
      ////
      foreach( $deploys as $dK=>$d ) {

        // $lowerVal['version'] lower than $d['version']
        if( $lowerVal['version'] < $d['version'] ) {
          $lowerKey = $dK;
          $lowerVal = $d;
        }
      }

      array_push( $retDeploys, $lowerVal );
      unset( $deploys[$lowerKey] );
    }

    return $retDeploys;
  }



  private function registerModelVersion( $voKey, $version, $isRCDeploy= false ) {



    $modelReg = new ModelRegisterModel();
    $v = $modelReg->listItems( ['filters'=>['name'=> $voKey ] ]);

    if( $this->noExecute !== true) {
      if( $regInfo=$v->fetch() ) {

        $regInfo->setter( 'executedRcDeploy', $isRCDeploy );
        $regInfo->setter( 'deployVersion', $version );
        $regInfo->save();
      }
      else {
        $regInfo = new ModelRegisterModel();
        $regInfo->setter('name', $voKey);
        $regInfo->setter( 'executedRcDeploy', $isRCDeploy );
        $regInfo->setter('firstVersion', $version);
        $regInfo->setter( 'deployVersion', $version );
        $regInfo->save();
      }
    }

  }


  private function registerModuleVersion( $moduleName ) {
    echo( $moduleName."::register();\n" );
    if( $this->noExecute !== true) {


      if($moduleName === 'app') {
        global $_C;
        $_C::register();
      }
      else {
        $n=new $moduleName();
        $n::register();
      }

    }

  }



  private function moduleIsRegistered( $moduleName ) {
    $ret = false;

    $moduleReg = new ModuleRegisterModel();

    $v = $moduleReg->listItems( ['filters'=>['name'=> $moduleName ] ]);
    if( $regInfo=$v->fetch() ) {
      $ret = $regInfo->getter('deployVersion');
    }

    return $ret;
  }


  private function execModuleRC( $moduleName ) {
    if( method_exists( $moduleName, 'moduleRc' ) ) {
      echo( "\nINIT: ".$moduleName."::moduleRc( )\n" );

      if( $this->noExecute !== true) {
        (new $moduleName)->moduleRc( );
      }

    }
  }

  private function execModuleDeploy( $moduleName, $whenGenerateModel ) {
    if( method_exists( $moduleName, 'moduleDeploy' ) ) {

      echo( "\nDEPLOY: ".$moduleName."::moduleDeploy( $whenGenerateModel )\n" );

      if( $this->noExecute !== true) {
        (new $moduleName)->moduleDeploy($whenGenerateModel);
      }

    }
  }

  public function createSchemaDB() {
    return $this->data->createSchemaDB();
  }


  public function renderRichSql( $sql ) {


    // Multilang expression
    preg_match_all( "#[\{]\s*multilang\s*\:\s*((.|\n)*?)\s*[\}]#", $sql, $matches);

    if( count($matches[0]) ) {
      for( $mi=0; count($matches[0]) > $mi; $mi++ ) {
        $multilangLines = '';

        foreach( array_keys( Cogumelo::getSetupValue( 'lang:available')) as $lang ) {
          $multilangLines .= str_replace('$lang', $lang, $matches[1][$mi]);
        }

        $sql = str_replace($matches[0][$mi], $multilangLines, $sql);
      }
    }

    //$debug = var_export($matches, true);

    return $sql;
  }

  function getOnlyVersionFromVersionString( $versionString ) {
    $ret = false;
    if( is_numeric($versionString)  ) {
      $ret = floatval( $versionString);
    }
    else {
      preg_match( '#((.*)\#)?(.*)#', $versionString, $vMatches );
      $ret = $vMatches[3];
    }

    return  (float) $ret;
  }

/*
  static function compareDeployVersions( $v1, $v2 ) {

    reg_match( '#^(.*)\#(\d{1,10}(.\d{1,10})?)#', $v1, $v1Matches );
    reg_match( '#^(.*)\#(\d{1,10}(.\d{1,10})?)#', $v2, $v2Matches );

    $v1Matches[2] = ( isset($v1Matches[2]) )? $v1Matches[2] : 0 ;
    $v2Matches[2] = ( isset($v2Matches[2]) )? $v2Matches[2] : 0 ;

    if( int $v1Matches[1] === int $v2Matches[1] && int $v1Matches[2] === int $v2Matches[2] ) {
      $ret = 0;
    }
    else
    if(
      int $v1Matches[1] > int $v2Matches[1] ||
      (
        int $v1Matches[1] === int $v2Matches[1] &&
        int $v1Matches[2] > int $v2Matches[2] &&
      )
    ) {
      $ret = 1;
    }
    else
    if(
      int $v1Matches[1] < int $v2Matches[1] ||
      (
        int $v1Matches[1] === int $v2Matches[1] &&
        int $v1Matches[2] < int $v2Matches[2] &&
      )
    ) {
      $ret = -1;
    }


    return $ret; // -1: $v1 < $v2 0:Equal 1: $v1 > $v2
  }

*/

}
