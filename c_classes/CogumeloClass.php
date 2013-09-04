<?php
/*
Cogumelo v0.5 - Innoto S.L.
Copyright (C) 2013 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
USA.
*/

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
    
        error_log( 	
        			'['. date('y-m-d H:i:s',time()) .'] ' .
        			'['. $_SERVER['REMOTE_ADDR'] .'] ' .
        			'[Session '. self::getUserInfo().'] ' . 
        			str_replace("\n", '\n', $texto)."\n", 3, LOGDIR.$fich_log.'.log' 
        );
    }

    // set an string with user information 
    function setUserInfo($userinfoString) {
    	$this->userinfoString = $userinfoString;
    }

    static function getUserInfo() {
    	//return $this->userinfoString;
    }

}

