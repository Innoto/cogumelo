<?php


class i18nServer extends Module {

  public $name = 'i18nServer';
  public $version = '1.0';

  public $dependences = array(
    array(
      "id" => "po-to-json-master",
      "params" => array("po-to-json-master"),
      "installer" => "manual",
      "includes" => array("po2json.php")
    )
  );

  public $includesCommon = array();



  public function __construct() {
    global $C_LANG;
    $this->addUrlPatterns( '#^jsTranslations/getJson.js$#', 'view:GetTranslations::getJson' );
  }

}
