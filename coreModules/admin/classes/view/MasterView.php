<?php
Cogumelo::load('coreView/View.php');

common::autoIncludes();
admin::autoIncludes();
//form::autoIncludes();
//user::autoIncludes();


class MasterView extends View
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
    $this->template->setTpl('loginForm.tpl', 'admin');
    $this->template->exec();
  }
}

