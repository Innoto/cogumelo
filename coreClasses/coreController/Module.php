<?php


/**
* Module Class
*
* This class is the abstract class that haritages the module classes
* Learn more about modules in https://github.com/Innoto/cogumelo/wiki/Cogumelo-basics#wiki-modules
*
* @author: pablinhob
*/


Cogumelo::load('coreController/ModuleController.php');

class Module
{
  private $urlPatterns = array();

  public $name = "";
  public $version = "";
  public $dependences = array();
  public $includesCommon = array();
  public $autoIncludeAlways = false;

  /**
  * Load module
  * @param string $load_path the path of module
  */
  public static function load( $load_path ) {
    $module_name = get_called_class();

    if( $file_to_include =  ModuleController::getRealFilePath('classes/'.$load_path.'', $module_name) ) {
      require_once($file_to_include);
    }
    else {
      Cogumelo::error("PHP File '".$load_path."'  not found in module : ".$module_name);
    }
  }


  /**
  * Set autoincludes
  */
  public static function autoIncludes() {

    // error_log( 'Module::autoincludes ' . get_called_class() );

    $dependencesControl = new DependencesController();
    $dependencesControl->loadModuleIncludes( get_called_class() );
  }

  /**
  * Load Dependences
  */
  public static function loadDependence( $idDependence, $installer = false ) {

    // error_log( 'Module::loadDependence ' . get_called_class() );

    $dependencesControl = new DependencesController();
    $dependencesControl->loadModuleDependence( get_called_class(), $idDependence, $installer );
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
      $version = round( $regModuleInfo->getter('deployVersion'), 3);
    }

    return $version;
  }


  /**
  * check current module version
  */
  public static function checkCurrentVersion() {
    eval('$instance = new '.static::class.'();');
    return $instance->version;
  }

  public function  moduleRC() {

  }

  public function  moduleDeploy() {

  }

//
// Metodos duplicados en CogumeloClass.php
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
// Metodos duplicados en CogumeloClass.php
//


}
