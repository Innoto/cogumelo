<?php


class MediaserverController {

  var $realFilePath = false;
  var $urlPath = false;
  var $moduleName = false;
  var $modulePath = '';
  private $minimify = false;

  public function __construct() {
    $productionMode = Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' );
    $minimifyFiles = Cogumelo::getSetupValue( 'mod:mediaserver:minimifyFiles' );
    $this->minimify = ( $productionMode === true ) && ( $minimifyFiles === true );
  }



  public function cacheContent( $path, $module, $doNotRedirect = false ) {
    // error_log( __METHOD__.' $path:'.$path.', $module: '.$module );

    $parsedUrl = parse_url($path);
    $this->urlPath = $parsedUrl['path'];
    $this->moduleName = $module;
    $this->realFilePath = ModuleController::getRealFilePath( 'classes/view/templates/'.$this->urlPath, $this->moduleName );
    $this->modulePath = ( $this->moduleName ) ? '/module/'.$this->moduleName.'/' : '' ;

    if( !file_exists( $this->realFilePath ) && !$doNotRedirect ) {
      if(!$doNotRedirect) {
        // RequestController::redirect(SITE_URL_CURRENT.'/404');
        RequestController::httpError404();
      }
    }

    if( mb_substr($this->urlPath, -4) === '.tpl' ||
        mb_substr($this->urlPath, -4) === '.php' ||
        mb_substr($this->urlPath, -4) === '.inc'
      ) {

      Cogumelo::error('trying to load( '.$this->urlPath.' ), but not allowed to serve .tpl .php or .inc files ');
      if(!$doNotRedirect) {
        // RequestController::redirect(SITE_URL_CURRENT.'/404');
        RequestController::httpError404();
      }
    }
    else {
      $this->copyAndMoveFile( $this->minimify );
    }
  }


  public function compileAndCacheLess( $path, $module ) {
    // error_log( __METHOD__.' $path:'.$path.', $module: '.$module );

    $parsedUrl = parse_url($path);
    $this->urlPath = $parsedUrl['path'];
    $this->moduleName = $module;
    $this->realFilePath = ModuleController::getRealFilePath('classes/view/templates/'.$this->urlPath, $this->moduleName);
    $this->modulePath = ( $this->moduleName )? '/module/'.$this->moduleName.'/' : '' ;

    if( mb_substr($this->urlPath, -5) === '.scss' ) {
      $this->compileAndMoveLessFile( $this->minimify );
    }
  }


  /**
  * Process path to serve media resource. It will move and
  * serve final path resource.
  */
  public function serveContent( $path, $module = false ) {

    if( Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) === false ||
      ( mb_substr($path , -3) === '.js' &&  Cogumelo::getSetupValue( 'mod:mediaserver:notCacheJs' ) ) )
    {
      $this->cacheContent( $path, $module );
    }
    else {
      $this->modulePath = ( $module )? '/module/'.$module.'/' : '' ;
      $this->urlPath = $path;
    }
    $this->serveFile( );
  }


  /*
  * Copy files to tmp path and move it to final path
  * @var boolean $minimify: if we want to minimify result
  */
  public function copyAndMoveFile( $minimify = false ) {
    $tmp_cache = Cogumelo::getSetupValue( 'mod:mediaserver:tmpCachePath' ) .'/'. $this->modulePath . $this->urlPath;
    $final_cache = WEB_BASE_PATH.'/'.Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ) .'/'. $this->modulePath . $this->urlPath;

    if( !file_exists( $tmp_cache && Cogumelo::getSetupValue( 'mod:mediaserver:host' ) === '/' ) ) {
      // create tmp folder
      $this->createDirPath( $tmp_cache );

      if( !$minimify ) {
        // copy to tmp path
        copy( $this->realFilePath, $tmp_cache );
      }
      else {
        $this->minimifyCopy( $this->realFilePath, $tmp_cache );
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
  * @var boolean $minimify: if we want to minimify result
  */
  public function compileAndMoveLessFile( $minimify = false ) {

    $lessControl = new LessController();
    $tmp_cache = Cogumelo::getSetupValue( 'mod:mediaserver:tmpCachePath' ) .'/'. $this->modulePath . $this->urlPath.'.css';
    $final_cache = WEB_BASE_PATH.'/'.Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ) .'/'. $this->modulePath . $this->urlPath.'.css' ;

    // error_log( __METHOD__.' $tmp_cache:'.$tmp_cache.', $final_cache: '.$final_cache );

    // create tmp folder
    $this->createDirPath( $tmp_cache );

    if( $minimify ) {
      $lessControl->setMinimify( true );
    }

    if( $lessControl->compile( $this->urlPath, $tmp_cache, $this->moduleName ) ) {
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
  * Create the full directory path recursively
  * @var string $path: the path of file or directory
  */
  public function createDirPath( $path ) {

    // excluding file name to create parent dir
    $dirname = dirname($path);

    if( !is_dir ( $dirname ) ) {
      mkdir( dirname($path ), 0744, true );
    }
  }


  public function serveFile() {

    Cogumelo::debug("Mediaserver, serving file: ".$this->realFilePath);

    if( !Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) || ( mb_substr($this->urlPath , -3) == '.js' &&  Cogumelo::getSetupValue( 'mod:mediaserver:notCacheJs' ) ) )  {
      // js file
      if( mb_substr($this->urlPath , -3) == '.js' ) {
        header('Content-Type: text/javascript');
        readfile( WEB_BASE_PATH.'/' . Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ) . $this->modulePath . $this->urlPath  );
      }
      else if( mb_substr($this->urlPath , -4) == '.css' ) {
        // css or
        header('Content-Type: text/css');
        readfile( WEB_BASE_PATH.'/'.  Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ) . $this->modulePath . $this->urlPath  );
      }
      else if( mb_substr($this->urlPath , -5) == '.scss' ) {
        // less file without compilation
        header('Content-Type: text/scss');
        readfile( WEB_BASE_PATH.'/'.  Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ) . $this->modulePath . $this->urlPath  );
      }
      else {
        // redirect to file
        RequestController::redirect( Cogumelo::getSetupValue( 'mod:mediaserver:host' ) . Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ) . $this->modulePath . $this->urlPath );
      }
    }
    else {
      // redirect to file
      if(file_exists( Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ) . $this->modulePath . $this->urlPath ) ) {
        RequestController::redirect( Cogumelo::getSetupValue( 'mod:mediaserver:host' ) . Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ) . $this->modulePath . $this->urlPath );
      }
    }
  }


  /*
  * Copy with Minimify css or js files using lib https://github.com/nitra/PhpMin/tree/master
  * @return void
  * @var string $fromPath: the path of file to minimify
  * @var string $toPath: the path to copy minimify file
  */
  public function minimifyCopy( $fromPath, $toPath ) {

    $filters = array(
      "RemoveComments" => true
    );

    $type = false;

    if( mb_substr($fromPath, -4) === '.css' ) {
      $type = 'css';
    }
    else if( mb_substr($fromPath, -3) === '.js' ) {
      $type = 'js';
    }

    if($type === 'js'){
      file_put_contents(
        $toPath,
        JSMin::minify( file_get_contents( $fromPath ), $filters ),
        LOCK_EX
      );
    }
    elseif($type === 'css') {
      file_put_contents(
        $toPath,
        CssMin::minify( file_get_contents( $fromPath ), $filters ),
        LOCK_EX
      );
    }
    else {
      copy( $fromPath, $toPath );
    }
  }



}
