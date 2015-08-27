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
    $ret = true;

    // generate less caches
    $lessTmpDir = CacheUtilsController::prepareLessTmpdir();

    // set includes dir
    $this->less->setImportDir( $lessTmpDir );

    // set less variables (Defined in setup)
    global $MEDIASERVER_LESS_CONSTANTS;

    $this->less->setVariables( $MEDIASERVER_LESS_CONSTANTS ) ;

    try {
      $this->less->checkedCompile( $lessTmpDir.$moduleName.'/classes/view/templates/'.$lessFilePath, $resultFilePath );
    } catch (Exception $ex) {
      Cogumelo::error( "less.php fatal error compiling ".basename($lessFilePath).": ".$ex->getMessage() );
      $ret = false;
    }

    return $ret;
  }

}
