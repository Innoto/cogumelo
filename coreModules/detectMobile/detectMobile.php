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


  public $includesCommon = [];


  public function __construct() {
    if( is_file( Cogumelo::getSetupValue( 'dependences:composerPath').'/mobiledetect/mobiledetectlib/Mobile_Detect.php' ) ) {
      require_once( Cogumelo::getSetupValue( 'dependences:composerPath').'/mobiledetect/mobiledetectlib/Mobile_Detect.php');

      $detect = new Mobile_Detect;
      Cogumelo::setSetupValue( 'mod:detectMobile:isMobile', $detect->isMobile() );
      Cogumelo::setSetupValue( 'mod:detectMobile:isTablet', $detect->isTablet() );

      Cogumelo::mergeSetupValue( 'mod:mediaserver:publicConf:javascript:setupFields',
        [ 'mod:detectMobile:isMobile', 'mod:detectMobile:isTablet' ] );
    }
    else {
      Cogumelo::error('Mobile_detect.php dpendence not found. Execute ./cogumelo installDependences to install it.');
    }
  }

}
