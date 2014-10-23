<?php
Cogumelo::load('c_view/View.php');
client::autoIncludes();

class TableView extends View
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

  function main() {
    $this->template->setTpl('table.tpl');
    $this->template->exec();
  } // function loadForm()
}

