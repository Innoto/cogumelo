<?php


class i18nServer extends Module {

  public $name = 'i18nServer';
  public $version = '1.0';

  public $dependences = array(  );

  public $includesCommon = array();



  public function __construct() {
    global $C_LANG;
    $this->addUrlPatterns( '#^jsTranslations/getJson.js(\?.*)?$#', 'view:GetTranslations::getJson' );
  }

}
