<?php
use Leafo\ScssPhp\Compiler;

class LessController {

  var $less = false;
  private $minimify = false;

  public function __construct() {
    // $this->less = new lessc();
  }

  /**
   * Compile less file
   *
   * @param string $lessFilePath: less file to compile
   *
   * @return boolean
   */
  public function compile( $lessFilePath, $resultFilePath, $moduleName, $lessTmpDir ) {
    // error_log( __METHOD__.' lessPath:'.$lessFilePath.', resPath:'.$resultFilePath.', module: '.$moduleName );

    $ret = true;

/*
    if( $this->less === false ) {
      $this->less = new lessc();
    }
*/
    if( $this->less === false ) {
      $this->less = new Compiler();
    }


    // set includes dir
    //$this->less->setImportDir( $lessTmpDir );
    $this->less->setImportPaths( $lessTmpDir );
//echo $lessTmpDir."\n";
    // set less variables (Defined in setup)
    //$this->less->setVariables( $this->getLessVarsFromSetup() ) ;

    if( $this->minimify ) {
      //$this->less->setFormatter('compressed');
    }

    try {
      //$this->less->checkedCompile( $lessTmpDir.$moduleName.'/classes/view/templates/'.$lessFilePath, $resultFilePath );
      file_put_contents($resultFilePath, $this->less->compile('@import "'.'/classes/view/templates/'.$lessFilePath.'";'));
    } catch (Exception $ex) {
      Cogumelo::error( "ScssPhp\Compile fatal error compiling ".basename($lessFilePath).": ".$ex->getMessage() );
      $ret = false;
    }

    return $ret;
  }

  public function setMinimify( $status = true ) {
    $this->minimify = !empty( $status );
  }

  public function getLessVarsFromSetup() {
    $lessVars = array();

    $lessData = [];

    $lessPC = Cogumelo::getSetupValue( 'mod:mediaserver:publicConf:scss' );

    $publicConf = !empty( $lessPC['globalVars'] ) ? $lessPC['globalVars'] : false;
    if( $publicConf && is_array( $publicConf ) && count( $publicConf ) > 0 ) {
      foreach( $publicConf as $globalKey ) {
        if( isset( $GLOBALS[ $globalKey ] ) ) {
          $lessValue = $this->valueToLess( $GLOBALS[ $globalKey ] );
          if( $lessValue !== null ) {
            $lessData[ $globalKey ] = $lessValue;
          }
        }
      }
    }

    $publicConf = !empty( $lessPC['setupFields'] ) ? $lessPC['setupFields'] : false;
    if( $publicConf && is_array( $publicConf ) && count( $publicConf ) > 0 ) {
      foreach( $publicConf as $setupField ) {
        $lessValue = $this->valueToLess( Cogumelo::getSetupValue( $setupField ) );
        if( $lessValue !== null ) {
          $lessData[ strtr( $setupField, ':', '_' ) ] = $lessValue;
        }
      }
    }

    $publicConf = !empty( $lessPC['vars'] ) ? $lessPC['vars'] : false;
    if( $publicConf && is_array( $publicConf ) && count( $publicConf ) > 0 ) {
      foreach( $publicConf as $name => $value ) {
        $lessValue = $this->valueToLess( $value );
        if( $lessValue !== null ) {
          $lessData[ $name ] = $lessValue;
        }
      }
    }

    foreach( $lessData as $key => $value ) {
      $lessVars[ 'cogumelo_publicConf_'.$key ] = $value;
    }

    //$lessVars[ 'cogumelo_media_app' ] = Cogumelo::getSetupValue( 'setup:appBasePath' ).'/classes/view/templates';
    $lessVars['cogumelo_app_templates'] = $this->valueToLess( Cogumelo::getSetupValue( 'setup:appBasePath' ).'/classes/view/templates' );

    return ( count( $lessVars ) > 0 ) ? $lessVars : false;
  }


  public function getLessFromSetup() {
    $lessContent = '/* COGUMELO SETUP CONSTANTS */'."\n";

    if( $lessVars = $this->getLessVarsFromSetup() ) {
      foreach( $lessVars as $name => $lessValue ) {
        $lessContent .= '$'.$name.' : '.$lessValue.';'."\n";
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
              // Cogumelo::debug( 'LessController->valueToLess: No conocemos como crear arrays asociativos en Less' );
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
          // Cogumelo::debug( 'LessController->valueToLess: No conocemos como crear arrays asociativos en Less' );
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
      Cogumelo::debug( __METHOD__.' - Unsupported type: '.gettype( $value ) );
    }

    return $lessValue;
  }


  private function isAssoc( $arr ) {
    return( array_keys($arr) !== range(0, count($arr) - 1) );
  }
}
