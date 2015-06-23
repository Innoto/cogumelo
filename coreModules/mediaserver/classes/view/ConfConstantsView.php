<?php

class ConfConstantsView {


  public function __construct() {

  }


  public function less(){

    $lessContent = '/* COGUMELO SETUP CONSTANTS */'."\n";

    global $MEDIASERVER_LESS_GLOBALS, $MEDIASERVER_LESS_CONSTANTS;

    if( is_array( $MEDIASERVER_LESS_GLOBALS ) && count( $MEDIASERVER_LESS_GLOBALS ) > 0 ) {
      foreach( $MEDIASERVER_LESS_GLOBALS as $globalKey ) {
        if( isset( $GLOBALS[ $globalKey ] ) ) {
          $lessValue = $this->valueToLess( $GLOBALS[ $globalKey ] );
          if( $lessValue !== null ) {
            $lessContent .= '@GLOBAL_'.$globalKey.' : '.$lessValue.';'."\n";
          }
        }
      }
    }

    if( is_array( $MEDIASERVER_LESS_CONSTANTS ) && count( $MEDIASERVER_LESS_CONSTANTS ) > 0 ) {
      foreach( $MEDIASERVER_LESS_CONSTANTS as $name => $value ) {
        //$lessContent .= '@'.$name.' : "'.$value.'";'."\n";
        $lessValue = $this->valueToLess( $value );
        if( $lessValue !== null ) {
          $lessContent .= '@'.$name.' : '.$lessValue.';'."\n";
        }
      }
    }
    $lessContent .= '/* END SETUP CONSTANTS */'."\n";


    header('Content-Type: text/less');
    echo $lessContent;
  }


  public function javascript(){

    error_log( print_r( array_keys( $GLOBALS ), true ) );

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


  private function valueToLess( $value ) {
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
            $valueDataJs = $this->valueToJs( $valueData );
            if( $valueDataJs === null ) {
              $arrResult = null;
              break;
            }
            $arrResult[] = ( $arrAssoc ) ? $valueKey.':'.$valueDataJs : $valueDataJs;
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