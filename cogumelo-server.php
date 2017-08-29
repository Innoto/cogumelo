<?php

/*

Este script se lanza desde un php en el servidor web que prepara el entorno con la infomacion necesaria

La preparacion de la informacion puede ser algo como esto:

// define( 'WEB_BASE_PATH', getcwd() ); // Apache DocumentRoot
// define( 'PRJ_BASE_PATH', realpath( WEB_BASE_PATH.'/..' ) ); // Project Path (normalmente contiene app/ httpdocs/ formFiles/)
// define( 'APP_BASE_PATH', PRJ_BASE_PATH.'/app' ); // App Path

// set_include_path( '.:'.APP_BASE_PATH ); // Include cogumelo core Location

// require_once( 'conf/setup.php' );

*/



// We check that the conexion comes from localhost
if( $_SERVER['REMOTE_ADDR'] !== 'local_shell' && isset( $_SERVER['REMOTE_ADDR'] ) &&  isPrivateIp( $_SERVER['REMOTE_ADDR'] ) ) {

  // Cargamos Cogumelo
  require_once( COGUMELO_LOCATION.'/coreClasses/CogumeloClass.php' );
  require_once( COGUMELO_LOCATION.'/coreClasses/coreController/DependencesController.php' );
  require_once( APP_BASE_PATH.'/Cogumelo.php' );


  $par = $_GET['q'];
  switch( $par ) {
    case 'rotate_logs':
      $dir = Cogumelo::getSetupValue( 'logs:path' );
      $handle = opendir( $dir );
      while( $file = readdir( $handle ) ) {
        $file = $dir.'/'.$file;
        if( is_file( $file ) ) {
          $pos = strpos( $file, 'gz' );
          if( $pos === false ){
            $gzfile = $file.'-'.date( 'Ymd-Hms' ).'.gz';
            $fp = gzopen( $gzfile, 'w9' );
            gzwrite ( $fp, file_get_contents( $file ) );
            gzclose( $fp );
          }
        }
      }
      break;
    case 'flush':
      $dir = Cogumelo::getSetupValue('smarty:compilePath'); // Def: templates_c
      rmdirRec( $dir, false ); // false para que borre el contenido y no el contenedor


      $dir = Cogumelo::getSetupValue('mod:filedata:cachePath'); // Def: cgmlImg
      rmdirRec( $dir, false ); // false para que borre el contenido y no el contenedor


      $dir = Cogumelo::getSetupValue('mod:mediaserver:tmpCachePath'); // Def: mediaCache
      rmdirRec( $dir, false ); // false para que borre el contenido y no el contenedor

      if( function_exists('opcache_reset') ) {
        opcache_reset();
        echo 'opcache_reset() LISTO!!!'."\n";
      }

      break;
    case 'client_caches':
      Cogumelo::load( 'coreController/ModuleController.php' );
      require_once( ModuleController::getRealFilePath( 'mediaserver.php', 'mediaserver' ) );
      mediaserver::autoIncludes();
      CacheUtilsController::generateAllCaches();
      break;
  } // switch
}
else {
  header( 'HTTP/1.0 403 Forbidden' );
  echo( "You are forbidden!\n\nUnusual access to cogumelo-server\n" );
  error_log('ERROR: cogumelo-server.php - Access forbidden');
}


function rmdirRec( $dir, $removeContainer = true ) {
  // error_log( "rmdirRec( $dir )" );
  if( is_dir( $dir ) ) {
    $dirElements = scandir( $dir );
    if( is_array( $dirElements ) && count( $dirElements ) > 0 ) {
      foreach( $dirElements as $object ) {
        if( $object !== '.' && $object !== '..' ) {
          if( is_link( $dir.'/'.$object ) ) {
            unlink( $dir.'/'.$object );
          }
          elseif( is_dir( $dir.'/'.$object ) ) {
            rmdirRec( $dir.'/'.$object );
          }
          else {
            unlink( $dir.'/'.$object );
          }
        }
      }
    }
    reset( $dirElements );
    if( $removeContainer ) {
      rmdir( $dir );
    }
  }
}


function isPrivateIp( $ip ) {
  return( strpos( $ip, '127.' ) === 0 || !filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) );
}
