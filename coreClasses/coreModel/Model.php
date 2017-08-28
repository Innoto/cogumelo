<?php

Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Facade.php');


/**
 * Merge of VO and Data controller concepts
 *
 * @package Cogumelo Model
 */
class Model extends VO {

  var $dataFacade;


  var $customFacade = false;
  var $customDAO = false;
  var $moduleDAO = false;

  var $notCreateDBTable = false;
  var $rcSQL = '';
  var $deploySQL = array();

  public function __construct( $datarray = array(), $otherRelObj = false ) {


    $this->setData( $datarray, $otherRelObj );

    if($this->customFacade) {
      $this->dataFacade = new $customFacade();
    }
    if( $this->customDAO ) {
      $this->dataFacade = new Facade( false,  $this->customDAO, $this->moduleDAO);
    }
    else {
      $this->dataFacade = new Facade( $this );
    }

  }



  /**
  * List items from table
  *
  * @param array $parameters array of filters
  *
  * @return DAOResult
  */
  public function listItems( array $parameters = array() ) {
    $p = array(
      'filters' => false,
      'range' => false,
      'order' => false,
      'fields' => false,
      'joinType' => 'LEFT',
      'affectsDependences' => false,
      'groupBy' => false,
      'cache' => false
    );
    $parameters =  array_merge($p, $parameters );


    Cogumelo::debug( 'Called listItems on '.get_called_class() );
    $data = $this->dataFacade->listItems(
      $parameters['filters'],
      $parameters['range'],
      $parameters['order'],
      $parameters['fields'],
      $parameters['joinType'],
      $parameters['affectsDependences'],
      $parameters['groupBy'],
      $parameters['cache']
    );

    return $data;
  }


  /**
  * Count items from table
  *
  * @param array $parameters array of filters
  *
  * @return array VO array
  */
  public function listCount( array $parameters = array() ) {

    $p = array(
      'filters' => false,
      'joinType' => 'left',
      'affectsDependences' => false,
      'cache' => false
    );
    $parameters =  array_merge($p, $parameters );

    Cogumelo::debug( 'Called listCount on '.get_called_class() );
    $data = $this->dataFacade->listCount( $parameters['filters'] );

    return $data;
  }

  public static function getFilters() {

    $extraFilters = array();
    $filterCols = array();

    eval('$tableName = '.get_called_class().'::$tableName;');
    eval('$cols = '.get_called_class().'::$cols;');
    eval('if( isset( '.get_called_class().'::$extraFilters) ) {$extraFilters = '.get_called_class().'::$extraFilters;}');


    foreach( $cols as $colK => $colD ) {
      $type = $colD['type'];

      if( $type == 'CHAR' || $type == 'VARCHAR' || $type == 'INT' || $type == 'BOOLEAN'  || $type == 'FOREIGN' ){
        $filterCols[ $colK ] = $tableName.".".$colK." = ? ";
      }

    }

    return array_merge( $filterCols, $extraFilters );
  }




  /**
  * Save item
  *
  * @param array $parameters array of filters
  *
  * @return object  VO
  */
  public function save( array $parameters = array() ) {

    // TODO: Si hay dependencias no hace return del objeto

    $p = array(
      'affectsDependences' => false
    );
    $parameters = array_merge($p, $parameters );


    // Save all dependences
    if($parameters['affectsDependences']) {
      $depsInOrder2 = $depsInOrder = $this->getDepInLinearArray();

      // save first time to create keys
      while( $selectDep = array_pop($depsInOrder) ) {
        $selectDep['ref']->save( array('affectsDependences' => false) );
      }


      // Update external keys of all VOs
      $this->refreshRelationshipKeyIds();

      // save second time to update keys in related VOs
      while( $selectDep = array_pop($depsInOrder2) ) {
        $selectDep['ref']->save( array('affectsDependences' => false) );
      }

    }
    // Save only this Model
    else {
      Cogumelo::debug( 'Called save on '.get_called_class(). ' with "'.$this->getFirstPrimarykeyId().'" = '. $this->getter( $this->getFirstPrimarykeyId() ) );
      $m = $this->saveOrUpdate();

      $filter = array( 'id' => $this->getter( $this->getFirstPrimarykeyId() ) );


      if( $els = $this->listItems( array('filters'=>$filter) )  ){
        if( $el = $els->fetch() ) {
          $this->data = $el->data;
          $m = $el;
        }
      }

      return $m;

    }


  }

  /**
  * Save item
  *
  * @param object $voObj voObject
  *
  * @return object  VO
  */
  private function saveOrUpdate( $voObj = false ) {
    $retObj = false;

    if(!$voObj) {
      $voObj = $this;
    }


    if( $voObj->data == array() ) {
      $retObj = $this;
    }
    else
    if( $voObj->exist() ) {
      $retObj = $this->dataFacade->Update( $voObj );
    }
    else {
      $retObj = $this->dataFacade->Create( $voObj );
    }

    return $retObj;
  }

  /**
  * Verify if VO exist in DDBB
  *
  * @param object $voObj voObject
  *
  * @return boolean
  */
  public function exist( $voObj = false ) {
    $ret = false;

    if(!$voObj) {
      $voObj = $this;
    }

    $pkId = $this->getFirstPrimarykeyId();

    if( $voObj->getter($pkId) && $filters = $voObj->data) {

      if( $this->listCount( array('filters'=>array( $pkId=>$filters[ $pkId ] ) )) ) {
        $ret = true;
      }
    }

    return $ret;
  }


  /**
  * Delete item
  *
  * @param array $parameters array of filters
  *
  * @return boolean
  */
  public function delete( array $parameters = array() ) {

    $p = array(
      'affectsDependences' => false
    );
    $parameters = array_merge($p, $parameters );


    // Delete all dependences
    if($parameters['affectsDependences']) {
      $depsInOrder = $this->getDepInLinearArray();

      while( $selectDep = array_pop($depsInOrder) ) {
          Cogumelo::debug( 'Called delete on '.get_called_class().' with "'.$selectDep['ref']->getFirstPrimarykeyId().'" = '. $selectDep['ref']->getter( $selectDep['ref']->getFirstPrimarykeyId() ) );
          $selectDep['ref']->dataFacade->deleteFromKey( $selectDep['ref']->getFirstPrimarykeyId(), $selectDep['ref']->getter( $selectDep['ref']->getFirstPrimarykeyId() )  );
      }
    }
    // Delete only this Model
    else {
      Cogumelo::debug( 'Called delete on '.get_called_class().' with "'.$this->getFirstPrimarykeyId().'" = '. $this->getter( $this->getFirstPrimarykeyId() ) );
      $this->dataFacade->deleteFromKey( $this->getFirstPrimarykeyId(), $this->getter( $this->getFirstPrimarykeyId() )  );
    }


    return true;
  }



  /**
  * Update item key
  *
  * @param array $parameters array of filters
  *
  * @return object  VO
  */
  public function updateKey( array $parameters = array() ) {


    $p = array(
      'searchKey' => null,
      'changeKey' => null
    );

    $dataVO = false;
    if($parameters['searchKey'] !== null ) {
      $dataVO = $this->listItems( array( 'filters' => array( $parameters['searchKey'] => $parameters['searchValue'] ) ))->fetch();
      if($dataVO && $parameters['changeKey'] !== null && $parameters['changeValue'] !== null){
        $dataVO->setter( $parameters['changeKey'], $parameters['changeValue'] );

        $dataVO->save();
      }
    }

    return $dataVO;
  }


  public function getAllData( $style = 'raw' ) {
    $retData = false;

    switch ( $style ) {
      case 'raw':
        $retData = $this->getAllRawData();
        break;

      case 'onlydata':
        $retData = $this->getAllOnlyData();
        break;

    }

    return $retData;
  }


  /**
   * Start transaction
   *
   * @return void
   */
  public function transactionStart() {
    $this->dataFacade->transactionStart();
  }

  /**
   * Commit transaction
   *
   * @return void
   */
  public function transactionCommit() {
    $this->dataFacade->transactionCommit();
  }

  /**
   * Rollback transaction
   *
   * @return void
   */
  public function transactionRollback() {
    $this->dataFacade->transactionRollback();
  }



}
