<?php

Cogumelo::load("c_controller/Module");

class client extends Module
{
  public $name = "client";
  public $version = "";
  
  public $dependencies = array(
   array(
     "id" =>"jquery",
     "params" => array("jquery#1.*"),
     "installer" => "bower",
     "load" => array("jquery.js")
   ),
   array(
     "id" =>"jquery-ui",
     "params" => array("jquery-ui"),
     "installer" => "bower",
     "load" => array("jquery-ui.js", "jquery-ui.css")
   )
  );  
    
    
  function __construct() {
    //$this->addUrlPatterns( regex, destination );
  }

}