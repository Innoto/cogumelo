<?php

Cogumelo::load("c_controller/Module.php");


class user extends Module
{
  public $name = "user";
  public $version = "";
  public $dependences = array(

  );

  public $includesCommon = array(

  );

  function __construct() {
    //$this->addUrlPatterns( '#^'.MOD_DEVEL_URL_DIR.'/read_logs$#', 'view:DevelView::read_logs' );
    //$this->addUrlPatterns( '#^'.MOD_DEVEL_URL_DIR.'/get_debugger#', 'view:DevelView::get_debugger' );
    //$this->addUrlPatterns( '#^'.MOD_DEVEL_URL_DIR.'/get_sql_tables$#', 'view:DevelView::get_sql_tables' );
  }
}