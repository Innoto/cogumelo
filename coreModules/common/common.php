<?php

Cogumelo::load("coreController/Module.php");

class common extends Module
{
  public $name = "common";
  public $version = "";

  public $dependences = array(
   array(
     "id" =>"less",
     "params" => array("less"),
     "installer" => "bower",
     "includes" => array()
   )
  );

  public $includesCommon = array(
    'styles/common.less'
  );


  function __construct() {
    //$this->addUrlPatterns( regex, destination );
  }

}
