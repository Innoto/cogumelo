<?php


class LessController {

  var $less = false;

  public function __construct() {
    $this->less = new lessc();
  }

  /**
   * Compile less file
   *
   * @param string $lessFilePath: less file to compile
   *
   * @return boolean
   */
  public function compile( $lessFilePath, $resultFilePath, $moduleName ) {
    $ret = true;

    /*
    if( $this->less === false ) {
      $this->less = new lessc();
    }
    */

    // generate less caches
    $lessTmpDir = CacheUtilsController::prepareLessTmpdir();

    // set includes dir
    $this->less->setImportDir( $lessTmpDir );

    // set less variables (Defined in setup)
    /*
    global $MEDIASERVER_LESS_CONSTANTS;
    $this->less->setVariables( $MEDIASERVER_LESS_CONSTANTS ) ;
    */
    $this->less->setVariables( $this->getLessVarsFromSetup() ) ;


    try {
      $this->less->checkedCompile( $lessTmpDir.$moduleName.'/classes/view/templates/'.$lessFilePath, $resultFilePath );
    } catch (Exception $ex) {
      Cogumelo::error( "less.php fatal error compiling ".basename($lessFilePath).": ".$ex->getMessage() );
      $ret = false;
    }

    return $ret;
  }


  public function getLessVarsFromSetup() {
    $lessVars = array();

    global $MEDIASERVER_LESS_GLOBALS, $MEDIASERVER_LESS_CONSTANTS;

    if( is_array( $MEDIASERVER_LESS_GLOBALS ) && count( $MEDIASERVER_LESS_GLOBALS ) > 0 ) {
      foreach( $MEDIASERVER_LESS_GLOBALS as $globalKey ) {
        if( isset( $GLOBALS[ $globalKey ] ) ) {
          $lessValue = $this->valueToLess( $GLOBALS[ $globalKey ] );
          if( $lessValue !== null ) {
            $lessVars[ 'GLOBAL_'.$globalKey ] = $lessValue;
          }
        }
      }
    }

    if( is_array( $MEDIASERVER_LESS_CONSTANTS ) && count( $MEDIASERVER_LESS_CONSTANTS ) > 0 ) {
      foreach( $MEDIASERVER_LESS_CONSTANTS as $name => $value ) {
        //$lessContent .= '@'.$name.' : "'.$value.'";'."\n";
        $lessValue = $this->valueToLess( $value );
        if( $lessValue !== null ) {
          $lessVars[ $name ] = $lessValue;
        }
      }
    }

    return ( count( $lessVars ) > 0 ) ? $lessVars : false;
  }


  public function getLessFromSetup() {
    $lessContent = '/* COGUMELO SETUP CONSTANTS */'."\n";

    if( $lessVars = $this->getLessVarsFromSetup() ) {
      foreach( $lessVars as $name => $lessValue ) {
        $lessContent .= '@'.$name.' : '.$lessValue.';'."\n";
      }
    }

    $lessContent .= '/* END SETUP CONSTANTS */'."\n";

    return $lessContent;
  }


  public function valueToLess( $value ) {
    $lessValue = null;

    switch( gettype( $value ) ) {
      case 'integer':
      case 'double':
      case 'float':
        $lessValue = $value;
        break;

      case 'boolean':
        $lessValue = ($value) ? 'true' : 'false';
        break;

      case 'array':
        $arrAssoc = $this->isAssoc( $value );
        $arrResult = array();
        if( !$arrAssoc ) { // No conocemos como crear arrays asociativos en Less
          foreach( $value as $valueKey => $valueData ) {
            if( is_array( $valueData ) ) { // No conocemos como crear arrays anidados en Less
              error_log( 'ConfConstantsView: No conocemos como crear arrays asociativos en Less' );
              $arrResult = null;
              break;
            }
            $valueDataLess = $this->valueToLess( $valueData );
            if( $valueDataLess === null ) {
              $arrResult = null;
              break;
            }
            $arrResult[] = $valueDataLess;
          }
        }
        else {
          $arrResult = null;
          error_log( 'ConfConstantsView: No conocemos como crear arrays asociativos en Less' );
        }

        if( $arrResult !== null ) {
          $lessValue = implode( ', ', $arrResult );
        }
        break;

      case 'NULL':
        $lessValue = 'null';
        break;

      case 'object':
      case 'resource':
      case 'unknown type':
        break;

      case 'string':
      default:
        $lessValue = '\''.addslashes( $value ).'\'';
        break;
    }

    if( $lessValue === null ) {
      error_log( 'ConfConstantsView valueToLess: No hemos convertido este valor de tipo '.gettype( $value ) );
      error_log( print_r( $value, true ) );
    }
    return $lessValue;
  }


  private function isAssoc( $arr ) {
    return( array_keys($arr) !== range(0, count($arr) - 1) );
  }
}
