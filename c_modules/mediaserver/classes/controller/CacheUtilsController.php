<?php

class CacheUtilsController {

  static function generateLessCaches() {

  }

  static function prepareLessCaches(){
    
  }

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
