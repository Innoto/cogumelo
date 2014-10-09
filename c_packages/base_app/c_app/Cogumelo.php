<?php


class Cogumelo extends CogumeloClass
{

  public $dependences = array(
    // BOWER   
    array(
      "id" => "jquery1.7",
      "params" => array("jquery#1.7"),
      "installer" => "bower",
      "includes" => array("jquery.js")
    ),  
    // COMPOSER 

    array(
      "id" => "simpleExcel",
      "params" => array("faisalman/simple-excel-php" , "dev-master"),
      "installer" => "composer",
      "includes" => array("yii.php")
    )
  );

  public $includesCommon = array();  
  
  
  function __construct() {
    parent::__construct();
    
    /*createForm*/
    $this->addUrlPatterns( '#^lostForm#', 'view:CreateForm::lostForm' );    
    $this->addUrlPatterns( '#^sendLostForm#', 'view:CreateForm::sendLostForm' );
    /*i18n*/
    $this->addUrlPatterns( '#^test#', 'view:Testi18n::translate' );    
    
    /*Forms*/
    $this->addUrlPatterns( '#^loadform#', 'view:Forms::loadForm' );
    $this->addUrlPatterns( '#^ajax_file_upload_parser$#', 'view:Forms::ajaxUpload' );
    $this->addUrlPatterns( '#^omeuphp#', 'view:Forms::phpinfo' );
    /*FormAction*/
    $this->addUrlPatterns( '#^actionform#', 'view:FormAction::actionForm' );
    /*Adminview*/
    $this->addUrlPatterns( '#^getobj$#', 'view:Adminview::getobj' );
    $this->addUrlPatterns( '#^setobj$#', 'view:Adminview::setobj' );
    /*MasterView*/
    $this->addUrlPatterns( '#^404$#', 'view:MasterView::page404' );
    $this->addUrlPatterns( '#^$#', 'view:MasterView::master' ); // App home url

  }

}
