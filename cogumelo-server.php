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
  require_once( COGUMELO_LOCATION.'/coreClasses/coreController/Cache.php' );
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

      // Def: templates_c
      rmdirRec( Cogumelo::getSetupValue('smarty:compilePath'), false );
      // Def: cgmlImg
      rmdirRec( Cogumelo::getSetupValue('mod:filedata:cachePath'), false );
      // Def: mediaCache
      rmdirRec( Cogumelo::getSetupValue('mod:mediaserver:tmpCachePath'), false );
      echo ' - Cogumelo File cache flush'."\n";

      if( function_exists('opcache_reset') ) {
        $opcacheReset = opcache_reset(); // ( ($opcacheReset) ? 'OK' : 'FAIL'
        echo ' - Cogumelo PHP cache flush'."\n";
      }

      $cacheCtrl = new Cache();
      $cacheCtrl->flush();
      echo ' - Cogumelo Memory Cache flush'."\n";

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

  $dir = rtrim( $dir, '/' );
  if( is_dir( $dir ) ) {
    $dirElements = scandir( $dir );
    if( !empty( $dirElements ) ) {
      foreach( $dirElements as $object ) {
        if( $object !== '.' && $object !== '..' ) {
          if( is_dir( $dir.'/'.$object ) ) {

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
      if( !is_link( $dir ) ) {
        rmdir( $dir );
      }
      else {
        unlink( $dir );
      }
    }
  }
}


function isPrivateIp( $ip ) {
  return( strpos( $ip, '127.' ) === 0 || !filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) );
}
