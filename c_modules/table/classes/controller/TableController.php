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

  var $control = false;
  var $clientData = array();
  var $colsDef = array();

  var $eachRowUrl = '';
  var $newItemUrl = '';

  var $controllerMethodAlias = array(
      'list' => 'listItems',
      'count' => 'listCount'
      );
  var $export = false;
  var $exports = array( 
      '0'=> array('name'=>'Export', 'controller'=>''),
      'csv' => array('name'=>'Csv', 'controller'=>'CsvExportTableController'),
      'xls' => array('name'=>'Excell', 'controller'=>'XlsExportTableController')
      );
  var $actions = array( '0'=> array('name'=>'Actions', 'actionMethod' => '' ) );
  var $tabs = false;
  var $searchId = 'tableSearch';
  var $currentTab = false;
  var $filters = array();
  var $rowsEachPage = 50;

  /*
  * @param object $control: is the data controller  
  * @param array $data  generally is htme full $_POST data variable
  */
  function __construct($control)
  {


    $clientdata = $_POST;
    $this->control = $control;


    if( $clientdata['exportType'] != 'false' ) {
      $this->export = $clientdata['exportType'];
    }

    // set orders
    $this->clientData['order'] = $clientdata['order'];

    // set tabs
    if( $clientdata['tab'] ) {
      $this->currentTab = $clientdata['tab'];
    }

    // set ranges
    if( $clientdata['range'] != false ){
      $this->clientData['range'] = $clientdata['range'];
    }
    else {
      $this->clientData['range'] = array(0, $this->rowsEachPage );
    }

    // filters
    if( $clientdata['filters'] != 'false' ) {
      $this->clientData['filters'] = $clientdata['filters'];
    }
    else {
      $this->clientData['filters'] = false;
    }

    // search box
    if(  $clientdata['search'] != 'false') {
      $this->clientData['search'] = $clientdata['search'];
    }
    else {
      $this->clientData['search'] = false;
    }
    
    $this->clientData['action'] = $clientdata['action'];
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
    if( array_key_exists($colId, $this->colsDef) ) {
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
  * @param int $defaultKey default key
  * @return void
  */
  function setTabs($tabsKey ,$tabs, $defaultKey) {
    if( !$this->currentTab ) {
      $this->currentTab = $defaultKey;
    }
    $this->tabs = array('tabsKey' => $tabsKey, 'tabs' => $tabs, 'defaultKey' => $defaultKey);
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
  * Get filters
  *
  * @return array
  */
  function getFilters() {
    $retFilters = array();
    
    if( $this->clientData['search'] ) {
      $retFilters[ $this->searchId ] = $this->clientData['search'];
    }

    if($this->currentTab != '*'){
      $retFilters[ $this->tabs['tabsKey'] ] = $this->currentTab;
    }

    return $retFilters; 
  }

  /*
  * set List method for controller
  * @param string $listMethod method name
  * @return void
  */
  function setListMethodAlias($listMethod) {
    $this->controllerMethodAlias['list'] = $listMethod;
  }

  /*
  * set exoport controller
  * @param string $id for selector option reference
  * @param string $name to display in selector
  * @param string $controller class name
  * @return void
  */
  function setExportController($id, $name, $controller) {
    $this->exports[$id] = array('name' => $name , 'method' => $controller);
  }

  /*
  * set Count method for controller
  * @param string $countMethod method name
  * @return void
  */
  function setCountMethodAlias($countMethod) {
    $this->controllerMethodAlias['count'] = $countMethod;
  }


  /*
  * set Search id for DAO filters
  * @param string $searchId
  * @return void
  */
  function setSearchRefId( $searchId ) {
    $this->searchId = $searchId;
  }

  /*
  * Data actions to allow in table
  *
  * @param string $actionAlias name of action for tableController
  * @param string $action name of action in controller
  * @return void
  */
  function setControllerMethodAlias( $methodAlias, $method  ) {
    $this->controllerMethodAlias[$methodAlias] = $method;
  }

  /*
  * Add Actions to the table and relate it with controller Method
  *
  * @param string $name to display it in table action selector
  * @param string $id for action
  * @param string $actionMethod method execution with variables iside
  * @return void
  */
  function setActionMethod( $name, $id, $actionMethod) {
    $this->actions[$id] = array('name' => $name, 'actionMethod' => $actionMethod);
  }


  /*
  * Get simple action list for client side
  *
  * @return array list of available actions 
  */
  function getActionsForClient() {

    $actList = array();

    foreach ($this->actions as $key => $value) {
      $actList[$key] = $value['name'];
    }

    return $actList;
  }



  /*
  * get export actions for client
  * @return array exports
  */
  function getExportsForClient() {
    
    $retExports = array();

    foreach ($this->exports as $eKey => $export) {
      $retExports[$eKey] =  $export;
    }
    return $retExports;
  }


  /*
  * Turn order objects from table in array readable by DAO
  *
  * @return array
  */
  function orderIntoArray() {
    $ordArray = false;

    if( is_array( $this->clientData['order'] ) ) {
      $ordArray = array();
      foreach(  $this->clientData['order'] as $ordObj ) {
        $ordArray[ $ordObj['key'] ] = $ordObj['value'];
      }
    }

    return $ordArray;
  }

  /*
  * Turn cols into array
  *
  * @return array
  */
  function colsIntoArray() {
    $colsArray = array();

    foreach(  $this->colsDef as $colKey => $col ) {
      $colsArray[ $colKey ] = $col['name'];
    }

    return $colsArray;
  }


  /* exec table
  * @return void
  */
  function exec() {

    if( $this->export ) {
      $this->execExport();
    }
    else {
      $this->execJsonTable();
    }
  }

  /* execExport table
  * @return void
  */
  function execExport() {
    $fileName = str_replace( ' ', '_', $this->tabs['tabs'][$this->currentTab] );
    eval('$lista = $this->control->'. $this->controllerMethodAlias['list'].'( $this->getFilters() , false, $this->orderIntoArray() );');
    new $this->exports[$this->export]['controller']( $this, $fileName, $lista );
  }


  /* setEachRowUrl url when click in row
  * @param string $url 
  * @return void
  */
  function setEachRowUrl($url) {
    $this->eachRowUrl = $url;
  }

  /* setNewItemUrl url 
  * @param string $url 
  * @return void
  */
  function setNewItemUrl($url) {
    $this->newItemUrl = $url;
  }



  /* execJsonTable table
  * @return void
  */
  function execJsonTable() {
    // if is executing a action ( like delete or update) and have permissions to do it
    if( 
      $this->clientData['action']['action'] != 'list' && 
      $this->clientData['action']['action'] != 'count'  && 
      array_key_exists( $this->clientData['action']['action'], $this->actions ) 
    ){

      // get primary key
      $refVO = new $this->control->voClass();
      $primaryKey = $refVO->getFirstPrimarykeyId();

      foreach( $this->clientData['action']['keys'] as $rowId) {
        eval( '$this->control->'.$this->actions[ $this->clientData['action']['action'] ]['actionMethod'] .';' );
      }
    }

    // doing a query to the controller
    eval('$lista = $this->control->'. $this->controllerMethodAlias['list'].'( $this->getFilters() , $this->clientData["range"], $this->orderIntoArray() );');
    eval('$totalRows = $this->control->'. $this->controllerMethodAlias['count'].'( $this->getFilters() );');


    // printing json table...

    header("Content-Type: application/json"); //return only JSON data
    
    echo "{";
    echo '"colsDef":'.json_encode($this->colsIntoArray() ).',';
    echo '"tabs":'.json_encode($this->tabs).',';
    echo '"filters":'.json_encode($this->filters).',';
    echo '"exports":'.json_encode($this->getExportsForClient()) . ',';
    echo '"actions":'.json_encode($this->getActionsForClient()) . ',';
    echo '"rowsEachPage":'. $this->rowsEachPage .',';
    echo '"totalRows":'. $totalRows.',';
    $coma = '';
    echo '"table" : [';
    if($lista != false) {
      while( $rowVO = $lista->fetch() ) {

        echo $coma;
        $coma = ',';

        // dump rowVO into row
        $row = array();
            
        $row['rowReferenceKey'] = $rowVO->getter( $rowVO->getFirstPrimarykeyId() ); 
        $rowId = $row['rowReferenceKey'];
        eval('$row["tableUrlString"] = '.$this->eachRowUrl.';');
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
