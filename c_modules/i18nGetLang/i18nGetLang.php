<?php
Cogumelo::load("c_controller/Module");

class i18nGetLang extends Module
{
    
  public $name = "i18nGetLang";
  public $version = "";
  public $dependences = array();
  
  
  function __construct(){
  $this->addUrlPatterns( '#^(es/?)(.*)$#', 'noendview:GetLang::setlang' );
  }

}