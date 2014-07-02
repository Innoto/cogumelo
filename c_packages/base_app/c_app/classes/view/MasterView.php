<?php

Cogumelo::load('c_view/View');

/**
* Clase Master de la que extenderemos todos los View
*/
class MasterView extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  function master($url_path=''){
    $this->common();
    $this->template->exec();
  }

  function common() {
    $this->template->setTpl("default.tpl");
    $this->template->addJs("vendor/jquery.js");
  }

  function page404() {
    echo "PAGE404: Recurso non atopado";
  }

}

