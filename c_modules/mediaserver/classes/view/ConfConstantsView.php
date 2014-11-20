<?php

class ConfConstantsView {


  function __construct() {

  }


  function less(){

    global $MEDIASERVER_LESS_CONSTANTS;

    header('Content-Type: text/less');
    echo '/* COGUMELO SETUP CONSTANTS */'."\n";
    if( sizeof($MEDIASERVER_LESS_CONSTANTS) > 0 ) {
      foreach ($MEDIASERVER_LESS_CONSTANTS as $name => $value) {
        echo '@'.$name.' : "'.$value.'"'."\n";
      }
    }
    echo '/* END SETUP CONSTANTS */'."\n";
  }


  function javascript(){

    global $MEDIASERVER_JAVASCRIPT_CONSTANTS;

    header('Content-Type: application/javascript');
    
    echo '/* COGUMELO SETUP CONSTANTS */'."\n";
    if( sizeof($MEDIASERVER_JAVASCRIPT_CONSTANTS) > 0 ) {
      foreach ($MEDIASERVER_JAVASCRIPT_CONSTANTS as $name => $value) {
        if( is_string( $value ) ) {
          echo 'var '.$name.' = "'.$value.'"'."\n";
        }
        else {
          echo 'var '.$name.' = '.$value.''."\n";
        }
      }
    }
    echo '/* END SETUP CONSTANTS */'."\n";
    
  }

}