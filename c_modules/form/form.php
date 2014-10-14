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
    'js/jquery-cogumelo-forms.js',
    'js/jquery.serializeFormToObject.js',
    'js/inArray.js',
    'js/regex.js',
    'js/numberEU.js',
    'js/timeMaxMin.js',
    'js/dateMaxMin.js',
    'js/dateTimeMaxMin.js',
    'js/appValidateMethods.js'
  );

  function __construct() {

  }
}