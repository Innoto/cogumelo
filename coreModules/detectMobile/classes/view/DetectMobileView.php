<?php

Cogumelo::load('coreView/View.php');
detectMobile::autoIncludes();
class DetectMobileView extends View
{

  public function __construct( $base_dir ) {
    parent::__construct( $base_dir );
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  public function accessCheck() {
    return true;
  }

  // load media from app
  public function detectMobile( ) {


    $detect = new Mobile_Detect;
    $isMobile = $detect->isMobile();
    Cogumelo::setSetupValue( 'cogumelo:detectMobile:isMobile', $isMobile );

    var_dump(Cogumelo::getSetupValue( 'cogumelo:detectMobile:isMobile' ));
  }
}
