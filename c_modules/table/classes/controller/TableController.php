<?php


/**
* TableController Class
*
* This class provides a backend to comunicate a js-ajax table system 
* with a cogumelo data controller, selecting what cools we want to show and 
* what to do with data in special cases.
*
* @author: pablinhob
*/

class TableController{


  var $clientData = array();
  var $colsDef = array();
  var $allowMethods = array(
      'list' => 'listItems',
      'delete' => false,
      'chageStatus' => false
      );

  var $tabs = array();
  var $currentTab = false;
  var $filters = array();

  /*
  *  @param array $data  generally is the full $_POST data variable
  */
  function __construct($postdata)
  {
    if(!array_key_exists('cogumeloTable', $postdata)){
      Cogumelo::error('$_POST array doesn`t contents a cogumeloTable key');
      exit;
    };

    $this->clientData = json_decode( $postdata['cogumeloTable'] );
  }

  /*
  * Set table col
  *
  * @param string $colId id of col in VO
  * @param string $colName. if false it gets the VO's col description.
  * @return void
  */
  function setCol($colId, $colName = false) {
    $this->colsDef[$colId] = array('name' => $colName, 'rules' => array() ); 
  }


  /*
  * Set col Rules
  *
  * @param string $colId id of col added with setCol method0
  * @param mixed $regexp the regular expression to match col's row value
  * @param string $finalContent is the result that we want to provide when 
  *  variable $value matches (Usually a text). Can be too an operation with other cols
  * @return void
  */
  function colRule($colId, $regexp, $finalContent) {
    if(array_key_exists($colId, $this->colsDef)) {
      $this->colsDef[$colId]['rules'][] = array('regexp' => $regexp, 'finalContent' => $finalContent );
    }
    else {
      Cogumelo::error('Col id "'.$colId.'" not found in table, can`t add col rule');
    }
  }

  /*
  * Set tabs
  *
  * @param string $tabsKey
  * @param array $tabs
  * @return void
  */
  function setTabs($tabsKey ,$tabs) {
    $this->tabs = array('tabsKey' => $tabsKey, 'tabs' => $tabs);
  }


  /*
  * Set filters array
  *
  * @param array $filters 
  * @return void
  */
  function setFilters( $filters ) {
    $this->filters = $filters;
  }


  /*
  * Data methods to allow in table
  *
  * @param string $allowMethods array of method names allowed in this table view
  * @return void
  */
  function allowMethods( $allowMethods ) {
    $this->allowMethods = $allowMethods;
  }


  /*
  * @param object $control: is the data controller
  * @return string JSON with table
  */
  function returnTableJson($control) {

    // if is executing a method ( like delete or update) and have permissions to do it
    if($this->clientData->method && array_key_exists( $this->clientData->method->name, $this->allowMethods ))
    {
      eval( '$control->'. $this->clientData->method->name. '('.$this->clientData->method->value.')');
    }


    // doing a query to the controller
    eval('$lista = $control->'. $this->allowMethods['list'].'( $this->clientData->filters , $this->clientData->range, $this->clientData->order);');


    // printing json table...

    header("Content-Type: application/json"); //return only JSON data
    
    echo "{";
    echo '"colsDef":'.json_encode($this->colsDef).',';
    echo '"tabs":'.json_encode($this->tabs).',';
    echo '"filters":'.json_encode($this->filters).',';
  
    $coma = '';
    echo '"table" : [';
    if($lista != false) {
      while( $rowVO = $lista->fetch() ) {

        echo $coma;
        $coma = ',';

        // dump rowVO into row
        $row = array();

        foreach($this->colsDef as $colDefKey => $colDef){
          $row[$colDefKey] = $rowVO->getter($colDefKey);
        }
        
        // modify row value if have colRules
        foreach($this->colsDef as $colDefKey => $colDef) {
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


        echo json_encode($row); 

      }
    }
    echo "]}";
  }



}