<?php

class CacheUtilsController {


  //
  //  LESS METHODS
  //

  static function generateLessCaches() {


  }

  // crea estructura con todos os arquivos LESS para a súa futura compilación
  static function prepareLessTmpdir( ){

    global $C_ENABLED_MODULES;
    $destino = SITE_PATH.'/tmp/mediaCache/lesstmp/'.self::generateLessTmpdirName().'/';


    mkdir($destino);

    $cacheableFolder = 'classes/view/templates/';

    foreach( $C_ENABLED_MODULES as $moduleName ){

      // cogumelo modules
      self::copyLessTmpdir( 
        COGUMELO_LOCATION.'/c_modules/',
        $moduleName.'/'.$cacheableFolder, 
        $destino 
      );

      // app modules
      self::copyLessTmpdir( 
        SITE_PATH.'/modules/',
        $moduleName.'/'.$cacheableFolder,
        $destino 
      );
      
    }

    // app files
    self::copyLessTmpdir( 
      SITE_PATH.'/',
      $cacheableFolder, 
      $destino 
    );

    return $nombreDir;
  }


  static function copyLessTmpdir($origDir, $filePath, $destDir){

    $includeFiles = array('less');
    $fileList = self::listFolderFiles( $origDir.$filePath , $extensions, $excludeExtensions );

    foreach ( $fileList as $filePath ) {
      mkdir( dirname($destDir.$filePath ), 0744, true );
      copy( $filePath, $destDir.$filePath );
    }

  }

  static function removeLessTmpdir($dirName){

  }


  static function generateLessTmpdirName() {

    $length = 5;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    if( file_exist( $randomString ) ){
      $randomString = self::generateLessTmpdirName();
    }

    return $randomString;
  }


  //
  //  END LESS METHODS
  //



  static function generateAllCaches() {
    global $C_ENABLED_MODULES;
    $cacheableFolder = 'classes/view/templates/';

    foreach( $C_ENABLED_MODULES as $moduleName ){

      // cogumelo modules
      self::cacheFolder( 
        COGUMELO_LOCATION.'/c_modules/'.$moduleName.'/'.$cacheableFolder, 
        $moduleName
      );
      // app modules
      self::cacheFolder( 
        SITE_PATH.'/modules/'.$moduleName.'/'.$cacheableFolder, 
        $moduleName
      );
    }

    // app files
    self::cacheFolder( SITE_PATH.'/'.$cacheableFolder );

  }

  static function cacheFolder( $folder , $moduleName = false ) {
    $mediaserverControl = new MediaserverController();

    $fileList = self::listFolderFiles( $folder , array('php', 'tpl', 'less'), true );



    if( sizeof( $fileList ) > 0 )
    {
      foreach ( $fileList as $filePath ) {
        $mediaserverControl->cacheContent( str_replace($folder, '' , $filePath ), $moduleName, true );
      }
    }

  }

  // recursive list folder
  static function listFolderFiles( $folder , $extensions, $excludeExtensions ) {
    $paths = array();

    if( is_dir( $folder ) ) {
      $iter = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
          RecursiveIteratorIterator::SELF_FIRST,
          RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
      );

      foreach ($iter as $path) {
          if ( is_file($path) ) {
            if( $excludeExtensions ) {
              if( self::excludeExtensions($path, $extensions ) ) {
                $paths[] = $path;
              }
            }
            else {
              if( self::includeExtensions($path, $extensions ) ) {
                $paths[] = $path;
              }
            }
          }
      }

    }

    return $paths;
  }


  // exclude path that have an extensión of array
  static function excludeExtensions( $filePath, $extArray ) {
    $ret = true;
    $found = false;

    foreach ( $extArray as $ext ) {
      if( substr( $filePath, -(strlen($ext)+1) ) == '.'.$ext ) {
        $found = true;
      }
    }

    if( $found == true ){
      $ret = false;
    }

    return $ret;
  }

  // include path that have an extensión of array
  static function includeExtensions( $filePath, $extArray ) {
    $ret = false;

    foreach ( $extArray as $ext ) {
      if( substr( $filePath, -(strlen($ext)+1) ) == '.'.$ext ) {
        $ret = true;
      }
    }

    return $ret;
  }  


}
