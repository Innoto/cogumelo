<?php

define('MOD_MEDIASERVER_URL_DIR', 'media');

Cogumelo::load("c_controller/Module.php");

class mediaserver extends Module
{
  public $name = "mediaserver";
  public $version = "";

  public $dependences = array(
    // COMPOSER 
    array(
      "id" => "jsmin",
      "params" => array("linkorb/jsmin-php", "1.0.0"),
      "installer" => "composer",
      "includes" => array("src/jsmin-1.1.1.php")
    ),
    array(
      "id" => "cssmin",
      "params" => array("natxet/CssMin", "3.0.2"),
      "installer" => "composer",
      "includes" => array("")
    ),
    array(
      "id" => "cssmin",
      "params" => array("less.php/less.php", "1.7.0.2"),
      "installer" => "composer",
      "includes" => array("lessc.inc.php")
    )        
  );

  public $includesCommon = array(
    'controller/MediaserverController.php',
    'controller/CacheUtilsController.php',
    'controller/LessController.php'
  );

 
  function __construct() {
    $this->addUrlPatterns( '#^'.MOD_MEDIASERVER_URL_DIR.'/module(.*)#', 'view:MediaserverView::module' );
    $this->addUrlPatterns( '#^'.MOD_MEDIASERVER_URL_DIR.'(.*)#', 'view:MediaserverView::application' );
  }

}