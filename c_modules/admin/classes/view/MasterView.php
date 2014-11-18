<?php
Cogumelo::load('c_view/View.php');

common::autoIncludes();
//form::autoIncludes();
//user::autoIncludes();


class UserView extends View
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

  function main(){
    echo "hola";
  }
}

