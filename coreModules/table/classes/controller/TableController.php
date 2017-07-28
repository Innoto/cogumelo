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

  var $isFirstTime = false;
  var $control = false;
  var $clientData = array();
  var $colsDef = array();
  var $colsDefToExport = array();
  var $colsClasses = array();
  var $sessionMaxLife = 1800;


  var $eachRowUrl = '';
  var $newItemUrl = '';

  var $controllerMethodAlias = array(
      'list' => 'listItems',
      'count' => 'listCount'
      );
  var $export = false;
  var $exports = false;
  var $actions = false;
  var $tabs = false;
  var $searchId = 'tableSearch';
  var $currentTab = null;
  var $filters = array();
  var $defaultFilters = array();
  var $extraFilters = array();
  var $rowsEachPage = 20;
  var $affectsDependences = false;
  var $joinType = 'LEFT';


  /**
  * @param object $model: is the data model
  * @param array $data  generally is htme full $_POST data variable
  */
  function __construct($model, $useSessions = false)
  {
    $this->exports = array(
        '0'=> array('name'=>__('Export'), 'controller'=>''),
        'csv' => array('name'=>'Csv', 'controller'=>'CsvExportTableController'),
        'xls' => array('name'=>'Excel', 'controller'=>'XlsExportTableController')
        );
    $this->actions = array( '0'=> array('name'=>__('Actions'), 'actionMethod' => '' ) );    

    $this->RAWClientData = $_POST;
    $this->model = $model;


    if( $this->RAWClientData['exportType'] != 'false' ) {
      $this->export = $this->RAWClientData['exportType'];
    }

    else
    if(
      $this->RAWClientData['action']['action'] === 'list' &&
      $useSessions === true &&
      isset($this->RAWClientData['firstTime'])
    ) {

      if( $this->RAWClientData['firstTime'] === 'true' ) {
        $this->isFirstTime = true;

        $sesRecovered = $this->recoverSession();
        if( $sesRecovered !== false ) {
          $this->RAWClientData = array_merge( $_POST, $sesRecovered );
          if( isset( $sesRecovered['previousPostData'] )) {
            $this->RAWClientData['previousPostData'] = $sesRecovered['previousPostData'];
          }
          //$this->sesRecovered;
          //$this->RAWClientData = $sesRecovered;
        }
        else {
          $this->saveToSession($this->RAWClientData);
        }
      }
      else {
        $this->saveToSession($this->RAWClientData);
      }
    }



    $this->clientData['order'] = $this->RAWClientData['order'];


    // set tabs
    if( $this->RAWClientData['tab'] !== "" ) {
      $this->currentTab = $this->RAWClientData['tab'];
    }

    // set ranges
    if(
      $this->RAWClientData['range'] != false &&
      is_numeric($this->RAWClientData['range'][0]) &&
      is_numeric($this->RAWClientData['range'][1])
    ){
      $this->clientData['range'] = $this->RAWClientData['range'];
    }
    else {
      $this->clientData['range'] = array(0, $this->rowsEachPage );
    }


    // filters
    if( $this->RAWClientData['filters'] != 'false' ) {
      $this->clientData['filters'] = $this->RAWClientData['filters'];
    }
    else {
      $this->clientData['filters'] = false;
    }

    // search box
    if(  $this->RAWClientData['search'] != 'false') {
      $this->clientData['search'] = $this->RAWClientData['search'];
    }
    else {
      $this->clientData['search'] = false;
    }

    $this->clientData['action'] = $this->RAWClientData['action'];
  }


  function recoverSession() {
    $ret = false;
    if(
      isset($_SESSION[ 'cogumelo_table_object' ]) &&
      isset($_SESSION['cogumelo_table_lastupdate']) &&
      ( time() - $_SESSION['cogumelo_table_lastupdate'] < $this->sessionMaxLife ) &&
      isset($_SESSION['cogumelo_table_url']) &&
      $_SESSION[ 'cogumelo_table_url'] === $_SERVER['REQUEST_URI']
    ) {
      $ret = $_SESSION[ 'cogumelo_table_object' ];
    }

    return $ret;
  }

  function saveToSession( $sessionData ) {
    $_SESSION[ 'cogumelo_table_object'] = $sessionData;
    $_SESSION[ 'cogumelo_table_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION[ 'cogumelo_table_lastupdate'] = time();
  }

  function setRowsEachPage( $rowsEachPage ) {

    //$this->RAWClientData = $_POST;

    $this->rowsEachPage = $rowsEachPage;

    // set ranges
    if( $this->RAWClientData['range'] === false || $this->RAWClientData['range']==='' ){
      $this->clientData['range'] = array(0, $rowsEachPage);
    }



  }

  /**
  * Set table col
  *
  * @param string $colId id of col in VO
  * @param string $colName. if false it gets the VO's col description.
  * @return void
  */
  function setCol($colId, $colName = false) {
    $this->colsDef[$colId] = array('name' => $colName, 'rules' => array() );
  }


    /**
    * Set table col (additionally to export)
    *
    * @param string $colId id of col in VO
    * @param string $colName. if false it gets the VO's col description.
    * @return void
    */
    function setColToExport($colId, $colName = false) {
      $this->colsDefToExport[$colId] = array('name' => $colName, 'rules' => array() );
    }



  /**
  * Unset table col
  *
  * @param string $colId id of col in VO
  * @return void
  */
  function unsetCol($colId) {
    unset($this->colsDef[$colId]);
  }

  /**
  * Set table col classes
  *
  * @param string $colId id of col in VO
  * @param mixed $colClasses string or string array
  * @return void
  */
  function setColClasses($colId, $colClasses) {
    if( !is_array( $colClasses ) ) {
      $colClasses = array( $colClasses );
    }

    $this->colsClasses[$colId] = $colClasses;
  }


  /**
  * Set col Rules
  *
  * @param string $colId id of col added with setCol method0
  * @param mixed $regexp the regular expression to match col's row value
  * @param string $finalContent is the result that we want to provide when
  *  variable $value matches (Usually a text). Can be too an operation with other cols
  * @param bool true -> $finalContent is preg_replace replace param
  * @return void
  */
  function colRule($colId, $regexp, $finalContent, $regex = false ) {
    if( array_key_exists($colId, $this->colsDef) ) {
      if ( !$regex ) {
        $this->colsDef[$colId]['rules'][] = array('regexp' => $regexp, 'finalContent' => $finalContent );
      }
      else {
        $this->colsDef[$colId]['rules'][] = array('regexp' => $regexp, 'regexContent' => $finalContent );
      }
    }
    else {
      Cogumelo::error('Col id "'.$colId.'" not found in table, can`t add col rule');
    }
  }


  /**
  * Set col export Rules
  *
  * @param string $colId id of col added with setCol method0
  * @param mixed $regexp the regular expression to match col's row value
  * @param string $finalContent is the result that we want to provide when
  *  variable $value matches (Usually a text). Can be too an operation with other cols
  * @param bool true -> $finalContent is preg_replace replace param
  * @return void
  */
  function colExportRule($colId, $regexp, $finalContent, $regex = false ) {


    if( array_key_exists($colId, $this->colsDef) ) {
      if ( !$regex ) {
        $this->colsDef[$colId]['rules'][] = array('regexp' => $regexp, 'finalContent' => $finalContent );
      }
      else {
        $this->colsDef[$colId]['rules'][] = array('regexp' => $regexp, 'regexContent' => $finalContent );
      }
    }

    if( array_key_exists($colId, $this->colsDefToExport) ) {
      if ( !$regex ) {
        $this->colsDefToExport[$colId]['rules'][] = array('regexp' => $regexp, 'finalContent' => $finalContent );
      }
      else {
        $this->colsDefToExport[$colId]['rules'][] = array('regexp' => $regexp, 'regexContent' => $finalContent );
      }
    }

  }


  /**
  * Set tabs
  *
  * @param string $tabsKey
  * @param array $tabs
  * @param int $defaultKey default key
  * @return void
  */
  function setTabs($tabsKey ,$tabs, $defaultKey) {
    if( $this->currentTab === null) {
      $this->currentTab = $defaultKey;
    }
    $this->tabs = array('tabsKey' => $tabsKey, 'tabs' => $tabs, 'defaultKey' => $this->currentTab);
  }


  /**
  * Set default filters array
  *
  * @param array $defaultFilters
  * @return void
  */
  function setDefaultFilters( $defaultFilters ) {
    $this->defaultFilters = $defaultFilters;
  }

  /**
  * Set extra filters array
  *
  * @param array $extratFilter
  * @return void
  */
  function setExtraFilter( $key,  $type, $title, $options, $defaultValue ) {

    $this->extraFilters[$key] = array( 'type' => $type, 'title' => $title, 'options' => $options, 'default' => $defaultValue );
  }



  /**
  * Set affectsDependences array
  *
  * @param array $defaultFilters
  * @return void
  */
  function setaffectsDependences( $affectsDependences ) {
    $this->affectsDependences = $affectsDependences;
  }


  /**
  * Set setJoinType array
  *
  * @param string $joinType
  * @return void
  */
  function setJoinType( $joinType ) {
    $this->joinType = $joinType;
  }


  /**
  * Get filters
  *
  * @return array
  */
  function getFilters() {
    $retFilters = array();

    if( $this->clientData['filters'] ) {
      foreach( $this->clientData['filters'] as $filterKey => $filterValue ) {
        if( isset($this->extraFilters[$filterKey]) ){
          if( $filterValue == '*'){
            unset( $retFilters[$filterKey] );
          }
          else {
            $retFilters[$filterKey] = $filterValue;
          }
        }

      }
      //$retFilters[ $this->searchId ]
    }

    if( $this->clientData['search'] ) {
      $retFilters[ $this->searchId ] = $this->clientData['search'];
    }

    if($this->currentTab != '*'){
      $retFilters[ $this->tabs['tabsKey'] ] = $this->currentTab;
    }

    $retFilters = array_merge( $retFilters, $this->defaultFilters );

    return $retFilters;
  }

  /**
  * set List method for controller
  * @param string $listMethod method name
  * @return void
  */
  function setListMethodAlias($listMethod) {
    $this->controllerMethodAlias['list'] = $listMethod;
  }


  /**
  * set exoport controller
  * @param string $id for selector option reference
  * @param string $name to display in selector
  * @param string $controller class name
  * @return void
  */
  function setExportController($id, $name, $controller) {
    $this->exports[$id] = array('name' => $name , 'method' => $controller);
  }

  /**
  * set Count method for controller
  * @param string $countMethod method name
  * @return void
  */
  function setCountMethodAlias($countMethod) {
    $this->controllerMethodAlias['count'] = $countMethod;
  }


  /**
  * set Search id for DAO filters
  * @param string $searchId
  * @return void
  */
  function setSearchRefId( $searchId ) {
    $this->searchId = $searchId;
  }

  /**
  * Data actions to allow in table
  *
  * @param string $actionAlias name of action for tableController
  * @param string $action name of action in controller
  * @return void
  */
  function setControllerMethodAlias( $methodAlias, $method  ) {
    $this->controllerMethodAlias[$methodAlias] = $method;
  }

  /**
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

  /**
  * Add Actions to the table and relate it with controller Method
  *
  * @param string $name to display it in table action selector
  * @param string $id for action
  * @param string $actionMethod method execution with variables iside
  * @return void
  */
  function setActionSeparator() {
    $this->actions['SEPARATOR_'.sizeof($this->actions)] = false;
  }

  /**
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



  /**
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


  /**
  * Turn order objects from table in array readable by DAO
  *
  * @return array
  */
  function orderIntoArray() {
    $ordArray = false;

    if( is_array( $this->clientData['order'] ) ) {
      $ordArray = array();
      foreach(  $this->clientData['order'] as $ordObj ) {
        if(!preg_match('#^(.*)\.(.*)$#', $ordObj['key'], $m ) ) {


          $modelClass = get_class( $this->model );
          $modelCols = $modelClass::$cols;
          if( isset($modelCols[$ordObj['key']]['multilang']) && $modelCols[$ordObj['key']]['multilang'] == true ) {
            global $C_LANG;
            $ordKey = $ordObj['key'].'_'.$C_LANG;
          }
          else {
            $ordKey = $ordObj['key'];
          }



          $ordArray[ $ordKey ] = $ordObj['value'];
        }
      }
    }
    //var_dump($ordArray);
    //exit;
    return $ordArray;
  }

  /**
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

  /**
  * Turn colsToExport into array
  *
  * @return array
  */
  function colsToExportIntoArray() {
    $colsArray = array();

    foreach(  $this->colsDefToExport as $colKey => $col ) {
      $colsArray[ $colKey ] = $col['name'];
    }

    return $colsArray;
  }



  /**
  * exec table
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

  /**
  * execExport table
  * @return void
  */
  function execExport() {
    $fileName = str_replace( ' ', '_', $this->tabs['tabs'][$this->currentTab] );
    $p = array(
        'filters' =>  $this->getFilters(),
        'order' => $this->orderIntoArray(),
        'affectsDependences' => $this->affectsDependences , //array('ResourceTopicModel'),
        'joinType' => $this->joinType
    );

    eval('$lista = $this->model->'. $this->controllerMethodAlias['list'].'( $p );');
    new $this->exports[$this->export]['controller']( $this, $fileName, $lista );
  }


  /**
  * setEachRowUrl url when click in row
  * @param string $url
  * @return void
  */
  function setEachRowUrl($url) {
    $this->eachRowUrl = $url;
  }

  /**
  * setNewItemUrl url
  * @param string $url
  * @return void
  */
  function setNewItemUrl($url) {
    $this->newItemUrl = $url;
  }



  /**
  * execJsonTable table
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
      eval( '$refVO = new '.$this->model->getVOClassName().'();');
      $primaryKey = $refVO->getFirstPrimarykeyId();

      foreach( $this->clientData['action']['keys'] as $rowId) {
        eval( '$this->model->'.$this->actions[ $this->clientData['action']['action'] ]['actionMethod'] .';' );
      }
    }

    // doing a query to the controller
    $p = array(
        'filters' =>  $this->getFilters(),
        'range' => $this->clientData['range'],
        'order' => $this->orderIntoArray(),
        'affectsDependences' => $this->affectsDependences , //array('ResourceTopicModel'),
        'joinType' => $this->joinType
    );

    //var_dump($p);

    //Cogumelo::console($this->getFilters() );

    eval('$lista = $this->model->'. $this->controllerMethodAlias['list'].'( $p );');
    eval('$totalRows = $this->model->'. $this->controllerMethodAlias['count'].'( $p );');


    // printing json table...

    header("Content-Type: application/json"); //return only JSON data

    echo '{';
    echo '"newItemUrl": "'.$this->newItemUrl.'",';
    echo '"colsDef":'.json_encode($this->colsIntoArray() ).',';
    echo '"colsClasses":'.json_encode($this->colsClasses ).',';
    echo '"tabs":'.json_encode($this->tabs).',';
    echo '"search":'. json_encode($this->clientData['search']).',';
    echo '"filters":'.json_encode($this->filters).',';
    echo '"extraFilters":'.json_encode($this->extraFilters).',';
    echo '"exports":'.json_encode($this->getExportsForClient()) . ',';
    echo '"actions":'.json_encode($this->getActionsForClient()) . ',';
    echo '"rowsEachPage":'. $this->rowsEachPage .',';
    echo '"totalRows":'. $totalRows.',';
    echo '"previousPostData":'. json_encode($this->RAWClientData) .',';
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
        foreach($this->colsDef as $colDefKey => $colDef) {
          // if have rules and matches with regexp
          if($colDef['rules'] != array() ) {

            foreach($colDef['rules'] as $rule){
              if( !isset( $rule['regexContent'] ) ) {
                if(preg_match( $rule['regexp'], $row[$colDefKey])) {
                  eval('$row[$colDefKey] = "'.$rule['finalContent'].'";');
                  break;
                }
              }
              else {
                if( $row[$colDefKey] = preg_replace( $rule['regexp'], $rule['regexContent'], $row[$colDefKey] ) ) {
                  break;
                }
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
