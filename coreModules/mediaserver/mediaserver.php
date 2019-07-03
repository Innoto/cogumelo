<?php


Cogumelo::load('coreController/Module.php');

class mediaserver extends Module {

  public $name = 'mediaserver';
  public $version = 1.0;

  public $dependences = array(
    // COMPOSER
    array(
      'id' => 'jsmin',
      'params' => array('linkorb/jsmin-php', '1.0.0'),
      'installer' => 'composer',
      'includes' => array('src/jsmin-1.1.1.php')
    ),
    array(
      'id' => 'cssmin',
      'params' => array('natxet/cssmin', '3.0.2'),
      'installer' => 'composer',
      'includes' => array('src/CssMin.php')
    ),
    array(
      'id' => 'scssphp',
      'params' => array('leafo/scssphp', '0.7.6'),
      'installer' => 'composer',
      'includes' => array('scss.inc.php')
    ),
    array(
     'id' => 'minify',
     'params' => array('matthiasmullie/minify', '1.3.61'),
     'installer' => 'composer',
     'includes' => array(
       '../minify/src/Minify.php',
       '../minify/src/CSS.php',
       '../minify/src/JS.php',
       '../minify/src/Exception.php',
       '../minify/src/Exceptions/BasicException.php',
       '../minify/src/Exceptions/FileImportException.php',
       '../minify/src/Exceptions/IOException.php',
       '../path-converter/src/ConverterInterface.php',
       '../path-converter/src/Converter.php'
     )
   ),

    array(
     'id' => 'resource-watcher',
     'params' => array("jasonlewis/resource-watcher", "1.2.*"),
     'installer' => 'composer',
     'includes' => array()
    )
  );

  public $includesCommon = array(
    'controller/MediaserverController.php',
    'controller/CacheUtilsController.php',
    'controller/ScssController.php'
  );


  public function __construct() {
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ).'/jsConfConstants.js#', 'view:ConfConstantsView::javascript' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'/jsConfConstants.js#', 'view:ConfConstantsView::javascript' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'/jsLog.js#', 'view:ConfConstantsView::jslog' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'/module(.*)#', 'view:MediaserverView::module' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'(/.*)#', 'view:MediaserverView::application' );
  }

}
