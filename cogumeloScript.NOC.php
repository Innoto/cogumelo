<?php
/**
 * PHPMD: Suppress all warnings from these rules.
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 **/

require_once( COGUMELO_LOCATION.'/coreClasses/CogumeloClass.php' );
require_once( APP_BASE_PATH.'/Cogumelo.php' );

global $COGUMELO_IS_EXECUTING_FROM_SCRIPT;
$COGUMELO_IS_EXECUTING_FROM_SCRIPT=true;

global $_C;
$_C = Cogumelo::get();

//
// Load the necessary modules
//
require_once( ModuleController::getRealFilePath('devel.php', 'devel') );
require_once( ModuleController::getRealFilePath('classes/controller/DevelDBController.php', 'devel') );
require_once( ModuleController::getRealFilePath('classes/controller/CacheUtilsController.php', 'mediaserver') );
Cogumelo::load('coreController/ModuleController.php');


if( empty( $_SERVER['DOCUMENT_ROOT'] ) ) {
  $_SERVER['DOCUMENT_ROOT'] = ( defined('WEB_BASE_PATH') ) ? WEB_BASE_PATH : getcwd().'/httpdocs';
}
echo( 'SERVER[DOCUMENT_ROOT] = '.$_SERVER['DOCUMENT_ROOT']."\n" );


if( $argc > 1 ) {
  //parameters handler
  switch( $argv[1] ) {
    case 'setPermissions': // set the files/folders permission
      setPermissions();
      break;

    case 'setPermissionsDevel': // set the files/folders permission
      setPermissionsDevel();
      break;

    case 'makeAppPaths': // Prepare folders
      makeAppPaths();
      break;


    case 'createDB': // create database
      ( IS_DEVEL_ENV ) ? setPermissionsDevel() : setPermissions();
      backupDB();

      createDB();
      break;

    case 'generateModel':
      ( IS_DEVEL_ENV ) ? setPermissionsDevel() : setPermissions();

      backupDB();
      createRelSchemes();
      generateModel();
      flushAll();
      break;

    case 'deploy':
      ( IS_DEVEL_ENV ) ? setPermissionsDevel() : setPermissions();

      backupDB();
      createRelSchemes();
      deploy();
      flushAll();
      break;

    case 'simulateDeploy':
      simulateDeploy();
      break;


    case 'createRelSchemes':
      ( IS_DEVEL_ENV ) ? setPermissionsDevel() : setPermissions();
      createRelSchemes();
      ( IS_DEVEL_ENV ) ? setPermissionsDevel() : setPermissions();
      break;


    case 'bckDB': // do the backup of the db
    case 'backupDB': // do the backup of the db
      ( IS_DEVEL_ENV ) ? setPermissionsDevel() : setPermissions();

      $file = ( $argc > 2 ) ? $argv[2].'.sql' : false;
      backupDB( $file );
      break;

    case 'restoreDB': // restore the backup of a given db
      if( $argc > 2 ) {
        // backupDB();

        $file = $argv[2]; //name of the backup file
        restoreDB( $file );
      }
      else {
        echo "You must specify the file to restore\n";
      }
      break;


    case 'prepareDependences':
      Cogumelo::load('coreController/DependencesController.php');
      $dependencesControl = new DependencesController();
      $dependencesControl->prepareDependences();
      break;

    case 'updateDependences':
      Cogumelo::load('coreController/DependencesController.php');
      $dependencesControl = new DependencesController();
      $dependencesControl->installDependences();
      break;

    case 'installDependences':
      Cogumelo::load('coreController/DependencesController.php');
      $dependencesControl = new DependencesController();
      $dependencesControl->prepareDependences();

      Cogumelo::load('coreController/DependencesController.php');
      $dependencesControl = new DependencesController();
      $dependencesControl->installDependences();
      break;


    case 'generateFrameworkTranslations':
      Cogumelo::load('coreController/i18nScriptController.php');
      $i18nscriptController = new i18nScriptController();
      $i18nscriptController->setEnviroment();
      $i18nscriptController->c_i18n_getSystemTranslations();
      echo "The files.po are ready to be edited!\n";
      break;

    case 'generateAppTranslations':
      Cogumelo::load('coreController/i18nScriptController.php');
      $i18nscriptController = new i18nScriptController();
      $i18nscriptController->setEnviroment();
      $i18nscriptController->c_i18n_getAppTranslations();
      echo "The files.po are ready to be edited!\n";
      break;

    case 'removeAllTranslations':
      Cogumelo::load('coreController/i18nScriptController.php');
      $i18nscriptController = new i18nScriptController();
      $i18nscriptController->c_i18n_removeTranslations();
      break;

    case 'precompileTranslations':
      actionPrecompileTranslations();
      break;

    case 'compileTranslations':
      actionCompileTranslations();
      break;

    case 'jsonTranslations':
      Cogumelo::load('coreController/i18nScriptController.php');
      $i18nscriptController = new i18nScriptController();
      $i18nscriptController->c_i18n_json();
      echo "The files.json are ready to be used!\n";
      break;


    /* We execute this two actions from web as we need to operate with the apache permissions*/
    case 'flush': // delete temporary files
      flushAll();
      echo "\n --- Flush DONE\n";
      break;

    // case 'rotateLogs':
    //   actionRotateLogs();
    //   break;

    case 'generateClientCaches':
      actionGenerateClientCaches();
      break;

    default:
      echo "Invalid parameter;try:";
      printOptions();
      break;

  }//end switch
}//end parameters handler
else{
  echo "You have to write an option:";
  printOptions();
}


function printOptions(){
  echo "\n
 + Permissions and dependences
    * flush                   Remove temporary files
      * setPermissions(Devel) Set the files/folders permission
        * makeAppPaths        Prepare folders
      * generateClientCaches  Cache all js, css, compiled less and other client files
    * installDependences      Exec prepareDependences and then exec updateDependences
      * prepareDependences    Generate JSON's dependences
      * updateDependences     Install all modules dependencies


 + Database
    * createDB                Create a database

    * generateModel           Initialize database

    * deploy                  Deploy
      * createRelSchemes      Create JSON Model Rel Schemes
      * simulateDeploy          simulate deploy SQL codes

    * resetModules
      - resetModuleVersions

    * backupDB                Do a DB backup (optional arg: filename)
    * restoreDB               Restore a database

 + Internationalization
    * generateFrameworkTranslations    Update text to translate in cogumelo and geozzy modules
    * generateAppTranslations    Get text to translate in the app
    * precompileTranslations     Generate the intermediate POs(geozzy, cogumelo and app)
    * compileTranslations     Mix geozzy, cogumelo and app POS in one and compile it to get the translations ready
  \n\n";
}

function actionPrecompileTranslations() {
  Cogumelo::load('coreController/i18nScriptController.php');
  $i18nscriptController = new i18nScriptController();
  $i18nscriptController->c_i18n_precompile();
  echo "\nThe intermediate .po are ready\n\n";
}

function actionCompileTranslations() {
  Cogumelo::load('coreController/i18nScriptController.php');
  $i18nscriptController = new i18nScriptController();
  /*$i18nscriptController->setEnviroment();*/
  $i18nscriptController->c_i18n_compile();
  /* generate json for js */
  $i18nscriptController->c_i18n_json();
  echo "\nThe files.mo are ready to be used!\n\n";
}

function generateModel() {
  $develdbcontrol = new DevelDBController();
  $develdbcontrol->scriptGenerateModel();
}

function deploy() {
  $develdbcontrol = new DevelDBController();
  $develdbcontrol->scriptDeploy();
}

function simulateDeploy() {
  ob_start(); // Start output buffering
  $fvotdbcontrol = new DevelDBController();
  $fvotdbcontrol->deploy();
  $ret= ob_get_contents(); // Store buffer in variable
  ob_end_clean(); // End buffering and clean up
  var_dump( [$ret] );
}

function createRelSchemes() {
  echo "\nCreating relationship schemes\n";

  global $C_ENABLED_MODULES;

  foreach( $C_ENABLED_MODULES as $moduleName ) {
    require_once( ModuleController::getRealFilePath( $moduleName.'.php' , $moduleName) );
  }

  Cogumelo::load('coreModel/VOUtils.php');
  VOUtils::createModelRelTreeFiles();
}

function flushAll() {
  echo "\n --- setPermissions:\n";
  ( IS_DEVEL_ENV ) ? setPermissionsDevel() : setPermissions();

  echo "\n --- actionFlush:\n";
  actionFlush();

  // echo "\n --- actionCompileTranslations:\n";
  // actionCompileTranslations();

  echo "\n --- actionGenerateClientCaches:\n";
  if( Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) ) {
    actionGenerateClientCaches();
  }
  else {
    echo "\nPasamos porque no estamos en PRODUCTION MODE\n";
  }

  echo "\n --- setPermissions:\n";
  ( IS_DEVEL_ENV ) ? setPermissionsDevel() : setPermissions();
}


function actionFlush() {

  // Def: app/tmp/templates_c
  rmdirRec( Cogumelo::getSetupValue('smarty:compilePath'), false );
  // Def: httpdocs/cgmlImg
  rmdirRec( Cogumelo::getSetupValue('mod:filedata:cachePath'), false );
  // Def: httpdocs/mediaCache
  rmdirRec( Cogumelo::getSetupValue('mod:mediaserver:tmpCachePath'), false );
  echo ' - Cogumelo File cache flush DONE'."\n";


  require_once( COGUMELO_LOCATION.'/coreClasses/coreController/Cache.php' );
  $cacheCtrl = new Cache();
  $cacheCtrl->flush();
  echo ' - Cogumelo Memory Cache flush DONE'."\n";


  $scriptCogumeloServerUrl = Cogumelo::getSetupValue( 'script:cogumeloServerUrl' );
  if( !empty( $scriptCogumeloServerUrl ) ) {
    echo ' - Cogumelo PHP cache flush...'."\n";

    // TODO: EVITAMOS CONTROLES HTTPS
    $contextOptions = stream_context_create( [
      "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
      ],
    ] );
    echo file_get_contents( $scriptCogumeloServerUrl . '?q=flush', false, $contextOptions );
  }
  else {
    echo ' - Cogumelo PHP cache flush DESCARTADO.'."\n";
  }

  echo "\nCogumelo caches deleted!\n\n";
}


// function actionRotateLogs() {
//   echo file_get_contents( Cogumelo::getSetupValue( 'script:cogumeloServerUrl' ) . '?q=rotate_logs' );
//   echo "\nRotate Logs DONE!\n\n";
// }


function actionGenerateClientCaches() {


  require_once( ModuleController::getRealFilePath( 'mediaserver.php', 'mediaserver' ) );
  mediaserver::autoIncludes();
  CacheUtilsController::generateAllCaches();


  // $ctx = stream_context_create( ['http'=> ['timeout' => 1200 ] ] ); //1200 Seconds is 20 Minutes
  // echo("Calling ".Cogumelo::getSetupValue( 'script:cogumeloServerUrl' ) . '?q=client_caches ... If you have any problem in less compilation, execute this url in browser');
  // file_get_contents( Cogumelo::getSetupValue( 'script:cogumeloServerUrl' ) . '?q=client_caches', false, $ctx);


  echo "\nClient caches generated\n\n";
}


function createDB(){

  echo "\nDatabase configuration\n";

  $user = false;

  $fileConnectionsInfo = APP_BASE_PATH.'/conf/inc/default-connections-info.php';
  if( file_exists( $fileConnectionsInfo ) ) {
    include $fileConnectionsInfo;
    if( defined( 'DDBB_PRIV_USER' ) && defined( 'DDBB_PRIV_PASS' ) ) {
      $user = DDBB_PRIV_USER;
      $passwd = DDBB_PRIV_PASS;
    }
  }

  if( !$user ) {
    $user = ReadStdin( "Enter an user with privileges:\n", '' );
    fwrite( STDOUT, "Enter the password:\n" );
    $passwd = getPassword( true );
    fwrite( STDOUT, "\n--\n" );
  }


  $develdbcontrol = new DevelDBController( $user, $passwd );
  $develdbcontrol->createSchemaDB();
  echo "\nDatase created!\n";
}



function makeAppPaths() {
  echo "makeAppPaths\n";

  // global $lc;

  $dirList = array( APP_TMP_PATH,
    Cogumelo::getSetupValue( 'smarty:configPath' ), Cogumelo::getSetupValue( 'smarty:compilePath' ),
    Cogumelo::getSetupValue( 'smarty:cachePath' ), Cogumelo::getSetupValue( 'smarty:tmpPath' ),
    Cogumelo::getSetupValue( 'mod:mediaserver:tmpCachePath' ),
    // WEB_BASE_PATH.'/'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ),
    WEB_BASE_PATH.'/'.Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ),
    Cogumelo::getSetupValue( 'logs:path' ),
    Cogumelo::getSetupValue( 'session:savePath' ),
    Cogumelo::getSetupValue( 'mod:form:tmpPath' ),
    Cogumelo::getSetupValue( 'mod:filedata:filePath' ),
    Cogumelo::getSetupValue( 'mod:filedata:cachePath' ),
    Cogumelo::getSetupValue( 'script:backupPath' ),
    Cogumelo::getSetupValue( 'i18n:path' ), Cogumelo::getSetupValue( 'i18n:localePath' )
  );

  foreach( Cogumelo::getSetupValue( 'lang:available' ) as $lang ) {
    $dirList[] = Cogumelo::getSetupValue( 'i18n:localePath' ).'/'.$lang['i18n'].'/LC_MESSAGES';
  }

  $sessionSavePath = Cogumelo::getSetupValue('session:savePath');
  if( !empty( $sessionSavePath ) ) {
    $dirList[] = $sessionSavePath;
  }

  // echo "\n\nMKDIR\n".json_encode($dirList)."\n\n";

  foreach( $dirList as $dir ) {
    if( $dir && $dir !== '' && !is_dir( $dir ) ) {
      if( !mkdir( $dir, 0750, true ) ) {
        echo 'ERROR: Imposible crear el dirirectorio: '.$dir."\n";
      }
    }
  }
  echo "makeAppPaths DONE.\n";
}

function setPermissions( $devel = false ) {
  makeAppPaths();

  $extPerms = $devel ? ',ugo+rX' : '';
  $sudo = 'sudo ';

  echo( "setPermissions ".($devel ? 'DEVEL' : '')."\n" );

  if( IS_DEVEL_ENV ) {
    $dirsString =
      WEB_BASE_PATH.' '.APP_BASE_PATH.' '.APP_TMP_PATH.' '.
      Cogumelo::getSetupValue( 'smarty:configPath' ).' '.Cogumelo::getSetupValue( 'smarty:compilePath' ).' '.
      Cogumelo::getSetupValue( 'smarty:cachePath' ).' '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).' '.
      Cogumelo::getSetupValue( 'mod:mediaserver:tmpCachePath' ).' '.
      WEB_BASE_PATH.'/'.Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ).' '.
      Cogumelo::getSetupValue( 'logs:path' ).' '.
      Cogumelo::getSetupValue( 'mod:form:tmpPath' ).' '.
      Cogumelo::getSetupValue( 'mod:filedata:filePath' ).' '.
      Cogumelo::getSetupValue( 'i18n:path' ).' '.Cogumelo::getSetupValue( 'i18n:localePath' )
    ;

    $fai = 'chgrp -R www-data '.$dirsString;
    echo( " - Executamos chgrp general \n" );
    exec( $sudo.$fai );
  }
  else {
    // exec( 'sudo '.$fai );
    echo( " - NON se executa chgrp general \n" );
  }


  if( IS_DEVEL_ENV ) {
    $fai = 'chmod -R go-rwx,g+rX'.$extPerms.' '.WEB_BASE_PATH.' '.APP_BASE_PATH;
    echo( " - Executamos chmod WEB_BASE_PATH APP_BASE_PATH\n" );
    exec( $sudo.$fai );
  }
  else {
    // exec( 'sudo '.$fai );
    echo( " - NON se executa chmod WEB_BASE_PATH APP_BASE_PATH\n" );
  }


  if( IS_DEVEL_ENV ) {
    // Path que necesitan escritura Apache
    $fai = 'chmod -R ug+rwX'.$extPerms.' '.APP_TMP_PATH.' '.
      // Smarty
      Cogumelo::getSetupValue( 'smarty:configPath' ).' '.Cogumelo::getSetupValue( 'smarty:compilePath' ).' '.
      Cogumelo::getSetupValue( 'smarty:cachePath' ).' '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).' '.

      // Cogumelo mediaserver
      Cogumelo::getSetupValue( 'mod:mediaserver:tmpCachePath' ).' '.
      WEB_BASE_PATH.'/'.Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ).' '.

      // Form y Filedata
      Cogumelo::getSetupValue( 'mod:filedata:cachePath' ).' '. // cgmlImg
      Cogumelo::getSetupValue( 'mod:filedata:filePath' ).' '. // formFiles
      Cogumelo::getSetupValue( 'mod:form:tmpPath' ).' '. // tmp formFiles

      // Varios
      Cogumelo::getSetupValue( 'logs:path' ).' '.
      // Cogumelo::getSetupValue( 'session:savePath' ).' '.
      // Cogumelo::getSetupValue( 'i18n:path' ).' '.Cogumelo::getSetupValue( 'i18n:localePath' ).' '.
      ''
    ;
    echo( " - Executamos chmod APP_TMP_PATH\n" );
    exec( $sudo.$fai );
  }
  else {
    echo( " - NON se executa chmod APP_TMP_PATH\n" );
  }

  if( IS_DEVEL_ENV ) {
    echo( " - Preparando [session:savePath] e [script:backupPath]\n" );
    // session:savePath tiene que mantener el usuario y grupo
    $sessionSavePath = Cogumelo::getSetupValue( 'session:savePath' );
    if( !empty($sessionSavePath) ) {
      $fai = 'chgrp -R www-data '.$sessionSavePath;
      echo( "  - Executamos $fai\n" );
      exec( $sudo.$fai );
      $fai = 'chmod -R ug+rwX'.$extPerms.' '.$sessionSavePath;
      echo( "  - Executamos $fai\n" );
      exec( $sudo.$fai );
    }

    // Solo usuario administrador
    $backupPath = Cogumelo::getSetupValue( 'script:backupPath' );
    if( !empty($backupPath) ) {
      $fai = 'chmod -R go-rwx '.$backupPath;
      echo( "  - Executamos $fai\n" );
      exec( $sudo.$fai );
    }
  }
  else {
    echo( " - NON se preparan [session:savePath] e [script:backupPath]\n" );
  }

  echo( "setPermissions ".($devel ? 'DEVEL' : '')." DONE.\n" );
}

function setPermissionsDevel() {
  setPermissions( true );
}


function backupDB( $file = false ) {
  doBackup(
    Cogumelo::getSetupValue('db:name'),
    Cogumelo::getSetupValue('db:user'),
    Cogumelo::getSetupValue('db:password'),
    $file,
    Cogumelo::getSetupValue('db:hostname')
  );
}

function doBackup( $dbName, $user, $passwd, $file, $dbHost ) {
  if( empty( $file ) ) {
    $file = date('Ymd-His').'-'.$dbName.'.sql';
  }

  if( empty( $dbHost ) ) {
    $dbHost = 'localhost';
  }

  $dir = Cogumelo::getSetupValue( 'script:backupPath' );

  $cmdBackup = 'mysqldump --hex-blob --complete-insert --skip-extended-insert '.
    '-h '.$dbHost.' -u '.$user.' -p'.$passwd.' '.$dbName.' > '.$dir.'/'.$file;

  popen( $cmdBackup, 'r' );
  exec( 'gzip ' . $dir . '/' . $file );
  exec( 'chmod go-rwx ' . $dir . '/' . $file . '*' );
  echo "\nYour db was successfully saved!\n";
}


function restoreDB( $file = false ) {

  $dbHost = Cogumelo::getSetupValue('db:hostname');
  $dbName = Cogumelo::getSetupValue('db:name');
  $user = Cogumelo::getSetupValue('db:user');
  $passwd = Cogumelo::getSetupValue('db:password');

  doBackup( $dbName, $user, $passwd, false, $dbHost );

  $dir = Cogumelo::getSetupValue( 'script:backupPath' );

  if( empty( $dbHost ) ) {
    $dbHost = 'localhost';
  }

  // $file_parts = explode('.',$dir.$file);
  $fileExt = pathinfo( $dir.$file, PATHINFO_EXTENSION );

  // if ($file_parts[2] == 'gz'){
  if( $fileExt === 'gz' ) {
    popen('gunzip -c '.$dir.$file.' | mysql -h '.$dbHost.' -u '.$user.' -p'.$passwd.' '.$dbName, 'r');
  }
  else {
    popen('mysql -h '.$dbHost.' -u '.$user.' -p'.$passwd.' '.$dbName.'<' .$dir.$file, 'r');
  }
  echo "\nYour db was successfully restored!\n";
}

/**
 * Get data from the shell.
 */
function ReadStdin( $prompt, $valid_inputs, $default = '' ) {
  while( !isset($input) || ( is_array($valid_inputs) && !in_array($input, $valid_inputs) ) || ( $valid_inputs === 'is_file' && !is_file($input) ) ) {
    echo $prompt;
    $input = strtolower(trim(fgets(STDIN)));
    if( empty($input) && !empty($default) ) {
      $input = $default;
    }
  }
  return $input;
}

/**
 * Get a password from the shell.
 * This function works on *nix systems only and requires shell_exec and stty.
 *
 * @param boolean $stars Wether or not to output stars for given characters
 *
 * @return string
 */
function getPassword( $stars = false ) {
  // Get current style
  $oldStyle = shell_exec('stty -g');

  if ($stars === false) {
    shell_exec('stty -echo');
    $password = rtrim(fgets(STDIN), "\n");
  }
  else {
    shell_exec('stty -icanon -echo min 1 time 0');

    $password = '';

    while( true ) {
      $char = fgetc( STDIN );

      if( $char === "\n" ) {
        break;
      }
      elseif( ord($char) === 127 ) {
        if( strlen($password) > 0 ) {
          fwrite( STDOUT, "\x08 \x08" );
          $password = substr( $password, 0, -1 );
        }
      }
      else {
        fwrite( STDOUT, "*" );
        $password .= $char;
      }
    }
  }

  // Reset old style
  shell_exec('stty ' . $oldStyle);

  // Return the password
  return $password;
}


function rmdirRec( $dir, $removeContainer = true ) {
  // error_log( "rmdirRec( $dir )" );

  $dir = rtrim( $dir, '/' );
  if( !empty( $dir ) && strpos( $dir, PRJ_BASE_PATH ) === 0 && is_dir( $dir ) ) {
    $dirElements = scandir( $dir );
    if( !empty( $dirElements ) ) {
      foreach( $dirElements as $object ) {
        if( $object !== '.' && $object !== '..' ) {
          if( is_dir( $dir.'/'.$object ) ) {

            rmdirRec( $dir.'/'.$object );
          }
          else {

            unlink( $dir.'/'.$object );
          }
        }
      }
    }
    reset( $dirElements );
    if( $removeContainer ) {
      if( !is_link( $dir ) ) {
        rmdir( $dir );
      }
      else {
        unlink( $dir );
      }
    }
  }
}
