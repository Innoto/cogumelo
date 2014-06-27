<?php

if( is_devel_server() ) {
  require_once('setup.dev.php');
}
else {
  require_once('setup.final.php');
}



/**
* Calcula si estamos en el entorno de desarrollo o produccion
* @return boolean 
*/
function is_devel_server() {
  $develEnviroment = false;

  if( isset( $_SERVER['REMOTE_ADDR'] ) ) {
    if ( strpos( $_SERVER['REMOTE_ADDR'], '10.77.' ) === 0 ||
      strpos( $_SERVER['REMOTE_ADDR'], '127.0.' ) === 0 
    ) {
      $develEnviroment = true;
    }
  }
  else {
    $last_line = exec('ip addr show | grep 10.72.');
    if ( strpos( $last_line, '10.77.' ) !== false ) {
      $develEnviroment = true;
    }
  }

  return $develEnviroment;
}

