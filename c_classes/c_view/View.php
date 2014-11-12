<?php


Cogumelo::load('c_view/Template.php');


abstract class View {
  var $first_execution = true;
  var $template;

  function __construct($teplates_dir) {
    if($this->first_execution) {

      $first_execution = false;

      $this->template = new Template($teplates_dir);

      if(!$this->accessCheck()){
        Cogumelo::error('Acess error on view '. get_called_class() );
        exit;
      }
      else {
        Cogumelo::debug('accessCheck OK '. get_called_class() );
      }
    }
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  function accessCheck() {

    Cogumelo::error('You need to define "accessCheck" into View');

    return false;
  }

}

