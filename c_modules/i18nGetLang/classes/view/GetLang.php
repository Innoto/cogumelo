<?php

Cogumelo::load('c_view/View.php');

class GetLang extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  // load media from app
  function setlang($url_path=''){
    echo "<br> SET Lang global variables<br><br>";
  }
}