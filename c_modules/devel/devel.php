<?php

// Dependencias en classes/view/templates/js/devel.js

Cogumelo::load("c_controller/Module.php");

class devel extends Module
{
  public $name = "devel";
  public $version = "2.0";
  public $dependences = array(
   // BOWER   
   array(
     "id" => "jquery",
     "params" => array("jquery#1.*"),
     "installer" => "bower",
     "includes" => array("src/jquery.js")
   ),
   array(
     "id" => "bootstrap",
     "params" => array("bootstrap"),
     "installer" => "bower",
     "includes" => array()
   ),  
   // COMPOSER 
   array(
     "id" => "kint",
     "params" => array("raveren/kint","1.0.*@dev"),
     "installer" => "composer",
     "includes" => array("Kint.class.php")
   ),
   array(
     "id" => "sqlFormatter",
     "params" => array("jdorn/sql-formatter", "1.3.*@dev"),
     "installer" => "composer",
     "includes" => array("lib/SqlFormatter.php")
   )
  );

  public $includesCommon = array(
    'controller/LogReaderController.php',
    'controller/DevelDBController.php',
    'controller/UrlListController.php'
  );

  function __construct() {
    $this->addUrlPatterns( '#^'.MOD_DEVEL_URL_DIR.'$#', 'view:DevelView::main' );
    $this->addUrlPatterns( '#^'.MOD_DEVEL_URL_DIR.'/read_logs$#', 'view:DevelView::read_logs' );
    $this->addUrlPatterns( '#^'.MOD_DEVEL_URL_DIR.'/get_debugger#', 'view:DevelView::get_debugger' );
    $this->addUrlPatterns( '#^'.MOD_DEVEL_URL_DIR.'/get_sql_tables$#', 'view:DevelView::get_sql_tables' );

    //Cogumelo::error( print_r( $this->getUrlPatternsToArray(), true ) );
  }

}