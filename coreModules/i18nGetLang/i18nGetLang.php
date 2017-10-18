<?php
Cogumelo::load("coreController/Module.php");

class i18nGetLang extends Module {

  public $name = "i18nGetLang";
  public $version = 1.0;
  public $dependences = array();


  public function __construct(){
    $langsConf = Cogumelo::getSetupValue( 'lang:available' );
    $patron = is_array( $langsConf ) ? implode( '|', array_keys( $langsConf ) ) : Cogumelo::getSetupValue( 'lang:default' );
    $this->addUrlPatterns( '#^('.$patron.')\/(.*)$#', 'noendview:GetLangView::setlang' );
    $this->addUrlPatterns( '#^('.$patron.')\/?(\?.*)$#', 'noendview:GetLangView::setlang' );
  }

}
