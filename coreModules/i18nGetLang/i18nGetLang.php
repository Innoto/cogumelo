<?php
Cogumelo::load("coreController/Module.php");

class i18nGetLang extends Module
{
    
  public $name = "i18nGetLang";
  public $version = "";
  public $dependences = array();
  
  
  function __construct(){

  	global $LANG_AVAILABLE;

    $i = 0;
    foreach ($LANG_AVAILABLE as $l=>$lang){
      $lang_array[$i] = $l;
      $i = $i +1;
    }

    $num = count($LANG_AVAILABLE);

    for ($j=0;$j<$num;$j++){
      if ($j==0)
        $patron = $lang_array[$j];
      else if ($j==$num-1)
        $patron = $patron.'|'.$lang_array[$j];
      else
        $patron = $patron .'|'.$lang_array[$j];
    }

  	$this->addUrlPatterns( '#^('.$patron.')\/(.*)$#', 'noendview:GetLangView::setlang' );
  }

}