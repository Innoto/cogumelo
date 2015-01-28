<?php

Cogumelo::load('coreView/View.php');
common::autoIncludes();
Cogumelo::autoIncludes();

/**
* Clase Master to extend other application methods
*/
class MasterView extends View
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

  function master($urlPath=''){

/*
    $dependencesControl = new DependencesController();
    $dependencesControl->loadModuleIncludes('devel');
*/

    $this->common();
    $this->template->exec();
  }

  function common() {
    $this->template->addClientScript('js/default.js');
    $this->template->setTpl('default.tpl');
  }

  function testdata(){
    echo "<pre>";


    user::load('model/UserModel.php');
    
    $user = new UserModel();

    $users = $user->listItems();
    while ($u  = $users->fetch() ) {
      echo $u->getter('surname')."\n";  
    }
    
  }

  function page404() {
    echo 'PAGE404: Recurso non atopado';
  }

}

