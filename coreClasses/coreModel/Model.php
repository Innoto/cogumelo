<?php

Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Facade.php');


/**
 * Merge of VO and Data controller concepts
 *
 * @package Cogumelo Model
 */
Class Model extends VO {

  var $dataFacade;


  var $customFacade = false;
  var $customDAO = false;
  var $moduleDAO = false;


  function __construct( $datarray= array(), $otherRelObj = false ) {
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
  * @return array VO array
  */
  function listItems( array $parameters = array() )
  {

    $p = array(
        'filters' => false,
        'range' => false,
        'order' => false,
        'fields' => false,
        'affectsDependences' => false,
        'cache' => false
      );
    $parameters =  array_merge($p, $parameters );


//var_dump($parameters);
    Cogumelo::debug( 'Called listItems on '.get_called_class() );
    $data = $this->dataFacade->listItems(
                                          $parameters['filters'],
                                          $parameters['range'],
                                          $parameters['order'],
                                          $parameters['fields'],
                                          $parameters['affectsDependences'],
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
  function listCount( array $parameters= array() )
  {

    $p = array(
        'filters' => false,
        'cache' => false
      );
    $parameters =  array_merge($p, $parameters );
//var_dump($parameters);
    Cogumelo::debug( 'Called listCount on '.get_called_class() );
    $data = $this->dataFacade->listCount( $parameters['filters']);

    return $data;
  }

  static function getFilters(){

    $extraFilters = array();
    $filterCols = array();

    eval('$tableName = '.get_called_class().'::$tableName;');
    eval('$cols = '.get_called_class().'::$cols;');
    eval('if( isset( '.get_called_class().'::$extraFilters) ) {$extraFilters = '.get_called_class().'::$extraFilters;}');    


    foreach( $cols as $colK => $colD  ) {
      $type = $colD['type'];

      if( $type == 'CHAR' || $type == 'VARCHAR' || $type == 'INT'){
          $filterCols[ $colK ] = $tableName.".".$colK." = ? ";
      }

    }

    return array_merge( $filterCols, $extraFilters );
  }




  /**
  * save item
  *
  * @param array $parameters array of filters
  *
  * @return object  VO
  */
  function save( array $parameters= array() )
  {
    $this->dataFacade->transactionStart();

    $p = array(
        'affectsDependences' => false
      );
    $parameters =  array_merge($p, $parameters );


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
      return $this->saveOrUpdate();
    }

    $this->dataFacade->transactionEnd();

  }

  /**
  * save item
  *
  * @param object $voObj voObject
  *
  * @return object  VO
  */
  private function saveOrUpdate( $voObj = false ){
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
  * if VO exist in DDBB
  *
  * @param object $voObj voObject
  *
  * @return boolean
  */
  function exist($voObj = false) {
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
  * delete item
  *
  * @param array $parameters array of filters
  *
  * @return boolean
  */
  function delete( array $parameters = array() ) {

    $this->dataFacade->transactionStart();

    $p = array(
        'affectsDependences' => false
      );
    $parameters =  array_merge($p, $parameters );


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

    $this->dataFacade->transactionEnd();

    return true;
  }



  /**
  * delete item
  *
  * @param array $parameters array of filters
  *
  * @return object  VO
  */
  function updateKey( array $parameters = array() ) {

    $this->dataFacade->transactionStart();

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

    $this->dataFacade->transactionEnd();

    return $dataVO;
  }


}


