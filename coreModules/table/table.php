<?php

Cogumelo::load("coreController/Module.php");

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
    'controller/ExportTableController.php',
    'controller/CsvExportTableController.php',
    'controller/XlsExportTableController.php',
    'controller/TableController.php',
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