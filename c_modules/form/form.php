<?php

Cogumelo::load("c_controller/Module");


class form extends Module
{
  public $name = "form";
  public $version = "";
  public $dependences = array(
   // BOWER   
   array(
     "id" => "jquery",
     "params" => array("jquery#1.*"),
     "installer" => "bower",
     "includes" => array("jquery.js")
   ),
   array(
     "id" => "jquery-validation",
     "params" => array("jquery-validation"),
     "installer" => "bower",
     "includes" => array("")
   )
  );

  public $includesCommon = array();
  
  function __construct() {
    
  }
}