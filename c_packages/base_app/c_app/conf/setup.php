<?php

if( develEnviroment() ) {
  define( 'IS_DEVEL_ENV', true );
  require_once('setup.dev.php');
}
else {
  define( 'IS_DEVEL_ENV', false );
  require_once('setup.final.php');
}



/**
* Calcula si estamos en el entorno de desarrollo o produccion
* @return bool
*/
function develEnviroment() {
  $develEnv = false;

  if( isset( $_SERVER['REMOTE_ADDR'] ) ) {
    if ( strpos( $_SERVER['REMOTE_ADDR'], '10.77.' ) === 0 ||
      strpos( $_SERVER['REMOTE_ADDR'], '127.0.' ) === 0
    ) {
      $develEnv = true;
    }
  }
  else {
    $last_line = exec('ip addr show | grep 10.72.');
    if ( strpos( $last_line, '10.77.' ) !== false ) {
      $develEnv = true;
    }
  }

  return $develEnv;
}

