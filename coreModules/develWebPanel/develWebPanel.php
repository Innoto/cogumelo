<?php

// Dependencias en classes/view/templates/js/devel.js

Cogumelo::load("coreController/Module.php");

class develWebPanel extends Module {

  public $name = "develWebPanel";
  public $version = 1.0;


  public function __construct() {

    global $COGUMELO_INSTANCED_MODULES;
    $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'$#', 'view:DevelView::main' );
    $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/read_logs$#', 'view:DevelView::read_logs' );
    $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/get_debugger#', 'view:DevelView::get_debugger' );
    $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/get_sql_tables$#', 'view:DevelView::get_sql_tables' );
    $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/phpinfo$#', 'view:DevelView::develPhpInfo' );
    $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.Cogumelo::getSetupValue( 'mod:devel:url' ).'/phpinfo[/\#\?]+.*#', 'view:DevelView::develPhpInfo' );
  }

}
