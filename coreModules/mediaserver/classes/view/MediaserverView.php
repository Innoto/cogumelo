<?php

Cogumelo::load('coreView/View.php');
mediaserver::autoIncludes();

class MediaserverView extends View {

  private $mediaserverControl;

  public function __construct( $base_dir ) {
    parent::__construct( $base_dir );

    $this->mediaserverControl = new MediaserverController();
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  public function accessCheck() {
    return true;
  }

  // load media from app
  public function application( $url_path = '' ) {


    preg_match('#(.*)#', $url_path[1], $result);
    $filePath = explode('?', $result[1]);

    $this->mediaserverControl->servecontent($filePath[0]);
    CacheUtilsController::removeLessTmpdir();
  }

  //load media from a module
  public function module( $url_path = '' ) {

    //preg_match('#/(.*?)/(.*)#', $url_path[1], $result);
    preg_match('#/(.*?)/(.*)#', $url_path[1], $result);


    $filePath = explode('?', $result[2]);

    if( $result != array() ) {
      $this->mediaserverControl->servecontent($filePath[0], $result[1]);
      CacheUtilsController::removeLessTmpdir();
    }
    else {
      Cogumelo::error('Mediaserver module receives empty request');
      RequestController::redirect(SITE_URL_CURRENT.'/404');
    }

  }

  // for less client includes
  public function onClientLess( $request ) {
    if( $request[1] == false ) {
      $this->mediaserverControl->serveContent($request[2].'.less');
    }
    else {
      $moduleName = mb_substr( $request[1], 0, mb_strlen( $request[1] )-1 );

      $this->mediaserverControl->serveContent($request[2].'.less', $moduleName);
    }
  }

}
