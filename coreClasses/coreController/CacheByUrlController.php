<?php

class CacheByUrlController {
  var $urlCacheControl;
  public function __construct(  ) {

    register_shutdown_function(function(){
      $this->shutdown();
    });

    if( $this->urlMatch() !== false ) {
      require_once( COGUMELO_LOCATION.'/coreClasses/coreController/Cache.php' );

      $this->urlCacheControl =  new Cache();

      if($this->urlCacheControl->getCache( $this->getCurrentUrl() ) ) {
        exit;
      }
      else {
        ob_start();
      }

    }

  }

  private function urlMatch() {

    $ret = false;

    $langsConf = Cogumelo::getSetupValue( 'lang:available' );
    $patron = is_array( $langsConf ) ? implode( '|', array_keys( $langsConf ) ) : Cogumelo::getSetupValue( 'lang:default' );
    preg_match( '#^('.$patron.')?\/?(.*)$#', $this->getCurrentUrl(), $m ); // lang is out fromo url

    $cacheByUrlConfFile = APP_BASE_PATH.'/conf/setup-455.cacheByUrl.php';

    if( file_exists($cacheByUrlConfFile) ) {
      require_once $cacheByUrlConfFile;
      $arrayUrlCaches = Cogumelo::getSetupValue( 'cogumelo:cache:cacheByUrl' );
      /*if(isset(Cogumelo::getSetupValue( 'cogumelo:cache:cacheByUrl')[ $m[2] ]) ) {
        $ret = $m[2]; // get cache time
        echo $ret;
      }*/
      if(is_array($arrayUrlCaches) && sizeof($arrayUrlCaches)>0) {

        foreach( $arrayUrlCaches as $uc ){
          if( $m[2] == $uc['url'] ) {
            $ret = $uc['cacheTime'];
          }
        }

      }
    }

    return $ret;
  }

  private function getCurrentUrl() {
    return preg_replace('#\/$#', '', preg_replace('#^'.SITE_FOLDER.'#', '', $_SERVER['REQUEST_URI'], 1) , 1);
  }

  private function shutdown() {

    if( $this->urlMatch() !== false ) {

      $cacheTime = $this->urlMatch();


      if( $this->urlCacheControl->getCache($this->getCurrentUrl()) ) {
        echo $this->urlCacheControl->getCache( $this->getCurrentUrl());
      }
      else {
        $urlCacheContent = ob_get_contents();
        ob_end_clean();
        $this->urlCacheControl->setCache( $this->getCurrentUrl(), $urlCacheContent, $cacheTime );

        echo $urlCacheContent;
      }


    }

  }

}
