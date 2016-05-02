<?php

class I18nController {
  /**
  * Prepare the enviroment to localize the project
  */
  public static function setLang( $url_path = false ) {
    // error_log( 'I18nController::setLang '.print_r( $url_path, true ) );

    global $C_LANG;

    if( $url_path ) {
      $C_LANG = $url_path[1];
    }
    else{
      $C_LANG = Cogumelo::getSetupValue( 'lang:default' );
    }

    $domain = 'messages';
    $langsAvailable = Cogumelo::getSetupValue( 'lang:available' );
    $locale = $langsAvailable[ $C_LANG ]['i18n'].'.utf8';
    $locale_dir = Cogumelo::getSetupValue( 'i18n:localePath' );

    setlocale( LC_ALL, $locale );
    putenv( "LC_ALL=$locale" );
    bindtextdomain( $domain, $locale_dir );
    bind_textdomain_codeset( $domain, 'utf8' );
    textdomain( $domain );
  }

  public static function getLang( $url ) {
    // error_log( 'I18nController::getLang '.print_r( $url, true ) );

    $m = self::processUrl($url);

    if( array_key_exists( 1, $m ) ) {
      return str_replace( '/', '', $m[1] );
    }
    else {
      return Cogumelo::getSetupValue( 'lang:default' );
    }
  }

  /*
  public static function extractUrl( $url ) {
    $m = self::processUrl($url);

    if(array_key_exists(2,$m)) {
      return $m[2];
    }
    else {
      return $url;
    }
  }
  */

  public static function processUrl( $url ) {
    $langsAvailable = array_keys( cogumeloGetSetupValue( 'lang:available' ) );
    foreach( $langsAvailable as $lng ) {
      if( preg_match( '^'.$lng.'/?', $url ) ) {
        $m = array( $url, $lng.'/', '' );
        break;
      }
      if( preg_match( '^('.$lng.'/)(.*)^', $url, $m ) ) {
        break;
      }
    }
    return $m;
  }

 /* Se a url actual non ten idioma, redirixe á páxina co idioma do navegador */
  public static function redirectLang( $page ) {

    $langsAvailable = array_keys( Cogumelo::getSetupValue( 'lang:available' ) );

    $browserLang_all = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $browserLang_parts = explode('-',$browserLang_all);
    $browserLang = $browserLang_parts[0];

    $currentUrl = explode('/', $_SERVER['REQUEST_URI']);

    switch($page){
      case 'home';
        if ($currentUrl[1]===''){ //home sen idioma
          Cogumelo::Redirect($_SERVER['REQUEST_URI'].$browserLang);
        }
        break;
      case 'explorer';
        $has_lang = false;
        foreach( $langsAvailable as $lng ) { // se ten idioma
          if ($currentUrl['1']===$lng){
            $has_lang = true;
          }
        }
        if(!$has_lang){
          Cogumelo::Redirect($browserLang.$_SERVER['REQUEST_URI']);
        }
        break;
    }
  }

}
