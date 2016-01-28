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


  public function javascript(){
    $jsContent = '/* COGUMELO SETUP CONSTANTS */'."\n";

    global $MEDIASERVER_JAVASCRIPT_GLOBALS, $MEDIASERVER_JAVASCRIPT_CONSTANTS;

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

    if( is_array( $MEDIASERVER_JAVASCRIPT_CONSTANTS ) && count( $MEDIASERVER_JAVASCRIPT_CONSTANTS ) > 0 ) {
      foreach( $MEDIASERVER_JAVASCRIPT_CONSTANTS as $name => $value ) {
        $jsValue = $this->valueToJs( $value );
        if( $jsValue !== null ) {
          $jsContent .= 'var '.$name.' = '.$jsValue.';'."\n";
        }
      }
    }
    $jsContent .= '/* END SETUP CONSTANTS */'."\n";


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
