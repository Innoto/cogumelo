<?php


class Cogumelo extends CogumeloClass
{

  public $dependences = array();
  public $includesCommon = array();


  function __construct() {
    parent::__construct();

    /*createForm*/
    $this->addUrlPatterns( '#^lostForm$#', 'view:CreateForm::lostForm' );
    $this->addUrlPatterns( '#^lostForm/u/(.*)#', 'view:CreateForm::updateLostForm' );
    $this->addUrlPatterns( '#^sendLostForm#', 'view:CreateForm::sendLostForm' );

    /*table*/
    $this->addUrlPatterns( '#^tableInterface$#', 'view:Tview::main');

    /*i18n*/
    $this->addUrlPatterns( '#^test#', 'view:Testi18n::translate' );

    /*FormsTest*/
    $this->addUrlPatterns( '#^loadform$#', 'view:Forms::loadForm' );
    $this->addUrlPatterns( '#^actionform$#', 'view:FormAction::actionForm' );
    $this->addUrlPatterns( '#^ajax_file_upload_parser$#', 'view:Forms::ajaxUpload' );
    $this->addUrlPatterns( '#^omeuphp$#', 'view:Forms::phpinfo' );

    /*FormsTestV2*/
    $this->addUrlPatterns( '#^loadformV2$#', 'view:FormsTestV2::loadForm' );
    $this->addUrlPatterns( '#^actionformV2$#', 'view:FormsTestV2::actionForm' );
    $this->addUrlPatterns( '#^ajax_file_uploadV2$#', 'view:FormsTestV2::ajaxUpload' );

    /*Adminview*/
    $this->addUrlPatterns( '#^getobj$#', 'view:Adminview::getobj' );
    $this->addUrlPatterns( '#^setobj$#', 'view:Adminview::setobj' );

    /*MasterView*/
    $this->addUrlPatterns( '#^404$#', 'view:MasterView::page404' );
    $this->addUrlPatterns( '#^$#', 'view:MasterView::master' ); // App home url



  }

}
