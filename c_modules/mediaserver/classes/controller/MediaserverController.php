<?php

Cogumelo::load('c_vendor/jsmin/jsmin.php');

class MediaserverController {


  var $realFilePath = false;
  var $urlPath = false;
  var $moduleName = false;
  var $modulePath = '';

  /**
  * Process path to serve media resource. It will move and 
  * redirect resource to final path.
  *
  */
  function serveContent($path, $module=false){

    $this->urlPath = $path;
    $this->moduleName = $module;
    $this->realFilePath = ModuleController::getRealFilePath('classes/view/templates/'.$this->urlPath, $this->moduleName);
    $this->modulePath = ( $this->moduleName )? '/module/'.$this->moduleName.'/' : '' ;

    if( MEDIASERVER_REFRESH_CACHE ) {
      if( !file_exists( $this->realFilePath ) ) {
        RequestController::redirect(SITE_URL_CURRENT.'/404');
      }

      if( substr($this->urlPath, -4) == '.tpl' || 
          substr($this->urlPath, -4) == '.php' || 
          substr($this->urlPath, -4) == '.inc' 
        ) {

        Cogumelo::error('trying to load( '.$this->urlPath.' ), but not allowed to serve .tpl .php or .inc files ');
        RequestController::redirect(SITE_URL_CURRENT.'/404');

      }
      else {
        Cogumelo::debug("Mediaserver, serving file: ".$this->realFilePath);

        if( (substr($this->urlPath, -4) == '.css' || substr($this->urlPath, -3) == '.js' ) && MEDIASERVER_MINIMIFY_FILES ) {
          //$this->copyAndMoveFile( $this->minify() );
        }
        else
        if( substr($this->urlPath, -5) == '.less' ) {
          //if( MEDIASERVER_MINIMIFY_FILES ) {
          // $this->copyAndMoveFile( $this->minify($this->lessCompile()) );
          //}
          //else {
          // $this->copyAndMoveFile( $this->lessCompile() );
          //}
        }
        else {
          $this->copyAndMoveFile();
        }
      }
    }

    // redirect to file
    RequestController::redirect( MEDIASERVER_HOST . MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath );

    //echo MEDIASERVER_HOST . MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath;
    
  } 


  /*
  * Copy files to tmp path and move it to final path
  * @return string : final path of file
  * @var string $path: the path of file to copy
  */
  function copyAndMoveFile( ) {

    $tmp_cache = MEDIASERVER_TMP_CACHE_PATH . $this->modulePath . $this->urlPath;
    $final_cache = SITE_PATH.'../httpdocs/'.MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath;


    if( !file_exists( $tmp_cache && MEDIASERVER_HOST == '/' ) ) {

      // create tmp folder
      $this->createDirPath( $tmp_cache );
      // copy to tmp path
      copy($this->realFilePath, $tmp_cache );

      // create final folder
      $this->createDirPath( $final_cache );

      if( file_exists( $final_cache ) ){
        unlink( $final_cache );
      }
      // move from tmp path to final path
      rename( $tmp_cache , $final_cache );
    }

  }


  /*
  * Create the full directory path recursively
  * @var string $path: the path of file or directory
  */
  function createDirPath( $path ) {

    // excluding file name to create parent dir
    $dirname = dirname($path);

    if( !is_dir ( $dirname ) ) {
      mkdir( dirname($path ), 0777, true );
    }

  }

  /*
  * Copy files to tmp path locking it to prevent failures
  * @return string : final path of file
  * @var string $path: the path of file to copy
  */
  /*  
  function blockAndCopyFile( $path ) {

  }*/


  /*
  * Minimify css or js files
  * @return string : path of minified file
  * @var string $path: the path of file to minify
  */
  /*function minify($realpath, $path, $type) {

    // creating secure name for cache file
    $cache_filename = MEDIASERVER_MINIMIFY_CACHE_PATH."/".str_replace('/','', $path);
    @$content =file_get_contents($cache_filename);

    if( ! $content ) {
      if($type == 'js'){
        $content = JSMin::minify(file_get_contents( $path ));
      }
      else
      if($type == 'css') {
        $content = CssMin::minify( file_get_contents( $path ));
      }

      if( $fp = fopen($cache_filename, 'w') ){
        if (flock($fp, LOCK_EX)) { // acquire an exclusive lock
          fwrite($fp, $content);
          fflush($fp); // flush output before releasing the lock
          flock($fp, LOCK_UN); //unlock
        }
        else {
          Cogumelo::debug('file in use: '. MEDIASERVER_MINIMIFY_CACHE_PATH.' '.$path);
        }
        fclose($fp);
      }
      else {
        Cogumelo::error('Cannot create cache file into '. MEDIASERVER_MINIMIFY_CACHE_PATH.' for file '.$path);
      }
    }

  }*/

}