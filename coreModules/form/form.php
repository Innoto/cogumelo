<?php

Cogumelo::load("coreController/Module.php");


class form extends Module
{


  public $name = "form";
  public $version = "";
  public $dependences = array(
   // BOWER
   array(
     "id" => "jquery-validation",
     "params" => array("jquery-validation"),
     "installer" => "bower",
     "includes" => array("dist/jquery.validate.js", "dist/additional-methods.js")
   )
  );


  public $includesCommon = array(
    'controller/FormController.php',
    'controller/FormValidators.php',
    'js/jquery.cogumeloFormController.js',
    'js/jquery.serializeFormToObject.js',
    'js/formValidators.js',
    'js/formValidatorsExtender.js',
    'styles/form.less'
  );


  public function __construct() {
    $this->addUrlPatterns( '#^cgml-form-file-upload$#', 'view:FormConnector::fileUpload' );
    $this->addUrlPatterns( '#^cgml-form-group-element$#', 'view:FormConnector::groupElement' );
  }


}
