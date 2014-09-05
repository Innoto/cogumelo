<?php

Cogumelo::load('c_view/View');
mediaserver::load('controller/MediaserverController');

class MediaserverView extends View
{

  private $mediaserverControl;

  function __construct($base_dir){
    parent::__construct($base_dir);

    $this->mediaserverControl = new MediaserverController();
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  // load media from app
  function application($url_path=''){
    if($url_path == ''){
      Cogumelo::error('Mediaserver receives empty request');
      RequestController::redirect(SITE_URL_CURRENT.'/404');
    }
    $this->mediaserverControl->serveContent($url_path);
  }

  //load media from a module
  function module($url_path=''){

    preg_match('#/(.*?)/(.*)#', $url_path, $result);

    if( $result != array() ) {
      $this->mediaserverControl->servecontent($result[2], $result[1]);
    }
    else {
      Cogumelo::error('Mediaserver module receives empty request');
      RequestController::redirect(SITE_URL_CURRENT.'/404');
    }

  }

}
