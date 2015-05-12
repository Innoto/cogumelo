<?php
Cogumelo::load("coreController/Module.php");

class i18nGetLang extends Module
{
    
  public $name = "i18nGetLang";
  public $version = "";
  public $dependences = array();
  
  
  function __construct(){

  	global $lang_available;
  	$lang_array = explode(',',LANG_AVAILABLE);
  	foreach ($lang_array as $l=>$lang){
  		$lang_short = explode('_',$lang);
  		$lang_available[$lang_short[0]] = $lang;
  	}

  	$this->addUrlPatterns( '#^(es|en|gl)\/(.*)$#', 'noendview:GetLangView::setlang' );
  }

}