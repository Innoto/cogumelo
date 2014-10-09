<?php


/**
* Module Class
*
* This class is the abstract class that haritages the module classes
* Learn more about modules in https://github.com/Innoto/cogumelo/wiki/Cogumelo-basics#wiki-modules
*
* @author: pablinhob
*/


Cogumelo::load('c_controller/ModuleController');

class Module
{
  private $urlPatterns = array();
  
  public $name = "";
  public $version = "";
  public $dependences = array();
  public $includesCommon = array();


  /**
  * @param string $load_path the path of module
  */
  static function load($load_path) {
    $module_name = get_called_class();

    if($file_to_include =  ModuleController::getRealFilePath('classes/'.$load_path.'.php', $module_name)) {
      require_once($file_to_include);
    }
    else {
      Cogumelo::error("PHP File '".$load_path."'  not found in module : ".$module_name);
    }
  }


  // Set autoincludes 
  static function autoIncludes() {
    $dependencesControl = new DependencesController();
    $dependencesControl->loadModuleIncludes( get_called_class() );
  }


//
// Metodos duplicados en CogumeloClass.php
// (Ini)

  function deleteUrlPatterns() {
    $this->urlPatterns = array();
  }

  function addUrlPatterns( $regex, $destination ) {
    $this->urlPatterns[ $regex ] = $destination;
  }

  function setUrlPatternsFromArray( $arrayUrlPatterns ) {
    $this->deleteUrlPatterns();
    foreach ($arrayUrlPatterns as $key => $value) {
      $this->addUrlPatterns( $key, $value );
    }
  }

  function getUrlPatternsToArray() {
    return $this->urlPatterns;
  }

// (Fin)
// Metodos duplicados en CogumeloClass.php
//


}