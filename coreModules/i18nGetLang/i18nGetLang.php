<?php
Cogumelo::load("coreController/Module.php");

class i18nGetLang extends Module
{

  public $name = "i18nGetLang";
  public $version = "";
  public $dependences = array();


  public function __construct(){

    global $LANG_AVAILABLE;

    $i = 0;
    if( $LANG_AVAILABLE ) {
      foreach( $LANG_AVAILABLE as $l => $lang ) {
        $lang_array[$i] = $l;
        $i = $i +1;
      }

      for( $j=0; $j<$i; $j++ ) {
        if( $j===0 ) {
          $patron = $lang_array[$j];
        }
        else if( $j===$i-1 ) {
          $patron = $patron.'|'.$lang_array[$j];
        }
        else {
          $patron = $patron .'|'.$lang_array[$j];
        }
      }
    }
    else {
      $patron = LANG_DEFAULT;
    }
    // error_log( 'i18nGetLang patron: '.$patron );

    $this->addUrlPatterns( '#^('.$patron.')\/(.*)$#', 'noendview:GetLangView::setlang' );
    $this->addUrlPatterns( '#^('.$patron.')\/?()$#', 'noendview:GetLangView::setlang' );
  }

}