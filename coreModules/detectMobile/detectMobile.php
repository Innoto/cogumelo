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
      "includes" => array("mobiledetectlib/Mobile_Detect.php")
    )

  );


  public $includesCommon = array(
    'controller/DetectMobileController.php'
  );


  public function __construct() {
/*
    $this->addUrlPatterns( '#^cgml-form-htmleditor-config.js#', 'view:FormConnector::customCkeditorConfig' );
    $this->addUrlPatterns( '#^cgml-form-file-upload$#', 'view:FormConnector::fileUpload' );
    $this->addUrlPatterns( '#^cgml-form-group-element$#', 'view:FormConnector::groupElement' );
*/
  }
}
