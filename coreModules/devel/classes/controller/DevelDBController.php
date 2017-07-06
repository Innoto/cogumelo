<?php

Cogumelo::load('coreModel/VOUtils.php');
Cogumelo::load('coreModel/Facade.php');

//
// DevelUtilsDB Controller Class
//
class  DevelDBController {

  var $data;
  var $voUtilControl;
  var $noExecute = false;

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
    echo "DEPLOY";
    exit;
    // first time deploy
    if( $this->VOTableExist( get_class(new ModelRegisterModel()) ) ) {
      $this->VOcreateTable( get_class(new ModelRegisterModel()) );
      ModuleRegisterModel::$NewDeploysSQLChangeColumns;
      //forzar actualizar todas as versións
    }
    else {
      $this->deploy();
    }
  }


  private function deploy() {
    $modules = $this->getModules();



    foreach( $modules as $module ) {


      $moduleDeploys = [];

      //deploy de modelos
      foreach( $this->getModelsInModule() as $model ) {
        if( $this->VOIsRegistered( $model ) ) {
          // rc model
          $moduleDeploys = array_merge($moduleDeploys, $this->VOgetCreateTableAsdeploy($model) );
          $moduleDeploys = array_merge($moduleDeploys, $this->VOgetDeploys( $model, ['onlyRC'=>true] ) );
        }
        else {
          // deploy modelo
          $moduleDeploys = array_merge(
            $moduleDeploys,
            $this->VOgetDeploys(
              $model,
              [
                'from'=> //última versión rexistrada do modulo,
                'to'=> //versión actual do módulo en código
              ]
            )
          );

        }

      }

      $this->executeAllModuleDeploys()


      // deploy de módulo
      if( $this->moduleIsRegistered( $module ) === true ){
        if( $this->moduleIsUpdated() === false ) {
          $this->execModuleDeploy($moduleName, false);
          $this->registerModuleVersion();
        }

      }
      else {
        $this->execModuleRC( $moduleName );
        $this->execModuleDeploy($moduleName, true);
        $this->registerModuleVersion();
      }




    }
    exit;
  }

  public function VOTableExist( $voClass ) {
    $this->data->checkTableExist( $voClass );
  }

  public function VOIsRegistered( $voClass ) {
    $ret = false;



    return $ret;
  }

  public function VOgetCreateTableAsdeploy( $voKey ) {
    return array(
      'version' => 0,
      'sql'=> $this->data->getTableSQL($voKey)
    );
  }


  public function VOcreateTable( $voClass ) {
    $this->data->createTable( $voClass, $this->noExecute );
  }

  private function VOdropTable( $voKey ) {
    $this->data->dropTable( $voKey, $this->noExecute );
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

      foreach( $vo->deploySQL as $d ) {

        $deployElement = $d;


        // exclude when are looking for onlyRC and is not RC deploy
        if(
          $filters['onlyRC'] == true &&
          (
            !isset($deployElement['executeOnGenerateModelToo']) ||
            ( isset($deployElement['executeOnGenerateModelToo']) && $deployElement['executeOnGenerateModelToo'] === false )
          )
        ) {
          $deployElement = false; //exclude
        }


        if(
          $deployElement !== false &&
          $filters['from'] !== false &&
          $d['version'] < $deployElement['from']
        ) {
          $deployElement = false; //exclude
        }

        if(
          $deployElement !== false &&
          $filters['to'] !== false &&
          $d['version'] < $deployElement['from']
        ) {
          $deployElement = false; //exclude
        }

      }
    }


    // return
    return $this->orderByVersion( $deploysArray );
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

    return $retModules;
  }



  public function getModelsInModule( $module) {

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


  private function orderByVersion( $deploys ) {
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


  private function registerModuleVersion( $moduleName ) {
    if( $this->noExecute === true) {
      echo( $moduleName."::register();\n" );
    }
    else {
      eval( $moduleName.'::register();' );
    }

  }



  private function moduleIsRegistered() {

  }

  private function modelIsRegistered() {

  }

  private function execModuleRC( $moduleName ) {
    if( method_exists( $moduleName, 'moduleRc' ) ) {
      echo( "\nINIT: ".$moduleName."::moduleRc( )\n" );
      if( $this->noExecute === true) {
        echo $moduleName."::moduleRc( );\n";
      }
      else {
        eval( $moduleName.'::moduleRc( );' );
      }

    }
  }

  private function execModuleDeploy( $moduleName, $whenGenerateModel ) {
    if( method_exists( $moduleName, 'moduleDeploy' ) ) {
      echo( "\nDEPLOY: ".$moduleName."::moduleDeploy( $whenGenerateModel )\n" );

      if( $this->noExecute === true) {
        echo $moduleName."::moduleRc( );\n";
        echo( "(new ".$moduleName.")->moduleDeploy($whenGenerateModel);\n" );
      }
      else {
        eval( "(new ".$moduleName.")->moduleDeploy($whenGenerateModel);" );
      }

    }
  }

/*
  private function compareDeployVersions( $v1, $v2 ) {

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
