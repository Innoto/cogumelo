<?php

Cogumelo::load("c_controller/Module.php");

define('MOD_ADMIN_URL_DIR', 'admin');

class admin extends Module
{
  public $name = "admin";
  public $version = "";
  public $dependences = array(
    array(
     "id" => "bootstrap",
     "params" => array("bootstrap"),
     "installer" => "bower",
     "includes" => array("dist/bootstrap.min.css", "dist/bootstrap.min.js")
    ),
    array(
     "id" => "font-awesome",
     "params" => array(),
     "installer" => "manual",
     "includes" => array("css/font-awesome.min.css")
    ),
    array(
     "id" =>"metismenu",
     "params" => array("metisMenu"),
     "installer" => "bower",
     "includes" => array("metisMenu.min.css", "metisMenu.min.js")
    ),
    array(
     "id" =>"html5shiv",
     "params" => array("html5shiv --save-dev"),
     "installer" => "bower",
     "includes" => array("html5shiv.js")
    ),
    array(
     "id" =>"respond",
     "params" => array(),
     "installer" => "manual",
     "includes" => array("respond.js")
    ),
    array(
     "id" =>"morris",
     "params" => array(),
     "installer" => "manual",
     "includes" => array("morris.js", "morris.css")
    )

  );

  public $includesCommon = array(

  );

  function __construct() {
    $this->addUrlPatterns( '#^'.MOD_ADMIN_URL_DIR.'$#', 'view:MasterView::main' );
  }
}