<?php

class CsvExportTableController extends ExportTableController {

  function __construct($tableControl, $fileName, $dataDAOResult) {
    $this->headers($fileName);
    $this->data($tableControl, $dataDAOResult);
  }

  function headers($fileName) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
  }

  function data($tableControl, $dataDAOResult) {



    if($dataDAOResult != false) {
      while( $rowVO = $dataDAOResult->fetch() ) {

        // dump rowVO into row
        $row = array();
            
        $row['rowReferenceKey'] = $rowVO->getter( $rowVO->getFirstPrimarykeyId() ); 
        foreach($tableControl->colsDef as $colDefKey => $colDef){
          $row[$colDefKey] = $rowVO->getter($colDefKey);
        }

        
        // modify row value if have colRules
        foreach($tableControl->colsDef as $colDefKey => $colDef) {
          // if have rules and matches with regexp
          if($colDef['rules'] != array() ) {

            foreach($colDef['rules'] as $rule){
              if(preg_match( $rule['regexp'], $row[$colDefKey])) {
                eval('$row[$colDefKey] = "'.$rule['finalContent'].'";');
                break;
              }
            }
          }
        }

        echo utf8_encode("".implode(",", $row)."\n"); 

      }
    }


  }

}