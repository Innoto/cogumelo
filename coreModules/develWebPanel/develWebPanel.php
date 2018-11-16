<?php

Cogumelo::load("coreController/Module.php");

class develWebPanel extends Module {

  public $name = "develWebPanel";
  public $version = 1.0;


  public function __construct() {

    global $COGUMELO_INSTANCED_MODULES;

    if( isset( $COGUMELO_INSTANCED_MODULES['devel'] ) ) {
      $develUrl = Cogumelo::getSetupValue( 'mod:devel:url' );
      if( empty($develUrl) ) {
        $develUrl = 'devel';
      }

      $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.$develUrl.'$#', 'view:DevelView::main' );
      $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.$develUrl.'/get_sql_tables$#', 'view:DevelView::get_sql_tables' );
      $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.$develUrl.'/phpinfo$#', 'view:DevelView::develPhpInfo' );
      $COGUMELO_INSTANCED_MODULES['devel']->addUrlPatterns( '#^'.$develUrl.'/phpinfo[/\#\?]+.*#', 'view:DevelView::develPhpInfo' );
    }
  }

}
