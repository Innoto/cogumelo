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
  * Evaluar las condiciones de acceso y reportar si se puede continuar
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

