<?php

class XlsExportTableController extends ExportTableController {

  function __construct($tableControl, $fileName, $dataDAOResult) {
    $this->headers($fileName, $tableControl);
    $this->data($tableControl, $dataDAOResult);
  }

  function headers($fileName) {
    header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Export.xsl");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
  }

  function data($tableControl, $dataDAOResult) {

    echo "<html>\n";
    echo "<body>\n";
    echo "<table class='table table-striped'>\n";
    echo "<tbody>\n";


    // excell HEADER
    echo "<tr>\n";
    echo "<th>\n";
    echo utf8_decode(implode("</th>\n<th>", array_merge($tableControl->colsIntoArray(), $tableControl->colsToExportIntoArray()) ));
    echo "</th>\n";
    echo "</tr>\n";

    if($dataDAOResult != false) {


      while( $rowVO = $dataDAOResult->fetch() ) {


        // dump rowVO into row
        $row = array();

        $row['rowReferenceKey'] = $rowVO->getter( $rowVO->getFirstPrimarykeyId() );
        $rowId = $row['rowReferenceKey'];

        $colsDefFinal = array_merge( $tableControl->colsDef, $tableControl->colsDefToExport );

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

        }

        // modify row value if have colRules
        foreach($tableControl->colsDef as $colDefKey => $colDef) {
          // if have rules and matches with regexp
          if($colDef['rules'] != array() ) {


            foreach($colDef['exportRules'] as $rule){
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


        echo utf8_decode("<tr><td>".implode("</td><td>", $row)."</td></tr>\n");
        //echo "".implode(",", $row)."\n";

      }


      echo "</tbody>\n";
      echo "</table>\n";
      echo "</body>\n";
      echo "</html>\n";


    }


  }

}
