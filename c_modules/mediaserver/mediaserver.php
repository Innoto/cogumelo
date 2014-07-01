<?php

define('MOD_MEDIASERVER_URL_DIR', 'media');

Cogumelo::load("c_controller/Module");

class mediaserver extends Module
{
  var $url_patterns = array(
    '#^media/module(.*)#' => 'view:MediaserverView::module',
    '#^media(.*)#' => 'view:MediaserverView::application'
/*
    '#^'.MOD_MEDIASERVER_URL_DIR.'/module(.*)#' => 'view:MediaserverView::module',
    '#^'.MOD_MEDIASERVER_URL_DIR.'(.*)#' => 'view:MediaserverView::application'
*/
  );
}