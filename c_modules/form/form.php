<?php

Cogumelo::load("c_controller/Module.php");


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


  function __construct() {

    $this->addUrlPatterns( '#^cgmlFormFileUpload$#', 'view:FormFileUpload::fileUpload' );

  }


}
