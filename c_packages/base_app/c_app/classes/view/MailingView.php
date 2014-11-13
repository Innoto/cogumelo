<?php

Cogumelo::load('c_view/View.php');
Cogumelo::load('controller/CogumeloMailController.php');
/**
* Clase Master de la que extenderemos todos los View
*/
class MailingView extends View
{

  function __construct($baseDir){
    parent::__construct($baseDir);
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  function probandoMailing(){

    $controlCogumeloMail =  new CogumeloMailController();
    $controlCogumeloMail->CogumeloSendMail('arodriguez@map-experience.com');
  }
}

