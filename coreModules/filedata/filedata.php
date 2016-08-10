<?php

Cogumelo::load("coreController/Module.php");

class filedata extends Module {

  public $name = 'filedata';
  public $version = 1.0;
  public $dependences = array();
  public $includesCommon = array(
    'controller/FiledataController.php',
    'model/FiledataModel.php'
  );


  public function __construct() {
    $this->addUrlPatterns( '#^cgmlImg/(?P<fileId>\d+?)(?P<profile>/.+?)?(?P<fileName>/.*)?$#', 'view:FiledataImagesView::showImg' );
    // $this->addUrlPatterns( '#^cgmlfilews/(\d+).*$#', 'view:FiledataWeb::webShow' );
    // $this->addUrlPatterns( '#^cgmlfilewd/(\d+).*$#', 'view:FiledataWeb::webDownload' );
    $this->addUrlPatterns( '#^cgmlformfilews/(\d+).*$#', 'view:FiledataWeb::webFormFileShow' );
    $this->addUrlPatterns( '#^cgmlformfilewd/(\d+).*$#', 'view:FiledataWeb::webFormFileDownload' );
  }

}
