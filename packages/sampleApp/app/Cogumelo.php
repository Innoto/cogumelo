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
    $this->addUrlPatterns( '#^tableinterfacedata$#', 'view:Tview::tableData');
    $this->addUrlPatterns( '#^tableInterface$#', 'view:Tview::main');

    /*i18n*/
   // $this->addUrlPatterns( '#^test#', 'view:Testi18n::translate' );

    /*FormModTest*/
    $this->addUrlPatterns( '#^form-mod-test$#', 'view:FormModTest::loadForm' );
    $this->addUrlPatterns( '#^form-mod-action$#', 'view:FormModTest::actionForm' );

    $this->addUrlPatterns( '#^testdata$#', 'view:MasterView::testdata' );

    /*Adminview*/
    $this->addUrlPatterns( '#^getobj$#', 'view:Adminview::getobj' );
    $this->addUrlPatterns( '#^setobj$#', 'view:Adminview::setobj' );

    /*MasterView*/
    $this->addUrlPatterns( '#^404$#', 'view:MasterView::page404' );
    $this->addUrlPatterns( '#^$#', 'view:MasterView::master' ); // App home url


  }

}
