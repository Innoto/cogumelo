<?php

Cogumelo::load('coreView/View.php');

class GetLangView extends View
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
      global $c_lang;
      if ($url_path)
        $c_lang = $url_path[1];
      else
        $c_lang = LANG_DEFAULT;
      //echo "<br> SET Lang global variables<br><br>";
  }
}