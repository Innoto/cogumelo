<?php

Cogumelo::load("c_controller/Module");

class devel extends Module
{

  var $url_patterns = array(

    '#^devel$#' => 'view:DevelView::main',
    '#^devel/read_logs$#' => 'view:DevelView::read_logs',
    '#^devel/get_debugger#' => 'view:DevelView::get_debugger',
    '#^devel/get_sql_tables$#' => 'view:DevelView::get_sql_tables'
  );

  
}