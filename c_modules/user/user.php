<?php

Cogumelo::load("c_controller/Module.php");

define('MOD_USER_URL_DIR', 'user');

class user extends Module
{
  public $name = "user";
  public $version = "";
  public $dependences = array(

  );

  public $includesCommon = array(

  );

  function __construct() {
    $this->addUrlPatterns( '#^'.MOD_USER_URL_DIR.'/loginform$#', 'view:UserView::loginForm' );
  }
}