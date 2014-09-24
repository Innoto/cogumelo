<?php

define('MOD_MEDIASERVER_URL_DIR', 'media');

Cogumelo::load("c_controller/Module");

class mediaserver extends Module
{
  public $name = "mediaserver";
  public $version = "";
  public $dependences = array(
     // BOWER   
     array(
       "id" => "jquery",
       "params" => array("jquery#1.*"),
       "installer" => "bower",
       "load" => array("jquery.js")
     )
 );

 
  function __construct() {
    $this->addUrlPatterns( '#^'.MOD_MEDIASERVER_URL_DIR.'/module(.*)#', 'view:MediaserverView::module' );
    $this->addUrlPatterns( '#^'.MOD_MEDIASERVER_URL_DIR.'(.*)#', 'view:MediaserverView::application' );
  }

}