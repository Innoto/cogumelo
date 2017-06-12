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
      'params' => array('natxet/CssMin', '3.0.2'),
      'installer' => 'composer',
      'includes' => array('')
    ),
    array(
      'id' => 'lessmin',
      'params' => array('oyejorge/less.php', '1.7.0.13'),
      'installer' => 'composer',
      'includes' => array('lessc.inc.php')
    )
  );

  public $includesCommon = array(
    'controller/MediaserverController.php',
    'controller/CacheUtilsController.php',
    'controller/LessController.php'
  );


  public function __construct() {
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' ).'/jsConfConstants.js#', 'view:ConfConstantsView::javascript' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'/jsConfConstants.js#', 'view:ConfConstantsView::javascript' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'/lessConfConstants.less#', 'view:ConfConstantsView::less' );

    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'/module(.*)#', 'view:MediaserverView::module' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'(/.*)#', 'view:MediaserverView::application' );
    $this->addUrlPatterns( '#(.+\/)?classes/view/templates/(.+)\.less$#', 'view:MediaserverView::onClientLess');

  }

}
