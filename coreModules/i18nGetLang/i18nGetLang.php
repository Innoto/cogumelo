<?php
Cogumelo::load("coreController/Module.php");

class i18nGetLang extends Module
{

  public $name = "i18nGetLang";
  public $version = 1.0;
  public $dependences = array();


  public function __construct(){

    $i = 0;
    $langsConf = Cogumelo::getSetupValue( 'lang:available' );
    $patron = is_array( $langsConf ) ? implode( '|', array_keys( $langsConf ) ) : cogumeloGetSetupValue( 'lang:default' );
    /*
      if( $langsConf ) {
        foreach( $langsConf as $l => $lang ) {
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
        $patron = Cogumelo::getSetupValue( 'lang:default' );
      }
    */

    $this->addUrlPatterns( '#^('.$patron.')\/(.*)$#', 'noendview:GetLangView::setlang' );
    $this->addUrlPatterns( '#^('.$patron.')\/?()$#', 'noendview:GetLangView::setlang' );
  }

}
