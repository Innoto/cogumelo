<?php

Cogumelo::load('coreView/View.php');
mediaserver::autoIncludes();

class MediaserverView extends View
{

  private $mediaserverControl;

  function __construct($base_dir){
    parent::__construct($base_dir);

    $this->mediaserverControl = new MediaserverController();
  }

  /**
  * Evaluate the access conditions and report if can continue
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
    $this->mediaserverControl->serveContent($url_path[1]);
    CacheUtilsController::removeLessTmpdir();
  }

  //load media from a module
  function module($url_path=''){

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
  function onClientLess($request){
    if( $request[1] == false){
      $this->mediaserverControl->serveContent($request[2].'.less');
    }
    else {
      $moduleName = substr($request[1], 0, strlen($request[1])-1);

      $this->mediaserverControl->serveContent($request[2].'.less', $moduleName);
    }
  }

}
