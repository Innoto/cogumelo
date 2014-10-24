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

    // generate less caches
    $lessTmpDir = CacheUtilsController::prepareLessTmpdir();

    // set includes dir
    $this->less->setImportDir( $lessTmpDir );

    try {
      $this->less->checkedCompile( $lessTmpDir.$moduleName.'/classes/view/templates/'.$lessFilePath, $resultFilePath );
    } catch (Exception $ex) {
      Cogumelo::error( "less.php fatal error compiling ".basename($lessFilePath).": ".$ex->getMessage() );
    }
    // remove temporal files
    //self::removeLessTmpdir( );
  }

}