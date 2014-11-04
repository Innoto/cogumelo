<?php

Cogumelo::load('c_view/View.php');
common::autoIncludes();
Cogumelo::autoIncludes();

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

    common::autoIncludes();
    $this->common();
    $this->template->exec();

  }

  function common() {
    $this->template->addClientScript('js/default.js');
    $this->template->setTpl('default.tpl');
  }

  function page404() {
    echo 'PAGE404: Recurso non atopado';
  }

}

