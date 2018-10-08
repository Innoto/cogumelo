<?php

Cogumelo::load("coreController/Module.php");

class common extends Module {

  public $name = "common";
  public $version = 1.0;
  public $autoIncludeAlways = true;

  public $dependences = array(
   array(
     "id" =>"less",
     "params" => array("less"),
     "installer" => "yarn",
     "includes" => array()
   ),
   array(
     'id' =>'lobibox',
     'params' => [ 'lobibox' ],
     'installer' => 'yarn',
     'includes' => [ 'dist/css/lobibox.min.css', 'dist/js/lobibox.min.js' ]
   )

  );

  public $includesCommon = array(
    'js/clientMsg.js'
  );


  public function __construct() {
    //$this->addUrlPatterns( regex, destination );
  }

}
