<?php

Cogumelo::load('coreView/View.php');

class TestmoduleView extends View
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

  function inicio() {
    $this->template->setTpl("test.tpl", 'testmodule');
    $this->template->addClientStyles("styles/common.css", 'testmodule');
    $this->template->addClientStyles("styles/common2.css", 'testmodule');
    $this->template->exec();
  }
}