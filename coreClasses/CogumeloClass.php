<?php

require_once( COGUMELO_LOCATION.'/coreClasses/coreController/Singleton.php' );
require_once( COGUMELO_LOCATION.'/coreClasses/coreController/ModuleController.php' );
require_once( COGUMELO_LOCATION.'/coreClasses/coreController/DependencesController.php' );
require_once( COGUMELO_LOCATION.'/coreClasses/coreController/I18n.php' );
require_once( COGUMELO_LOCATION.'/coreModules/cogumeloSession/classes/controller/CogumeloSessionController.php' );

class CogumeloClass extends Singleton {

  public $request;
  public $modules;

  private $urlPatterns;

  protected $userinfoString = '';


  public $dependences = array();
  public $includesCommon = array();

  // main dependences for cogumelo framework
  static $mainDependences = array(
     array(
       'id' => 'phpmailer',
       'params' => array( 'phpmailer/phpmailer', '5.2.14' ),
       'installer' => 'composer',
       'includes' => array('class.phpmailer.php')
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
       'params' => array('smarty/smarty', '3.1.18'),
       'installer' => 'composer',
       'includes' => array('libs/Smarty.class.php')
     ),
     array(
       'id' => 'jquery',
       'params' => array('jQuery#2.2'),
       'installer' => 'bower',
       'includes' => array()
     ),
     array(
       'id' => 'gettext',
       'params' => array('Gettext'),
       'installer' => 'manual',
       'includes' => array('')
     ),
     array(
       'id' => 'smarty-gettext',
       'params' => array('smarty-gettext/smarty-gettext', '~1.1.1'),
       'installer' => 'composer',
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
     ),
     array(
       'id' => 'php-jwt',
       'params' => array('firebase/php-jwt', '3.*'),
       'installer' => 'composer',
       'includes' => array('src/JWT.php')
     )
  );

  // Set autoincludes
  public static function autoIncludes() {
    $dependencesControl = new DependencesController();
    $dependencesControl->loadAppIncludes();
  }


  public static function get(){
    return parent::getInstance('Cogumelo');
  }

  public function __construct() {
    $sessionCtrl = new CogumeloSessionController();
    $sessionCtrl->prepareTokenSessionEnvironment();

    session_start();
    global $C_SESSION_ID;
    $C_SESSION_ID = session_id();

    /*
      $tkName = 'CGMLTOKENSESSID';
      session_name( $tkName );

      // -H "Authorization: Bearer mytoken123"
      // https://tools.ietf.org/html/rfc1945#section-11

      if( !isset( $_COOKIE[ $tkName ] ) ) {
        if( isset( $_POST[ $tkName ] ) && trim( $_POST[ $tkName ] ) !== '' ) {
          session_id( $_POST[ $tkName ] );
        }
        elseif( isset( $_SERVER[ 'HTTP_X_'.$tkName ] ) && trim( $_SERVER[ 'HTTP_X_'.$tkName ] ) !== '' ) {
          session_id( $_SERVER[ 'HTTP_X_'.$tkName ] );
        }
      }
    */

    /*
      var formData = new FormData();

      // Por POST
      // formData.append( 'CGMLTOKENSESSID', 'MEU-POST-2lv80fl591mpjpm04' );

      $.ajax({
        url: '/cgml-session.json', type: 'POST',

        // Por HEADER
        headers: {'X-CGMLTOKENSESSID': 'MEU-HEAD-rropjpm042lv80fl5'},

        data: formData, cache: false, contentType: false, processData: false,
        success: function setStatusSuccess( $jsonData, $textStatus, $jqXHR ) {
          console.log( 'jsonData: ', $jsonData );
          //console.log( $jsonData.status );
        }
      });
    */
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

    if(Cogumelo::getSetupValue( 'logs:debug' )){
      //self::console(debug_backtrace(), $error_msg );
    }

    self::error($error_msg);
  }

  public static function errorHandler() {

    $last_error = error_get_last();

    if($last_error!=null) {
      $error_msg = 'Fatal error: '.$last_error['message'].' on file "'.$last_error['file'].'" line: '.$last_error['line'];
      if( Cogumelo::getSetupValue( 'logs:debug' ) ) {
        //self::console($last_error, $error_msg);
      }
      self::error($error_msg);
    }
  }

  //
  //  LOGS
  //
  public static function error( $description ) {
    if( Cogumelo::getSetupValue( 'logs:error' ) === true ) {
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
    if( Cogumelo::getSetupValue( 'logs:debug' ) === true ) {
      self::log($description, 'cogumelo_debug_sql');
    }
  }

  public static function log( $texto, $fich_log = 'cogumelo' ) {
    global $COGUMELO_DISABLE_LOGS;
    $ignore = false;


    if( !$COGUMELO_DISABLE_LOGS ) {

      // // Rodeo para evitar "PHP Notice:  Use of undefined constant MOD_DEVEL_URL_DIR"
      // $arrayDefines = get_defined_constants();
      // if(
      //   $_SERVER['REQUEST_URI'] != '/'.$arrayDefines['MOD_DEVEL_URL_DIR'].'/read_logs' &&
      //   $_SERVER['REQUEST_URI'] != '/'.$arrayDefines['MOD_DEVEL_URL_DIR'].'/get_debugger'
      // ) {
      //   $ignore = true;
      // }

      // if( $ignore ) {

      $develUrl = Cogumelo::getSetupValue( 'mod:devel:url' );
      if( $develUrl &&
        $_SERVER['REQUEST_URI'] != '/'.$develUrl.'/read_logs' &&
        $_SERVER['REQUEST_URI'] != '/'.$develUrl.'/get_debugger'
      ) {
        error_log(
          '['. date('y-m-d H:i:s',time()) .'] ' .
          '['. $_SERVER['REMOTE_ADDR'] .'] ' .
          '[Session '. self::getUserInfo().'] ' .
          str_replace("\n", '\n', $texto)."\n", 3, Cogumelo::getSetupValue( 'logs:path' ).'/'.$fich_log.'.log'
        );
      }
    }
  }

  public static function disableLogs() {
    global $COGUMELO_DISABLE_LOGS;
    $COGUMELO_DISABLE_LOGS = true;
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



  public static function setSetupValue( $path, $value ) {
    // error_log( 'COGUMELO::setSetupValue: '.$path );
    global $CGMLCONF;

    if( !isset( $CGMLCONF ) || !is_array( $CGMLCONF ) ) {
      $CGMLCONF = array(
        'cogumelo' => array()
      );
    }

    $parts = explode( ':', $path );
    $stack = '';
    foreach( $parts as $key ) {
      $valid = false;
      $stackPrev = $stack;
      $stack .= '[\''.$key.'\']';
      $fai = '$valid = isset( $CGMLCONF'. $stack .');';
      eval( $fai );
      if( !$valid ) {
        $fai = '$isArray = is_array( $CGMLCONF'. $stackPrev .');';
        eval( $fai );
        if( $isArray ) {
          $fai = '$CGMLCONF'. $stack .' = null;';
          eval( $fai );
        }
        else {
          $fai = '$CGMLCONF'. $stackPrev .' = array( $key => null );';
          eval( $fai );
        }
      }
    }
    $fai = '$CGMLCONF'. $stack .' = $value;';
    eval( $fai );

    return $CGMLCONF;
  }

  public static function getSetupValue( $path = '' ) {
    // error_log( 'Cogumelo::getSetupValue: '.$path );
    global $CGMLCONF;
    $value = null;

    $parts = explode( ':', $path );
    $stack = ( $parts[0] === '' ) ? '' : '[\'' . implode( '\'][\'', $parts ) . '\']';
    $fai = '$valid = isset( $CGMLCONF'. $stack .' );';
    eval( $fai );
    if( $valid ) {
      $fai = '$value = $CGMLCONF'. $stack .';';
      eval( $fai );
    }

    return $value;
  }


  /**
  * un-register the app
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
  * register or update app register
  */
  public static function register() {

    devel::load('model/ModuleRegisterModel.php');

    $moduleRegisterControl = new ModuleRegisterModel();
    $moduleRegisters = $moduleRegisterControl->listItems( array('filters'=>array( 'name'=> static::class ) ));


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
  * check last registered version
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
  * check current app version
  */
  public static function checkCurrentVersion() {
    return  Cogumelo::$version;
  }


}
