<?php
Cogumelo::load("c_controller/Module");

class i18nGetLang extends Module
{
    
  public $name = "i18nGetLang";
  public $version = "";
  public $dependencies = array();
  
  
  function __construct(){
  $this->addUrlPatterns( '#^(es/?)(.*)$#', 'noendview:GetLang::setlang' );
  }

}