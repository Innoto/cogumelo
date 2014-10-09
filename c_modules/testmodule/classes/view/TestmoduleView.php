<?php

Cogumelo::load('c_view/View.php');

class TestmoduleView extends View
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

  function inicio() {
    $this->template->setTpl("test.tpl", 'testmodule');
    $this->template->addClientStyles("styles/common.css", 'testmodule');
    $this->template->addClientStyles("styles/common2.css", 'testmodule');
    $this->template->exec();
  }
}