<?php

define('MOD_DEVEL_URL_DIR', 'devel');
// Dependencias en classes/view/templates/js/devel.js

Cogumelo::load("c_controller/Module");

class devel extends Module
{

  var $url_patterns = array(
    '#^devel$#' => 'view:DevelView::main',
    '#^devel/read_logs$#' => 'view:DevelView::read_logs',
    '#^devel/get_debugger#' => 'view:DevelView::get_debugger',
    '#^devel/get_sql_tables$#' => 'view:DevelView::get_sql_tables'
/*
    '#^'.MOD_DEVEL_URL_DIR.'$#' => 'view:DevelView::main',
    '#^'.MOD_DEVEL_URL_DIR.'/read_logs$#' => 'view:DevelView::read_logs',
    '#^'.MOD_DEVEL_URL_DIR.'/get_debugger#' => 'view:DevelView::get_debugger',
    '#^'.MOD_DEVEL_URL_DIR.'/get_sql_tables$#' => 'view:DevelView::get_sql_tables'
*/
  );

}