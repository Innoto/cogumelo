<?php


/**
* ModuleController Class
*
* Controls all features of module system
* Learn more about modules in https://github.com/Innoto/cogumelo/wiki/Cogumelo-basics#wiki-modules
*
* @author: pablinhob
*/

require_once(COGUMELO_LOCATION."/coreClasses/coreController/RequestController.php");

class ModuleController
{

  var $url_path;
  var $module_paths = array();


  function __construct($url_path = false, $from_shell = false) {
    $this->url_path = $url_path;
    $this->setModules();

    $this->includeModules();

    if( !$from_shell ) {
      $this->execModules();
    }


  }


  function setModules() {
    global $C_ENABLED_MODULES;

    if (!is_array($C_ENABLED_MODULES)) {
      return;
    }

    foreach ($C_ENABLED_MODULES as $module_name) {
      if( $module_main_class = self::getRealFilePath($module_name.'.php' ,$module_name) ) {
        $this->module_paths[$module_name] = dirname($module_main_class); // get module.php container
      }
      else {
        $this->module_paths[$module_name] = false;
        Cogumelo::error("Module not found: ".$module_name);
      }
    }

  }


  function execModules() {
    global $C_INDEX_MODULES;

    foreach($C_INDEX_MODULES as $module_name) {
      $this->execModule($module_name);
    }
  }

  function execModule($module_name) {
    if($this->module_paths[$module_name] == false) {
      Cogumelo::error("Module '".$module_name. "' not found.");
    }
    else {
      $modulo = new $module_name();
      $this->request = new RequestController( $modulo->getUrlPatternsToArray(), $this->url_path, $this->module_paths[$module_name] );
      $this->url_path = $this->request->getLeftoeverUrl();
      Cogumelo::debug("Reading UrlPatterns from: ".$module_name);
    }
  }

  function includeModules() {

    global $C_ENABLED_MODULES;

    foreach($C_ENABLED_MODULES as $module_name) {
      $mod_path = $this->module_paths[$module_name];
      require_once($mod_path.'/'.$module_name.'.php');
    }

  }

  function getLeftUrl() {
    return $this->url_path;
  }


  static function getRealFilePath($file_relative_path, $module = false) {
    $retPath = false;

    if(!$module) {
      $retPath = SITE_PATH.$file_relative_path;
    }
    else {
      global $C_ENABLED_MODULES;
      if(in_array($module, $C_ENABLED_MODULES)) {
        // APP modules
        if( file_exists(SITE_PATH.'/modules/'.$module.'/'.$file_relative_path) ) { 
          $retPath = SITE_PATH.'/modules/'.$module.'/'.$file_relative_path;
        }
        // DIST modules
        else if( COGUMELO_DIST_LOCATION != false && file_exists( COGUMELO_DIST_LOCATION.'/distModules/'.$module.'/'.$file_relative_path ) ) {
          $retPath = COGUMELO_DIST_LOCATION.'/distModules/'.$module.'/'.$file_relative_path;
        }        
        // CORE modules
        else if( file_exists( COGUMELO_LOCATION.'/coreModules/'.$module.'/'.$file_relative_path ) ) {
          $retPath = COGUMELO_LOCATION.'/coreModules/'.$module.'/'.$file_relative_path;
        }
        else {
          Cogumelo::error("ModuleController: '".$file_relative_path."'' not found into module '".$module."' ");
        }

      }
      else {
        Cogumelo::error('ModuleController: Module named as "'.$module.'" is not enabled. Add it to $C_ENABLED_MODULES setup.php array' );
      }
    }
    return $retPath;
  }


}
