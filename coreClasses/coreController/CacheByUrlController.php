<?php

class CacheByUrlController {
  public function __construct(  ) {
    register_shutdown_function(function(){
      $this->sutdown();
    });

    if( $this->urlMatch() === true ) {
      $this->urlCacheMatches = true;
      ob_start();
    }
  }

  private function urlMatch() {
    //var_dump( $this->getCurrentUrl() );
    return false;
  }

  private function getCurrentUrl() {
    return preg_replace('#\/$#', '', preg_replace('#^'.SITE_FOLDER.'#', '', $_SERVER['REQUEST_URI'], 1) , 1);
  }

  private function sutdown() {
    if( $this->urlMatch() === true ) {
      $urlCacheContent = ob_get_contents();
      ob_end_clean();
      echo 'CONTIDO CACHEADO'.$urlCacheContent;
    }
  }

}
