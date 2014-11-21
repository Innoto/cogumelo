<?php

Cogumelo::load("coreController/Module.php");

class filedata extends Module
{
  public $name = "filedata";
  public $version = "";
  public $dependences = array(

  );


  public $includesCommon = array(
    'controller/FiledataController.php',
    'model/FiledataVO.php'
  );



  function __construct() {


  }
}