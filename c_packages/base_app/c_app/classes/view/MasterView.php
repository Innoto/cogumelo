<?php

Cogumelo::load('c_view/View');

/**
* Clase Master de la que extenderemos todos los View
*/
class MasterView extends View
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

  function master($urlPath=''){

/*
    $dependencesControl = new DependencesController();
    $dependencesControl->loadModuleIncludes('devel');
*/

    client::autoIncludes();
    $this->common();
    $this->template->exec();

  }

  function common() {
    $this->template->setTpl('default.tpl');
    $this->template->addClientStyles('styles/client.css', 'client');
  }

  function page404() {
    echo 'PAGE404: Recurso non atopado';
  }

}

