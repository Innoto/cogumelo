<?php

Cogumelo::load("coreController/Module.php");

class filedata extends Module {

  public $name = "filedata";
  public $version = "";
  public $dependences = array();
  public $includesCommon = array(
    'model/FiledataModel.php',
    'view/FiledataWeb.php'
  );


  public function __construct() {
    $this->addUrlPatterns( '#^cgmlfilews/(\d+).*$#', 'view:FiledataWeb::webShow' );
    $this->addUrlPatterns( '#^cgmlfilewd/(\d+).*$#', 'view:FiledataWeb::webDownload' );
    $this->addUrlPatterns( '#^cgmlformfilews/(\d+).*$#', 'view:FiledataWeb::webFormFileShow' );
    $this->addUrlPatterns( '#^cgmlformfilewd/(\d+).*$#', 'view:FiledataWeb::webFormFileDownload' );
  }

}