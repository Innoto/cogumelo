<?php

// Dependencias en classes/view/templates/js/devel.js

Cogumelo::load("coreController/Module.php");

class devel extends Module {

  public $name = "devel";
  public $version = 1.0;
  public $dependences = array(
   // BOWER

   array(
     "id" => "jquery",
     "params" => array("jQuery#2.2"),
     "installer" => "bower",
     "includes" => array("dist/jquery.js")
   ),

   array(
    "id" => "bootstrap",
    "params" => array("bootstrap#v3.3"),
    "installer" => "bower",
    "includes" => array("dist/js/bootstrap.min.js")
   ),
   array(
     "id" =>"d3",
     "params" => array("d3"),
     "installer" => "bower",
     "includes" => array("d3.js")
   ),
   array(
     "id" =>"webcola",
     "params" => array("webcola#3.0.0"),
     "installer" => "bower",
     "includes" => array("WebCola/cola.v3.min.js")
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
    'controller/UrlListController.php',
    'js/devel.js',
    'styles/devel.less',
    'js/drawERD.js'
  );

  public function __construct() {
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'$#', 'view:DevelView::main' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/read_logs$#', 'view:DevelView::read_logs' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/get_debugger#', 'view:DevelView::get_debugger' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/get_sql_tables$#', 'view:DevelView::get_sql_tables' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/phpinfo$#', 'view:DevelView::develPhpInfo' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/phpinfo[/\#\?]+.*#', 'view:DevelView::develPhpInfo' );
    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/GC#', 'view:DevelView::runGarbageCollectors' );

    $this->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/porto#', 'view:DevelView::develPorto' );

    //Cogumelo::error( print_r( $this->getUrlPatternsToArray(), true ) );
  }

}
