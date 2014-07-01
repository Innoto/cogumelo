<?php

define('MOD_MEDIASERVER_URL_DIR', 'media');

Cogumelo::load("c_controller/Module");

class mediaserver extends Module
{

  function __construct() {
    $this->addUrlPatterns( '#^'.MOD_MEDIASERVER_URL_DIR.'/module(.*)#', 'view:MediaserverView::module' );
    $this->addUrlPatterns( '#^'.MOD_MEDIASERVER_URL_DIR.'(.*)#', 'view:MediaserverView::application' );
  }

}