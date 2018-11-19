<?php

Cogumelo::load("coreController/Module.php");

class common extends Module {

  public $name = "common";
  public $version = 1.0;
  public $autoIncludeAlways = true;

  public $dependences = array(
   array(
     "id" =>"less",
     "params" => array("less#v2.7.3"),
     "installer" => "bower",
     "includes" => array()
   ),
   array(
     'id' =>'lobibox',
     'params' => [ 'lobibox' ],
     'installer' => 'bower',
     'includes' => [ 'dist/css/lobibox.min.css', 'dist/js/lobibox.min.js' ]
   )

  );

  public $includesCommon = array(
    //'styles/common.less',
    'js/clientMsg.js',
    'js/cogumeloLog.js'
  );


  public function __construct() {
    //$this->addUrlPatterns( regex, destination );
  }

}
