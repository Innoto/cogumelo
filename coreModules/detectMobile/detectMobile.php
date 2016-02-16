<?php

Cogumelo::load( 'coreController/Module.php' );


class detectMobile extends Module {


  public $name = 'detectMobile';
  public $version = '';
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

    //detectMobile::autoIncludes();
    //$detect = new Mobile_Detect;
    //$isMobile = $detect->isMobile();
    //Cogumelo::setSetupValue( 'cogumelo:detectMobile:isMobile', $isMobile );

  }
}
