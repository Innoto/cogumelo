<?php
mediaserver::load('controller/LessController.php');


class ConfConstantsView {

  public function __construct() {

  }


  public function less() {
    $lessController = new LessController();

    $lessContent = $lessController->getLessFromSetup();

    header('Content-Type: text/less');
    echo $lessContent;
  }


  /**
   * Construimos dinamicamente el contenido de jsConfConstants.js con la información solicitada
   *
   * @global TYPE $MEDIASERVER_JAVASCRIPT_GLOBALS
   * @global TYPE $MEDIASERVER_JAVASCRIPT_CONSTANTS
   **/
  public function javascript(){
    $jsContent = '/* COGUMELO SETUP INFO */'."\n";

    $publicConfCode = '';
    $publicConfJs = Cogumelo::getSetupValue( 'mediaserver:publicConf:javascript:globalVars' );
    if( $publicConfJs && is_array( $publicConfJs ) && count( $publicConfJs ) > 0 ) {
      foreach( $publicConfJs as $globalKey ) {
        if( isset( $GLOBALS[ $globalKey ] ) ) {
          $jsValue = $this->valueToJs( $GLOBALS[ $globalKey ] );
          if( $jsValue !== null ) {
            $publicConfCode .= '  \''.$globalKey.'\': '.$jsValue.','."\n";
          }
        }
      }
    }
    $setupFields = Cogumelo::getSetupValue( 'mediaserver:publicConf:javascript:setupFields' );
    if( $setupFields && is_array( $setupFields ) && count( $setupFields ) > 0 ) {
      foreach( $setupFields as $setupField ) {
        $jsValue = $this->valueToJs( Cogumelo::getSetupValue( $setupField ) );
        if( $jsValue !== null ) {
          $publicConfCode .= '  \''. strtr( $setupField, ':', '_' ) .'\': '.$jsValue.','."\n";
        }
      }
    }
    $publicConfJs = Cogumelo::getSetupValue( 'mediaserver:publicConf:javascript:vars' );
    if( $publicConfJs && is_array( $publicConfJs ) && count( $publicConfJs ) > 0 ) {
      foreach( $publicConfJs as $name => $value ) {
        $jsValue = $this->valueToJs( $value );
        if( $jsValue !== null ) {
          $publicConfCode .= '  \''.$name.'\': '.$jsValue.','."\n";
        }
      }
    }
    if( $publicConfCode !== '' ) {
      $jsContent .= "\n".'var cogumelo = cogumelo || {};'."\n\n";
      $jsContent .= 'cogumelo.publicConf = {'."\n".
        rtrim( $publicConfCode, " ,\n\r" )."\n".
        '};'."\n\n";
    }


    global $MEDIASERVER_JAVASCRIPT_GLOBALS;
    if( is_array( $MEDIASERVER_JAVASCRIPT_GLOBALS ) && count( $MEDIASERVER_JAVASCRIPT_GLOBALS ) > 0 ) {
      foreach( $MEDIASERVER_JAVASCRIPT_GLOBALS as $globalKey ) {
        if( isset( $GLOBALS[ $globalKey ] ) ) {
          $jsValue = $this->valueToJs( $GLOBALS[ $globalKey ] );
          if( $jsValue !== null ) {
            $jsContent .= 'var GLOBAL_'.$globalKey.' = '.$jsValue.';'."\n";
          }
        }
      }
    }
    global $MEDIASERVER_JAVASCRIPT_CONSTANTS;
    if( is_array( $MEDIASERVER_JAVASCRIPT_CONSTANTS ) && count( $MEDIASERVER_JAVASCRIPT_CONSTANTS ) > 0 ) {
      foreach( $MEDIASERVER_JAVASCRIPT_CONSTANTS as $name => $value ) {
        $jsValue = $this->valueToJs( $value );
        if( $jsValue !== null ) {
          $jsContent .= 'var '.$name.' = '.$jsValue.';'."\n";
        }
      }
    }


    $jsContent .= '/* END COGUMELO SETUP INFO */'."\n";

    // Enviamos el contenido de jsConfConstants.js
    header('Content-Type: application/javascript');
    echo $jsContent;
  }


  private function valueToJs( $value ) {
    $jsValue = null;

    switch( gettype( $value ) ) {
      case 'integer':
      case 'double':
      case 'float':
        $jsValue = $value;
        break;

      case 'boolean':
        $jsValue = ($value) ? 'true' : 'false';
        break;

      case 'array':
        $arrAssoc = $this->isAssoc( $value );
        $arrResult = array();
        foreach( $value as $valueKey => $valueData ) {
          $valueDataJs = $this->valueToJs( $valueData );
          if( $valueDataJs !== null ) {
            $arrResult[] = ( $arrAssoc ) ? $valueKey.':'.$valueDataJs : $valueDataJs;
          }
          else {
            $arrResult = null;
            break;
          }
        }
        if( $arrResult !== null ) {
          $jsValue = ( $arrAssoc ) ? '{ '.implode( ', ', $arrResult ).' }' : '[ '.implode( ', ', $arrResult ).' ]';
        }
        break;

      case 'NULL':
        $jsValue = 'null';
        break;

      case 'object':
      case 'resource':
      case 'unknown type':
        break;

      case 'string':
      default:
        $jsValue = '\''.addslashes( $value ).'\'';
        break;
    }

    if( $jsValue === null ) {
      error_log( 'ConfConstantsView valueToJs: No hemos convertido este valor de tipo '.gettype( $value ) );
      error_log( print_r( $value, true ) );
    }
    return $jsValue;
  }

  private function isAssoc( $arr ) {
    return( array_keys($arr) !== range(0, count($arr) - 1) );
  }
}
