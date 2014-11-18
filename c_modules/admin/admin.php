<?php

Cogumelo::load("c_controller/Module.php");

define('MOD_USER_URL_DIR', 'admin');

class admin extends Module
{
  public $name = "admin";
  public $version = "";
  public $dependences = array(

  );

  public $includesCommon = array(

  );

  function __construct() {
    $this->addUrlPatterns( '#^'.MOD_USER_URL_DIR.'/(*)$#', 'view:MasterView::main' );
  }
}