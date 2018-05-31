<?php

Cogumelo::load("coreController/Module.php");

class filedata extends Module {

  public $name = 'filedata';
  public $version = 2;
  public $dependences = array();
  public $includesCommon = array(
    'controller/FiledataController.php',
    'model/FiledataModel.php'
  );


  public function __construct() {
    // Ej: cgmlImg/5-aDFG345/fastCut/algo.jpg
    $this->addUrlPatterns( '#^cgmlImg/(?P<fileId>\d+?)(-a(?P<aKey>[^\/]+?))?(?P<profile>/.+?)?(?P<fileName>/.*)?$#', 'view:FiledataImagesView::showImg' );
    $this->addUrlPatterns( '#^cgmlformfilews/(?P<fileId>\d+)(-a(?P<aKey>[^\/]+?))?(?P<fileName>/.*)?$#', 'view:FiledataWeb::webFormFileShow' );
    $this->addUrlPatterns( '#^cgmlformfilewd/(?P<fileId>\d+)(-a(?P<aKey>[^\/]+?))?(?P<fileName>/.*)?$#', 'view:FiledataWeb::webFormFileDownload' );
    $this->addUrlPatterns( '#^cgmlformpublic/(.*)$#', 'view:FiledataWeb::webFormPublic' );
    // $this->addUrlPatterns( '#^cgmlfilews/(\d+).*$#', 'view:FiledataWeb::webShow' );
    // $this->addUrlPatterns( '#^cgmlfilewd/(\d+).*$#', 'view:FiledataWeb::webDownload' );
  }

}
