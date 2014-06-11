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

    // set url patterns
    $this->url_patterns = $this->setUrlPatterns();

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

    if(!file_exists($file_path)) {
        Cogumelo::error("PHP File not found : ".$file_path);
    }
    else {
        require_once $file_path;
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
    
    if($_SERVER['REQUEST_URI'] != "/devel/read_logs") {
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
  //  Debug
  //
/*
  static function objDebugObjectCreate($obj) {
    return array(
        "creation_date" => getdate(),
        "data" => $obj
      );
  }

  static function objDebugPull() {

    // Garbage collerctor
  }

  static function objDebugPush($obj) {
    if(DEBUG && isset($obj)){

    }
  }
*/
  // manages a session array, setting an array of X debug objectsor an error message
  static function debug_object_list($debug_obj=false) {

    $array_size_for_debpage = 10;

    if(DEBUG){
      if($debug_obj){ // developer only want to get session array, dont put into
        if(isset($_SESSION['cogumelo_dev_obj_array'])){
          $session_array = unserialize($_SESSION['cogumelo_dev_obj_array']); 
        }
        else {
          $session_array = false;
        }
      }
      else {
       if( !isset($_SESSION['cogumelo_dev_obj_array']) ){ // session doesnt exist, create array
          $session_array  = array();

          for($c=1; $c <= $array_size_for_debpage; $c++){
            $session_array[$c]  = false;
          }
        }
        else {
          $session_array = unserialize($_SESSION['cogumelo_dev_obj_array']); // session exist, get session array
        }

        for($c=1; $c < $array_size_for_debpage ; $c++){
          $session_array[$c+1] = $_SESSION['cogumelo_dev_obj_array'][$c];
        }

        $session_array[1] = $debug_obj;

        $_SESSION['cogumelo_dev_obj_array'] = serialize($session_array);
      }
    }
    else // DEBUG is OFF, return false;
    {
      $session_array = false; 
    }
    

    return $session_array;

  }
}

