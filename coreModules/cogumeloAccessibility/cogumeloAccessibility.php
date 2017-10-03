<?php
Cogumelo::load( 'coreController/Module.php' );


class cogumeloAccessibility extends Module {

  public $name = 'cogumeloAccessibility';
  public $version = 1;
  public $dependences = [];

  public $includesCommon = [];


  public function __construct() {
    cogumeloAccessibility::load('controller/CogumeloAccessibilityController.php');
    $ctrl = new CogumeloAccessibilityController();
    $ctrl->evalAccessibilityMode();

    // $this->addUrlPatterns( '#^cgml-accessibility.json#', 'view:CogumeloAccessibilityView::jsonInfo' );
  }
}
