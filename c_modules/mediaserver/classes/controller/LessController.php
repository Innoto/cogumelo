<?php


class LessController {

  var $less = false;

  function __construct() {
    $this->less = new lessc();
  }

  /*
  * Compile less file
  * @return string : the path of compiled file
  * @var string $path: less file to compile
  */
  function compile( $lessFilePath, $resultFilePath , $moduleName ) {



    $this->setIncludesDir( $lessFilePath, $moduleName );

    try {
      $this->less->checkedCompile( COGUMELO_LOCATION.'/c_modules/'.$moduleName.'/classes/view/templates/'.$lessFilePath, $resultFilePath );
    } catch (Exception $ex) {
      Cogumelo::error( "less.php fatal error compiling ".basename($lessFilePath).": ".$ex->getMessage() );
    }
  }


  function setIncludesDir( $filePath , $moduleName){



    if($moduleName != false) {

      $coreModulePath = COGUMELO_LOCATION.'/c_modules/'; // core module
      $appModulePath = SITE_PATH.'/modules/'; // app module
    
      $this->less->setImportDir( $coreModulePath ); 

    }
    else {
      $appPath = SITE_PATH.'/classes/view/template/';
      $this->less->setImportDir( $appPath ); 
    }

    
  }

}