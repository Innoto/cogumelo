<?php


class Cogumelo extends CogumeloClass
{

  function __construct() {
    parent::__construct();

    $this->addUrlPatterns( '#^loadform#', 'view:Forms::loadForm' );
    $this->addUrlPatterns( '#^ajax_file_upload_parser$#', 'view:Forms::ajaxUpload' );
    $this->addUrlPatterns( '#^omeuphp#', 'view:Forms::phpinfo' );

    $this->addUrlPatterns( '#^actionform#', 'view:FormAction::actionForm' );



    $this->addUrlPatterns( '#^getobj$#', 'view:Adminview::getobj' );
    $this->addUrlPatterns( '#^setobj$#', 'view:Adminview::setobj' );

    $this->addUrlPatterns( '#^404$#', 'view:MasterView::page404' );

    $this->addUrlPatterns( '#^$#', 'view:MasterView::master' ); // App home url

  }

}
