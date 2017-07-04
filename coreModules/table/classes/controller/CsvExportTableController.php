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
        $rowId = $row['rowReferenceKey'];

        $colsDefFinal = $tableControl->colsDef;
        foreach( $tableControl->colsDefToExport as $cdefK => $cdef ) {

            $colsDefFinal[$cdefK] = $cdef;

        }

        foreach( $colsDefFinal as $colDefKey => $colDef) {

          if( preg_match('#^(.*)\.(.*)$#', $colDefKey, $m )) {

            $depList = $rowVO->getterDependence('id', $m[1] );


            if( is_array($depList) && count($depList)>0 ) {
              //Cogumelo::console($depList);
              if(isset($depList[0])) {
                $row[$colDefKey] = $depList[0]->getter($m[2]);
              }
              else {
                $row[$colDefKey] = array_pop($depList)->getter($m[2]);
              }


            }
            else {

              $row[$colDefKey] = '' ;

            }
          }
          else {
            $row[$colDefKey] = $rowVO->getter($colDefKey);
          }

          $row[$colDefKey] = '"'.$row[$colDefKey].'"';

        }

        // modify row value if have colRules
        foreach($colsDefFinal as $colDefKey => $colDef) {
          // if have rules and matches with regexp

          //var_dump($colDef);
          if( isset($colDef['rules']) >0) {




            foreach($colDef['rules'] as $rule){
              if( !isset( $rule['regexContent'] ) ) {
                if(preg_match( $rule['regexp'], $row[$colDefKey])) {
                  eval('$row[$colDefKey] = "'.$rule['finalContent'].'";');
                  break;
                }
              }
              else {
                //$row[$colDefKey] = preg_replace( $rule['regexp'], $rule['regexContent'], $row[$colDefKey] );
                if( $row[$colDefKey] = preg_replace( $rule['regexp'], $rule['regexContent'], $row[$colDefKey] ) ) {
                  break;
                }
              }
            }



          }

        }

        //var_dump( array_keys($row) );
        unset($row['rowReferenceKey']);



        echo  implode(",", $row)."\n";


      }
    }

  }

}
