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
    require_once( Cogumelo::getSetupValue( 'dependences:composerPath').'/mobiledetect/mobiledetectlib/Mobile_Detect.php');

    $detect = new Mobile_Detect;

    Cogumelo::setSetupValue( 'mod:detectMobile:isMobile', $detect->isMobile() );
    Cogumelo::setSetupValue( 'mod:detectMobile:isTablet', $detect->isTablet() );
  }
}
