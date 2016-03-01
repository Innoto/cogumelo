<?php

Cogumelo::load( 'coreController/Module.php' );


class detectMobile extends Module {


  public $name = 'detectMobile';
  public $version = 1.0;
  public $dependences = array(

    array(
      "id" =>"mobiledetect",
      "params" => array("mobiledetect/mobiledetectlib","^2.8"),
      "installer" => "composer",
      "includes" => array("Mobile_Detect.php")
    )

  );


  public $includesCommon = array(
  );


  public function __construct() {
    $this->addUrlPatterns( '#^()\/?()$#', 'noendview:DetectMobileView::detectMobile' );
  }
}
