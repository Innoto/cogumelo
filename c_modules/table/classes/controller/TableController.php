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
  var $controllerMethodAlias = array(
      'list' => 'listItems',
      'count' => 'listCount'
      );
  var $actions = array( '0'=> array('name'=>'Actions', 'actionMethod' => '' ) );
  var $tabs = false;
  var $searchId = 'tableSearch';
  var $currentTab = false;
  var $filters = array();
  var $rowsEachPage = 50;

  /*
  * @param object $control: is the data controller  
  * @param array $data  generally is the full $_POST data variable
  */
  function __construct($control, $postdata)
  {

    $this->control = $control;

    // set orders
    $this->clientData['order'] = $postdata['order'];

    // set tabs
    if($postdata['tab']) {
      $this->currentTab = $postdata['tab'];
    }

    // set ranges
    if( $postdata['range'] != false ){
      $this->clientData['range'] = $postdata['range'];
    }
    else {
      $this->clientData['range'] = array(0, $this->rowsEachPage );
    }

    // filters
    if( $postdata['filters'] != 'false' ) {
      $this->clientData['filters'] = $postdata['filters'];
    }
    else {
      $this->clientData['filters'] = false;
    }

    // search box
    if(  $postdata['search'] != 'false') {
      $this->clientData['search'] = $postdata['search'];
    }
    else {
      $this->clientData['search'] = false;
    }
    
    $this->clientData['action'] = $postdata['action'];
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


  /*
  * @return string JSON with table
  */
  function returnTableJson() {
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
