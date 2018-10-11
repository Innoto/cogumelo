<?php
use Leafo\ScssPhp\Compiler;

class ScssController {

  var $scss = false;
  private $minimify = false;

  public function __construct() {

  }

  /**
   * Compile scss file
   *
   * @param string $scssFilePath: scss file to compile
   *
   * @return boolean
   */
  public function compile( $scssFilePath, $resultFilePath, $moduleName, $scssTmpDir ) {
    // error_log( __METHOD__.' scssPath:'.$scssFilePath.', resPath:'.$resultFilePath.', module: '.$moduleName );
    $ret = true;
/*
    if( $this->scss === false ) {
      $this->scss = new scssc();
    }
*/
    if( $this->scss === false ) {
      $this->scss = new Compiler();
    }

    // set scss variables (Defined in setup)
    $this->scss->setVariables( $this->getScssVarsFromSetup() ) ;

    // set includes dir
    //$this->scss->setImportDir( $scssTmpDir );
    $this->scss->setImportPaths( $scssTmpDir );

    if( $this->minimify ) {
      //$this->scss->setFormatter('compressed');
    }

    try {
      //$this->scss->checkedCompile( $scssTmpDir.$moduleName.'/classes/view/templates/'.$scssFilePath, $resultFilePath );

      if( $moduleName ) {
        $m = '/'.$moduleName;
      }
      else {
        $m = '';
      }

//echo "\n".'@import "'.$m.'/classes/view/templates/'.$scssFilePath.'";';

      $InitialScssToCompile =
        $this->getScssFromSetup() .
        '@import "'.$m.'/classes/view/templates/'.$scssFilePath.'";';

      file_put_contents(
        $resultFilePath,
        $this->scss->compile($InitialScssToCompile)
      );
    } catch (Exception $ex) {
      Cogumelo::error( "ScssPhp\Compile error: ".basename($scssFilePath).": ".$ex->getMessage() );
      $ret = false;
    }

    return $ret;
  }

  public function setMinimify( $status = true ) {
    $this->minimify = !empty( $status );
  }

  public function getScssVarsFromSetup() {
    $scssVars = array();

    $scssData = [];

    $scssPC = Cogumelo::getSetupValue( 'mod:mediaserver:publicConf:scss' );

    $publicConf = !empty( $scssPC['globalVars'] ) ? $scssPC['globalVars'] : false;
    if( $publicConf && is_array( $publicConf ) && count( $publicConf ) > 0 ) {
      foreach( $publicConf as $globalKey ) {
        if( isset( $GLOBALS[ $globalKey ] ) ) {
          $scssValue = $this->valueToScss( $GLOBALS[ $globalKey ] );
          if( $scssValue !== null ) {
            $scssData[ $globalKey ] = $scssValue;
          }
        }
      }
    }

    $publicConf = !empty( $scssPC['setupFields'] ) ? $scssPC['setupFields'] : false;
    if( $publicConf && is_array( $publicConf ) && count( $publicConf ) > 0 ) {
      foreach( $publicConf as $setupField ) {
        $scssValue = $this->valueToScss( Cogumelo::getSetupValue( $setupField ) );
        if( $scssValue !== null ) {
          $scssData[ strtr( $setupField, ':', '_' ) ] = $scssValue;
        }
      }
    }

    $publicConf = !empty( $scssPC['vars'] ) ? $scssPC['vars'] : false;
    if( $publicConf && is_array( $publicConf ) && count( $publicConf ) > 0 ) {
      foreach( $publicConf as $name => $value ) {
        $scssValue = $this->valueToScss( $value );
        if( $scssValue !== null ) {
          $scssData[ $name ] = $scssValue;
        }
      }
    }

    foreach( $scssData as $key => $value ) {
      $scssVars[ 'cogumelo_publicConf_'.$key ] = $value;
    }

    //$scssVars[ 'cogumelo_media_app' ] = Cogumelo::getSetupValue( 'setup:appBasePath' ).'/classes/view/templates';
    $scssVars['cogumelo_app_templates'] = $this->valueToScss( Cogumelo::getSetupValue( 'setup:appBasePath' ).'/classes/view/templates' );

    return ( count( $scssVars ) > 0 ) ? $scssVars : false;
  }


  public function getScssFromSetup() {
    $scssContent = '/* COGUMELO SETUP CONSTANTS */'."\n";

    if( $scssVars = $this->getScssVarsFromSetup() ) {
      foreach( $scssVars as $name => $scssValue ) {
        $scssContent .= '$'.$name.' : '.$scssValue.';'."\n";
      }
    }

    $scssContent .= '/* END SETUP CONSTANTS */'."\n";

    return $scssContent;
  }


  public function valueToScss( $value ) {
    $scssValue = null;

    switch( gettype( $value ) ) {
      case 'integer':
      case 'double':
      case 'float':
        $scssValue = $value;
        break;

      case 'boolean':
        $scssValue = ($value) ? 'true' : 'false';
        break;

      case 'array':
        $arrAssoc = $this->isAssoc( $value );
        $arrResult = array();
        if( !$arrAssoc ) {
          foreach( $value as $valueKey => $valueData ) {
            if( is_array( $valueData ) ) {

              $arrResult = null;
              break;
            }
            $valueDataScss = $this->valueToScss( $valueData );
            if( $valueDataScss === null ) {
              $arrResult = null;
              break;
            }
            $arrResult[] = $valueDataScss;
          }
        }
        else {
          $arrResult = null;
        }

        if( $arrResult !== null ) {
          $scssValue = implode( ', ', $arrResult );
        }
        break;

      case 'NULL':
        $scssValue = 'null';
        break;

      case 'object':
      case 'resource':
      case 'unknown type':
        break;

      case 'string':
      default:
        $scssValue = '\''.addslashes( $value ).'\'';
        break;
    }

    if( $scssValue === null ) {
      Cogumelo::debug( __METHOD__.' - Unsupported type: '.gettype( $value ) );
    }

    return $scssValue;
  }


  private function isAssoc( $arr ) {
    return( array_keys($arr) !== range(0, count($arr) - 1) );
  }
}
