<?php


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


    $parsedUrl = parse_url($path);
    $this->urlPath = $parsedUrl['path'];
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
          $this->copyAndMoveFile( true ); // copy and mofe with MINIFY
        }
        else
        if( substr($this->urlPath, -5) == '.less' ) {
          if( MEDIASERVER_COMPILE_LESS == false ) {
            $this->copyAndMoveFile();
          }
          else {
            $this->compileAndMoveLessFile();
          }
        }
        else {
          $this->copyAndMoveFile();
        }
      }
    }




    $this->serveFile( );

    //echo MEDIASERVER_HOST . MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath;
    
  } 


  /*
  * Copy files to tmp path and move it to final path
  * @var boolean $minify: if we want to minify result
  */
  function copyAndMoveFile( $minify = false ) {

    $tmp_cache = MEDIASERVER_TMP_CACHE_PATH . $this->modulePath . $this->urlPath;
    $final_cache = SITE_PATH.'../httpdocs/'.MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath;


    if( !file_exists( $tmp_cache && MEDIASERVER_HOST == '/' ) ) {

      // create tmp folder
      $this->createDirPath( $tmp_cache );

      if( !$minify ) {
        // copy to tmp path
        copy( $this->realFilePath, $tmp_cache );
      }
      else {
        $this->minifyCopy( $this->realFilePath, $tmp_cache );
      }

      // create final folder
      $this->createDirPath( $final_cache );

      // delete final file if exist
      if( file_exists( $final_cache ) ){
        unlink( $final_cache );
      }

      // move from tmp path to final path
      rename( $tmp_cache , $final_cache );
    }

  }


  /*
  * Compile and move compiled less to final path
  * @var boolean $minify: if we want to minify result
  */
  function compileAndMoveLessFile( $minify = false ) {

    $lessControl = new LessController();
    $tmp_cache = MEDIASERVER_TMP_CACHE_PATH . $this->modulePath . $this->urlPath. '.css';
    $final_cache = SITE_PATH.'../httpdocs/'.MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath .'.css' ;

    // create tmp folder
    $this->createDirPath( $tmp_cache );

    $lessControl->compile( $this->urlPath, $tmp_cache, $this->moduleName );


    // create final folder
    $this->createDirPath( $final_cache );

    // delete final file if exist
    if( file_exists( $final_cache ) ){
      unlink( $final_cache );
    }

    // move from tmp path to final path
    rename( $tmp_cache , $final_cache );
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


  function serveFile( ) {

    // js file
    if( substr($this->urlPath , -3) == '.js' ) {
      header('Content-Type: text/javascript');
      readfile( SITE_PATH.'../httpdocs/' . MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath  );
    }
    else 
    // css or 
    if( substr($this->urlPath , -4) == '.css' ) { 
      header('Content-Type: text/css');
      readfile( SITE_PATH.'../httpdocs/'.  MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath  );
    }
    else
    // less file without compilation
    if( substr($this->urlPath , -5) == '.less' && !MEDIASERVER_COMPILE_LESS ) {
      header('Content-Type: text');
      readfile( SITE_PATH.'../httpdocs/'.  MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath  );
    }
    else 
    // less file with compilation      
    if( substr($this->urlPath , -5) == '.less' ){
      header('Content-Type: text/css');
      readfile( SITE_PATH.'../httpdocs/'.  MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath.'.css'  );      
    }
    else {
      // redirect to file
      RequestController::redirect( MEDIASERVER_HOST . MEDIASERVER_FINAL_CACHE_PATH . $this->modulePath . $this->urlPath );
    }

  }

  /*
  * Copy with Minimify css or js files using lib https://github.com/nitra/PhpMin/tree/master
  * @return void
  * @var string $fromPath: the path of file to minify
  * @var string $toPath: the path to copy minify file
  */
  function minifyCopy($fromPath, $toPath) {


    $filters = array
    (
      "RemoveComments" => true
    );

    $type = false;

    if( substr($fromPath, -4) == '.css' ) {
      $type = 'css';
    }
    else if( substr($fromPath, -3) == '.js' ) {
      $type = 'js';
    }

    if($type == 'js'){
      file_put_contents(
        $toPath, 
        JSMin::minify(file_get_contents( $fromPath ), $filters),
        LOCK_EX
      );
    }
    else if($type == 'css') {
      file_put_contents(
        $toPath, 
        CssMin::minify( file_get_contents( $fromPath ), $filters),
        LOCK_EX
      );
    }
    else {
      copy( $fromPath, $toPath );
    }


  }

}