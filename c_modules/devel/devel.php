<?php

Cogumelo::load("c_controllers/module/Module");

class devel extends Module
{

  var $url_patterns = array(

    '#^devel$#' => 'view:DevelView::main',
    '#^devel/read_logs$#' => 'view:DevelView::read_logs',
    '#^devel/create_db_scheme(.*)$#' => 'view:DevelView::create_db_scheme'
  );
}