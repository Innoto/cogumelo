<?php

Cogumelo::load('coreModel/VO.php');
Cogumelo::load("coreModel/Facade.php");

Class Model extends VO {

  var $dataFacade;


  var $customFacade = false;
  var $customDAO = false;
  var $moduleDAO = false;
  var $filters = array();


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
  * @param array $filters array of filters
  * @param array $range two element array with result range ex. array(0,100)
  * @param array $order order for query
  * @param array fields
  * @param boolean $resolveDependences
  * @param boolean $cache true means cache is enabled
  * 
  * @return array VO array
  */
  function listItems($filters = false, $range = false, $order = false, $fields = false, $resolveDependences = false, $cache = false)
  {
    Cogumelo::debug( "Called listItems on ".get_called_class() );
    $data = $this->dataFacade->listItems($filters, $range, $order, $fields, $resolveDependences, $cache);

    return $data;
  }


  /**
  * Count items from table
  *
  * @return array VO array
  */
  function listCount($filters = false)
  {
    Cogumelo::debug( "Called listCount on ".get_called_class() );
    $data = $this->dataFacade->listCount($filters);

    return $data;
  }

  function getFilters(){
    return $this->filters;
  }


  /**
  * create item
  *
  * @return object VO 
  */
  function create()
  {
    Cogumelo::debug( "Called create on ".get_called_class() );
    return $this->dataFacade->Create($data);
  }

  /**
  * save item
  *
  * @return object  VO
  */

  function save()
  {
    Cogumelo::debug( "Called update on ".get_called_class() );
    return $this->dataFacade->Update($data);
  }


}