<?php

Cogumelo::load('coreView/View.php');

class GetLangView extends View
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
  public function setlang( $url_path = '' ) {
    // error_log( 'GetLangView::setlang '.print_r( $url_path, true ) );

    Cogumelo::load( 'coreController/I18nController.php' );
    I18nController::setLang( $url_path );

    //echo "<br> SET Lang global variables<br><br>";
  }
}
