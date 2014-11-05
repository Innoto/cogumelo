<?php

Cogumelo::load("c_controller/Module.php");

class table extends Module
{
  public $name = "table";
  public $version = "";

  public $dependences = array(
  );

  public $includesCommon = array(
    'controller/TableController.php',
    'view/TableView.php',
    'js/table.js',
    'styles/table.less'
  );


 
  function __construct() {

  }

  static function getTableHtml( $tableId, $tableDataUrl ) {    
    return TableView::getTableHtml( $tableId, $tableDataUrl );
  }

}