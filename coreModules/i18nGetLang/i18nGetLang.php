<?php
Cogumelo::load("coreController/Module.php");

class i18nGetLang extends Module
{
    
  public $name = "i18nGetLang";
  public $version = "";
  public $dependences = array();
  
  
  function __construct(){
  	global $c_lang;
  	$c_lang  = 'gl';
  	$this->addUrlPatterns( '#^(gl/?)(.*)$#', 'noendview:GetLang::setlang' );
  }

}