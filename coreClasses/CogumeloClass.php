<?php

require_once( COGUMELO_LOCATION.'/coreClasses/coreController/Singleton.php' );
require_once( COGUMELO_LOCATION.'/coreClasses/coreController/ModuleController.php' );
require_once( COGUMELO_LOCATION.'/coreClasses/coreController/DependencesController.php' );
require_once( COGUMELO_LOCATION.'/coreClasses/coreController/I18n.php' );
require_once( COGUMELO_LOCATION.'/coreClasses/coreController/SetupMethods.php' );
require_once( COGUMELO_LOCATION.'/coreClasses/coreController/CacheByUrlController.php' );

// require_once( COGUMELO_LOCATION.'/coreModules/cogumeloSession/classes/controller/CogumeloSessionController.php' );



class CogumeloClass extends Singleton {

  public $request;
  public $modules;

  private $urlPatterns;

  protected $userinfoString = '';

  private $enviromentTimezones = null;

  private static $setupMethods = null;

  public $dependences = array();
  public $includesCommon = array();

  // main dependences for cogumelo framework
  static $mainDependences = array(
     array(
       'id' => 'phpmailer',
       'params' => array( 'phpmailer/phpmailer', '5.2.26' ),
       'installer' => 'composer',
       'includes' => array('PHPMailerAutoload.php')
       // 'includes' => array('class.phpmailer.php')
     ),
     array( // para phpmailer
       'id' => 'oauth2-client',
       'params' => array( 'league/oauth2-client', '1.4.*' ),
       'installer' => 'composer',
       'includes' => array()
     ),
     array( // para phpmailer
       'id' => 'oauth2-google',
       'params' => array( 'league/oauth2-google', '1.0.*' ),
       'installer' => 'composer',
       'includes' => array()
     ),
     array(
       'id' => 'smarty',
       'params' => array('smarty/smarty', '3.1.33'),
       'installer' => 'composer',
       'includes' => array('libs/Smarty.class.php')
     ),
     array(
       'id' => 'jquery',
       'params' => array('jquery@3.3'),
       'installer' => 'yarn',
       'includes' => array()
     ),

     array(
      "id" => "popper.js",
      "params" => array("popper.js"),
      "installer" => "yarn"
     ),
     array(
      "id" => "bootstrap",
      "params" => array("bootstrap@4"),
      "installer" => "yarn"
     ),
     array(
       'id' => 'gettext',
       'params' => array('Gettext'),
       'installer' => 'manual',
       'includes' => array('')
     ),
     array(
       'id' => 'smarty-gettext',
       'params' => array('smarty-gettext'),
       'installer' => 'manual',
       'includes' => array('block.t.php')
     ),
     array(
       'id' =>'rsvp',
       'params' => array('rsvp'),
       'installer' => 'manual',
       'includes' => array()
     ),
     array(
       'id' =>'basket',
       'params' => array('basket'),
       'installer' => 'manual',
       'includes' => array()
     )
     /*
     ,
     array(
       'id' => 'php-jwt',
       'params' => array('firebase/php-jwt', '3.*'),
       'installer' => 'composer',
       'includes' => array('src/JWT.php')
     )
     */
  );

  public function __construct() {
    // Control hard url cache
    $cacheByUrlControl = new CacheByUrlController();

    $this->setTimezones();
    // CogumeloSession controller
    $cogumeloSessionControllerClassFile = Cogumelo::getSetupValue( 'cogumeloSessionController:classFile' );
    if( empty( $cogumeloSessionControllerClassFile ) || !file_exists( $cogumeloSessionControllerClassFile ) ) {
      $cogumeloSessionControllerClassFile = COGUMELO_LOCATION.
        '/coreModules/cogumeloSession/classes/controller/CogumeloSessionController.php';
    }
    require_once( $cogumeloSessionControllerClassFile );
    $sessionCtrl = new CogumeloSessionController();
    $sessionCtrl->prepareTokenSessionEnvironment();

    /*
    session_start();
    global $C_SESSION_ID;
    $C_SESSION_ID = session_id();
    */
  }

  // Set autoincludes
  public static function autoIncludes() {
    $dependencesControl = new DependencesController();
    $dependencesControl->loadAppIncludes();
  }


  public static function get(){
    return parent::getInstance('Cogumelo');
  }

  public function exec() {
    /* i18n */
    Cogumelo::load('coreController/I18nController.php');
    I18nController::setLang();

    // cut out the SITE_FOLDER and final slash from path
    $url_path = preg_replace('#\/$#', '', preg_replace('#^'.SITE_FOLDER.'#', '', $_SERVER['REQUEST_URI'], 1) , 1);

    // modules
    $this->modules = new ModuleController( $url_path );
    $url_path_after_modules = $this->modules->getLeftUrl();

    // main request controller
    self::load('coreController/RequestController.php');
    $this->request = new RequestController($this->urlPatterns, $url_path_after_modules );
  }







  public function setTimezones() {
    $cgmlTZ = [
      'system' => false,
      'database' => false,
      'project' => false
    ];

    $tzOptions = DateTimeZone::listIdentifiers();


    // System Timezone
    $tzName = Cogumelo::getSetupValue('date:timezone:system');
    if( empty( $tzName ) || !in_array( $tzName, $tzOptions ) ) {
      $tzName = 'UTC';
    }
    $cgmlTZ['system'] = new DateTimeZone( $tzName );


    // Database Timezone
    $tzName = Cogumelo::getSetupValue('date:timezone:database');
    if( empty( $tzName ) || !in_array( $tzName, $tzOptions ) ) {
      $tzName = 'UTC';
    }
    $cgmlTZ['database'] = new DateTimeZone( $tzName );


    // Project Timezone
    if( Cogumelo::getSetupValue('date:timezone:project') ) {
      $tzName = Cogumelo::getSetupValue('date:timezone:project');
    }
    else {
      $tzName = Cogumelo::getSetupValue('date:timezone');
    }
    if( empty( $tzName ) || gettype( $tzName ) !== 'string' || !in_array( $tzName, $tzOptions ) ) {
      $tzName = 'UTC';
    }
    $cgmlTZ['project'] = new DateTimeZone( $tzName );


    // PHP data Timezone = System
    date_default_timezone_set( $cgmlTZ['system']->getName() );


    $this->enviromentTimezones = $cgmlTZ;
    return $cgmlTZ;
  }


  public static function getTimezoneSystem() {
    $cgmlObj = self::get();
    return $cgmlObj->enviromentTimezones['system'];
  }

  public static function getTimezoneDatabase() {
    $cgmlObj = self::get();
    return $cgmlObj->enviromentTimezones['database'];
  }

  public static function getTimezoneProject() {
    $cgmlObj = self::get();
    return $cgmlObj->enviromentTimezones['project'];
  }





  public function viewUrl( $url ) {

    // cut out the SITE_FOLDER and final slash from path
    $url_path = preg_replace('#\/$#', '', preg_replace('#^'.SITE_FOLDER.'#', '', $url, 1) , 1);

    // modules
    $this->modules = new ModuleController( $url_path );
    $url_path_after_modules = $this->modules->getLeftUrl();

    // main request controller
    self::load('coreController/RequestController.php');
    $this->request = new RequestController($this->urlPatterns, $url_path_after_modules );
  }

  //
  //  include
  //
  public static function load( $classname ) {

    if( preg_match('#^core#', $classname) ){
      $filename =  $classname;
      $file_path = COGUMELO_LOCATION.'/coreClasses/'.$filename;
    }
    else {
      $filename =  $classname;
      $file_path = APP_BASE_PATH. '/classes/'. $filename;
    }

    // check if file exist
    if(!file_exists($file_path)) {
      Cogumelo::error('PHP File not found : '.$file_path);
    }
    else {
      require_once $file_path;
    }
  }


  //
  //  include Vendor libs
  //
  public static function vendorLoad( $loadFile ) {
    require_once WEB_BASE_PATH.'/vendorServer/'.$loadFile;
  }


  //
  //  Redirect (alias for RequestController::redirect )
  //
  public static function redirect( $redirect_url ) {
    RequestController::redirect( $redirect_url );
  }


  //
  //  Error Handler
  //
  public static function warningHandler( $errno, $errstr, $errfile, $errline ) {

    $error_msg = 'Warning: '.$errstr.' on file "'.$errfile.'" line:'.$errline;

    // if(Cogumelo::getSetupValue( 'logs:debug' )){
    //   self::console(debug_backtrace(), $error_msg );
    // }

    self::error($error_msg);
  }

  public static function errorHandler() {

    $last_error = error_get_last();

    if($last_error!=null) {
      $error_msg = 'Fatal error: '.$last_error['message'].' on file "'.$last_error['file'].'" line: '.$last_error['line'];

      // if( Cogumelo::getSetupValue( 'logs:debug' ) ) {
      //   self::console($last_error, $error_msg);
      // }

      self::error($error_msg);
    }
  }

  //
  //  LOGS
  //
  public static function error( $description ) {
    global $COGUMELO_IS_EXECUTING_FROM_SCRIPT;

    if( Cogumelo::getSetupValue( 'logs:error' ) === true  ||  $COGUMELO_IS_EXECUTING_FROM_SCRIPT == true ) {
      echo '<br>Cogumelo error: '.$description."\n";
    }

    self::log($description, 'cogumelo_error');
  }

  public static function debug( $description ) {
    if( Cogumelo::getSetupValue( 'logs:debug' ) === true ) {
      self::log($description, 'cogumelo_debug');
    }
  }

  public static function debugSQL( $description ) {
    if( Cogumelo::getSetupValue( 'logs:rawSql' ) === true ) {
      self::log($description, 'cogumelo_debug_sql');
    }
  }

  public static function log( $texto, $logLabel = 'cogumelo' ) {
    global $COGUMELO_DISABLE_LOGS;

    if( !$COGUMELO_DISABLE_LOGS ) {
      $develPanel = false;
      $develUrl = Cogumelo::getSetupValue( 'mod:devel:url' );
      if( !empty( $develUrl ) ) {
        $develPanel = $_SERVER['REQUEST_URI'] === '/'.$develUrl.'/read_logs' ||
          $_SERVER['REQUEST_URI'] === '/'.$develUrl.'/get_debugger';
      }

      if( !$develPanel ) {
        $setupLogs = Cogumelo::getSetupValue('logs');
        $typeLog = empty( $setupLogs['type'] ) ? 'file' : $setupLogs['type'];

        if( $typeLog !== 'disable' && !empty( $setupLogs['disableLabels'] ) && is_array( $setupLogs['disableLabels'] ) ) {
          if( in_array( $logLabel, $setupLogs['disableLabels'] ) ) {
            $typeLog = 'disable';
          }
        }

        switch( $typeLog ) {
          case 'disable':
            break;

          case 'syslog':
            // Secure
            $logLabel = basename( $logLabel );

            $idName = '';
            if( !empty( $setupLogs['idName'] ) ) {
              $idName = '-'.$setupLogs['idName'];
            }
            elseif( $prjIdName=Cogumelo::getSetupValue('project:idName') ) {
              $idName = '-'.$prjIdName;
            }
            elseif( $dbName=Cogumelo::getSetupValue('db:name') ) {
              $idName = '-'.$dbName;
            }

            $logLabel = str_replace( 'cogumelo_', '', $logLabel );

            $logLevel = LOG_INFO;
            $logMsg = '['.$_SERVER['REMOTE_ADDR'] .']['.self::getUserInfo().'] '.$texto;
            // $logMsg = '['.$_SERVER['REMOTE_ADDR'] .']['.self::getUserInfo().'] '.str_replace("\n", '\n', $texto);

            switch( $logLabel ) {
              case 'error':
                $logLevel = LOG_ERR;
                break;
              case 'debug':
                $logLevel = LOG_DEBUG;
                break;
              case 'debug_sql':
                $logLevel = LOG_DEBUG;
                break;
              case 'cogumelo':
                $logLevel = LOG_NOTICE;
                break;
            }

            $logId = 'cogumelo-'.$logLabel.$idName;

            openlog( $logId, LOG_ODELAY, LOG_LOCAL0 );
            syslog( $logLevel, $logMsg );
            closelog();
            break;

          default:
            // Secure
            $logLabel = basename( $logLabel );

            $msg = '['.date('y-m-d H:i:s',time()).'] ['.$_SERVER['REMOTE_ADDR'] .'] '.
              '[Session '.self::getUserInfo().'] '.str_replace("\n", '\n', $texto)."\n";

            if( !empty( $setupLogs['path'] ) ) {
              $fileLog = $setupLogs['path'].'/'.$logLabel.'.log';
            }
            else {
              $fileLog = Cogumelo::getSetupValue('setup:appBasePath').'/log/'.$logLabel.'.log';
            }

            error_log( $msg, 3, $fileLog );
            break;
        }
      } // if( !$develPanel )
    }
  }

  public static function disableLogs( $disable = true ) {
    global $COGUMELO_DISABLE_LOGS;
    $COGUMELO_DISABLE_LOGS = $disable;
  }

  // set an string with user information
  public function setUserInfo( $userinfoString ) {
    $this->userinfoString = $userinfoString;
  }

  public static function getUserInfo() {
    if(class_exists('UserSessionController')) {
      require_once(ModuleController::getRealFilePath('classes/controller/UserSessionController.php', 'user'));
      $userSessionControl = new UserSessionController();
      if($user = $userSessionControl->getUser()) {
        $res = $user['data']['login'];
      }
      else{
        $res = "";
      }
    }
    else {
      $res = "";
    }
    return $res;
  }



  //
  //  Advanced Object Debug
  //
  public static function objDebugObjectCreate( $obj, $comment ) {
    return array( 'comment' => $comment, 'creation_date' => getdate(), 'data' => $obj );
  }

  public static function objDebugPull() {
    $now = getdate();
    $debug_object_maxlifetime = 60; // in seconds
    $result_array = array();

    if( Cogumelo::getSetupValue( 'logs:debug' ) &&
      isset($_SESSION['cogumelo_dev_obj_array'])  &&
      $_SESSION['cogumelo_dev_obj_array'] != '' &&
      $_SESSION['cogumelo_dev_obj_array'] != null &&
      is_array(unserialize($_SESSION['cogumelo_dev_obj_array']))
    ) {

      $session_array = unserialize( $_SESSION['cogumelo_dev_obj_array'] );

      if(is_array($session_array) && count($session_array) > 0 ) {
        foreach( $session_array as $session_obj ) {
          if( isset($session_obj['creation_date'])
            && ( $now[0] - $session_obj['creation_date'][0]) <= $debug_object_maxlifetime
          ) {
            array_push($result_array, $session_obj);
          }
        }
      }

      // reset sesesion array
      $_SESSION['cogumelo_dev_obj_array'] = array();
    }

    return $result_array;
  }

  public static function console( $obj, $comment = '' ) {
    return self::objDebugPush($obj, $comment);
  }

  public static function objDebugPush( $obj, $comment ) {
    if(Cogumelo::getSetupValue( 'logs:debug' ) && isset($obj)){

      $session_array = array();

      if( isset($_SESSION['cogumelo_dev_obj_array']) &&
        $_SESSION['cogumelo_dev_obj_array'] != '' &&
        $_SESSION['cogumelo_dev_obj_array'] != null &&
        is_array(unserialize($_SESSION['cogumelo_dev_obj_array']))
      ) {

        $session_array = unserialize($_SESSION['cogumelo_dev_obj_array']);
      }

      //var_dump($session_array);

      array_push($session_array, self::objDebugObjectCreate($obj, $comment) );

      $_SESSION['cogumelo_dev_obj_array'] = serialize($session_array);
    }
  }


  //
  // Metodos duplicados en Module.php
  // (Ini)

  public function deleteUrlPatterns() {
    $this->urlPatterns = array();
  }

  public function addUrlPatterns( $regex, $destination ) {
    $this->urlPatterns[ $regex ] = $destination;
  }

  public function setUrlPatternsFromArray( $arrayUrlPatterns ) {
    $this->deleteUrlPatterns();
    foreach( $arrayUrlPatterns as $key => $value ) {
      $this->addUrlPatterns( $key, $value );
    }
  }

  public function getUrlPatternsToArray() {
    return $this->urlPatterns;
  }

  // (Fin)
  // Metodos duplicados en Module.php
  //





  /*
   * INI - Setup methods. Shared with the setup files
   */

  // require_once( COGUMELO_LOCATION.'/coreClasses/coreController/SetupMethods.php' );
  public static function getSetupMethods() {
    if( empty( self::$setupMethods ) ) {
      self::$setupMethods = new SetupMethods();
    }
    return self::$setupMethods;
  }

  public static function setSetupValue( $path, $value ) {
    return self::getSetupMethods()->setSetupValue( $path, $value );
  }
  public static function getSetupValue( $path = false ) {
    return self::getSetupMethods()->getSetupValue( $path );
  }
  public static function issetSetupValue( $path ) {
    return self::getSetupMethods()->issetSetupValue( $path );
  }
  public static function createSetupValue( $path, $value ) {
    return self::getSetupMethods()->createSetupValue( $path, $value );
  }
  public static function updateSetupValue( $path, $value ) {
    return self::getSetupMethods()->updateSetupValue( $path, $value );
  }
  public static function addSetupValue( $path, $value ) {
    return self::getSetupMethods()->addSetupValue( $path, $value );
  }
  public static function mergeSetupValue( $path, $addArray ) {
    return self::getSetupMethods()->mergeSetupValue( $path, $addArray );
  }
  /*
   * END - Setup methods. Shared with the setup files
   */




  /**
  * Un-register the app
  */
  public static function unRegister() {

    devel::load('model/ModuleRegisterModel.php');

    $moduleRegisterControl = new ModuleRegisterModel();
    $moduleRegisters = $moduleRegisterControl->listItems( array('filters'=>array( 'name'=>static::class ) ));



    if( $regModuleInfo = $moduleRegisters->fetch() ) {
      $regModuleInfo->delete();
    }

  }

  /**
  * Register or update app register
  */
  public static function register() {

    devel::load('model/ModuleRegisterModel.php');

    $moduleRegisterControl = new ModuleRegisterModel();
    $moduleRegisters = $moduleRegisterControl->listItems( array('filters'=>array( 'name'=> 'Cogumelo' ) ));


    if( $regModuleInfo = $moduleRegisters->fetch() ) {
      $regModuleInfo->setter( 'deployVersion', static::checkCurrentVersion() );
      $regModuleInfo->save();
    }
    else {
      $reg = new ModuleRegisterModel( array('name'=>static::class ,'firstVersion'=> static::checkCurrentVersion(), 'deployVersion'=> static::checkCurrentVersion() ) );
      $reg->save();
    }

  }

  /**
  * Check last registered version
  */
  public static function checkRegisteredVersion() {
    devel::load('model/ModuleRegisterModel.php');
    $version = false;

    $moduleRegisterControl = new ModuleRegisterModel();
    $moduleRegisteredList = $moduleRegisterControl->listItems( array('filters'=>array( 'name'=>static::class  ) ));

    if( $regModuleInfo = $moduleRegisteredList->fetch() ) {
      $version = $regModuleInfo->getter('deployVersion');
    }

    return $version;
  }


  /**
  * Check current app version
  */
  public static function checkCurrentVersion() {
    return  Cogumelo::$version;
  }


}
