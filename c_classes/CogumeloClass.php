<?php

require_once(COGUMELO_LOCATION."/c_classes/c_controller/Singleton.php");
require_once(COGUMELO_LOCATION."/c_classes/c_controller/ModuleController.php");

class CogumeloClass extends Singleton
{
  public $request;
  public $modules;
  public $url_patterns;

  protected $userinfoString = "";

  static function get(){
      return parent::getInstance('Cogumelo');
  }

	public function __construct() {

        session_start();

	}


  function exec(){

    Cogumelo::debug("Request URI: ".$_SERVER["REQUEST_URI"]);

    // cut out the SITE_FOLDER and final slash from path 
    $url_path = preg_replace('#\/$#', '', preg_replace('#^'.SITE_FOLDER.'#', '', $_SERVER["REQUEST_URI"], 1) , 1);

    // modules
    $this->modules = new ModuleController( $url_path );
    $url_path_after_modules = $this->modules->getLeftUrl();

    // main request controller
    self::load('c_controller/RequestController');
    $this->request = new RequestController($this->url_patterns, $url_path_after_modules );
  }  

  //
  //  Auto include 
  //

  static function load($classname) {


    if(preg_match('#^c_vendor/#', $classname)){
      $file_path = COGUMELO_LOCATION.'/c_vendor/'.preg_replace('#^c_vendor/#', '', $classname);
    }
    else
    if(preg_match('#^c_#', $classname)){ 
      $filename =  $classname . '.php';
      $file_path = COGUMELO_LOCATION.'/c_classes/'.$filename;
    }
    else
    if(preg_match('#^vendor/#', $classname)){
      $file_path = SITE_PATH.$classname;
    }
    else { 
      $filename =  $classname . '.php';
      $file_path = SITE_PATH. 'classes/'. $filename;
    }


    // check if file exist
    if(!file_exists($file_path)) {
      Cogumelo::error("PHP File not found : ".$file_path);
    }
    else {
      require_once $file_path;
    }
  }


  //
  //  Error Handler
  //
  static function warningHandler( $errno, $errstr, $errfile, $errline) {

    $error_msg = "Warning: $errstr on file '$errfile' line:$errline";

    if(DEBUG){
      self::objDebug(debug_backtrace(), $error_msg );
    }

    self::error($error_msg);
  }

  static function errorHandler() {

    $last_error=error_get_last(); 
    
    if($last_error!=null) {
      $error_msg = "Fatal error: ".$last_error['message']." on file '".$last_error['file']." ' line: ".$last_error['line'];
      if(DEBUG) {
        self::objDebug($last_error, $error_msg);
      }
      self::error($error_msg);
    }  
  }

	//
	//	LOGS
	//

	static function error($description)	{
		if(ERRORS == true) {
			echo "<br>Cogumelo error: ".$description;
    }

		self::log($description, 'cogumelo_error');
	}

  static function debug($description) {
    if(DEBUG == true) {
      self::log($description, 'cogumelo_debug');
    }
  }

  static function log( $texto, $fich_log='cogumelo' ) {
    
    if($_SERVER['REQUEST_URI'] != "/devel/read_logs" && $_SERVER['REQUEST_URI'] != "/devel/get_debugger") {
      error_log( 	
      	'['. date('y-m-d H:i:s',time()) .'] ' .
    		'['. $_SERVER['REMOTE_ADDR'] .'] ' .
    		'[Session '. self::getUserInfo().'] ' . 
    		str_replace("\n", '\n', $texto)."\n", 3, LOGDIR.$fich_log.'.log' 
      );
    }
  }

  // set an string with user information 
  function setUserInfo($userinfoString) {
    $this->userinfoString = $userinfoString;
  }

  static function getUserInfo() {
  	//return $this->userinfoString;
  }




  //
  //  Advanced Object Debug
  //

  static function objDebugObjectCreate($obj, $comment) {

    return array(
        "comment" => $comment,
        "creation_date" => getdate(),
        "data" => $obj
      );
  }

  static function objDebugPull() {
    $now = getdate();
    $debug_object_maxlifetime = 60; // in seconds
    $result_array = array();

    if( DEBUG && 
        isset($_SESSION['cogumelo_dev_obj_array'])  &&
        $_SESSION['cogumelo_dev_obj_array'] != "" &&
        $_SESSION['cogumelo_dev_obj_array'] != null &&
        is_array(unserialize($_SESSION['cogumelo_dev_obj_array'])) 
      ) {
      
      $session_array = unserialize( $_SESSION['cogumelo_dev_obj_array'] );

      if(is_array($session_array) && sizeof($session_array) > 0 ) {
        foreach ($session_array as $session_obj) {
          if( isset($session_obj['creation_date']) && ( $now[0] - $session_obj['creation_date'][0]) <= $debug_object_maxlifetime  ){
            array_push($result_array, $session_obj);
          }
        }
      }

      // reset sesesion array
      $_SESSION['cogumelo_dev_obj_array'] = array();
    }

    return $result_array;
  }

  static function objDebug($obj, $comment="") {
    return self::objDebugPush($obj, $comment);
  }

  static function objDebugPush($obj, $comment) {
    if(DEBUG && isset($obj)){

      $session_array = array();

      if( isset($_SESSION['cogumelo_dev_obj_array']) &&
          $_SESSION['cogumelo_dev_obj_array'] != "" &&
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

}

