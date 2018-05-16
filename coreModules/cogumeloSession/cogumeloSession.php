<?php

Cogumelo::load( 'coreController/Module.php' );


class cogumeloSession extends Module {

  public $name = 'cogumeloSession';
  public $version = 1.0;
  public $dependences = array();

  public $includesCommon = array(
    'controller/CogumeloSessionController.php'
  );


  public function __construct() {
    // error_log(__METHOD__ );

    $this->addUrlPatterns( '#^cgml-session.json$#', 'view:CogumeloSessionView::jsonTokenSession' );
  }
}
