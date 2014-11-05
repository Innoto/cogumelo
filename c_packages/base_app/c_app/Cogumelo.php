<?php


class Cogumelo extends CogumeloClass
{

  public $dependences = array(
    array(
     "id" => "formstoneWallpaper",
     "params" => array("Wallpaper"),
     "installer" => "bower",
     "includes" => array("jquery.fs.wallpaper.js", "jquery.fs.wallpaper.css")
    )
  );
  public $includesCommon = array();


  function __construct() {
    parent::__construct();

    /*probandoMailing*/
    $this->addUrlPatterns( '#^probandomailing$#', 'view:MailingView::probandoMailing');

    /*createForm*/
    $this->addUrlPatterns( '#^lostform$#', 'view:CreateForm::lostForm' );
    $this->addUrlPatterns( '#^lostform/u/(.*)#', 'view:CreateForm::updateLostForm' );
    $this->addUrlPatterns( '#^sendlostform#', 'view:CreateForm::sendLostForm' );
    $this->addUrlPatterns( '#^deletefostform#', 'view:CreateForm::deleteLostForm' );

    /*table*/
<<<<<<< HEAD
    $this->addUrlPatterns( '#^tableinterfacedata$#', 'view:Tview::tableData');    
    $this->addUrlPatterns( '#^tableInterface$#', 'view:Tview::main');
=======
    $this->addUrlPatterns( '#^tableinterface$#', 'view:Tview::main');
>>>>>>> a065b2819d1223157527951abaaeaa803385aa29

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
