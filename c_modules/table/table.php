<?php

Cogumelo::load("c_controller/Module.php");

class table extends Module
{
  public $name = "table";
  public $version = "";

  public $dependences = array(


   array(
     "id" =>"jquery.download",
     "params" => array("jquery.fileDownload#1.4.2"),
     "installer" => "bower",
     "includes" => array("src/Scripts/jquery.fileDownload.js")
   )


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