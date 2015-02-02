<?php

Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Facade.php');

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
  * @return array VO array
  */
  function listCount( array $filters= array() )
  {

    $p = array(
        'filters' => false, 
        'affectsDependences' => false, 
        'cache' => false
      );
    $parameters =  array_merge($p, $parameters );

    Cogumelo::debug( 'Called listCount on '.get_called_class() );
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
  function create(  array $filters= array() )
  {

    $p = array(
        'affectsDependences' => false
      );
    $parameters =  array_merge($p, $parameters );

    Cogumelo::debug( 'Called create on '.get_called_class() );
    return $this->dataFacade->Create($this);
  }

  /**
  * save item
  *
  * @return object  VO
  */
  function save(  array $parameters= array() )
  {

    $p = array(
        'affectsDependences' => false
      );
    $parameters =  array_merge($p, $parameters );

    Cogumelo::debug( 'Called update on '.get_called_class() );
    return $this->dataFacade->Update($this);
  }

  function delete( array $parameters = array() ) {

    $p = array(
        'affectsDependences' => false
      );
    $parameters =  array_merge($p, $parameters );




    // delete all dependences ?
    if($parameters['affectsDependences']) {
      $depsInOrder = $this->getDepInLinearArray();

      while( $selectDep = array_pop($depsInOrder) ) {
          $selectDep->delete();
      }

    }


    Cogumelo::debug( 'Called delete on '.get_called_class().' with "'.$this->getFirstPrimarykeyId().'" = '. $this->getter( $this->getFirstPrimarykeyId() ) );

    $data = $this->dataFacade->deleteFromKey( $this->getFirstPrimarykeyId(), $this->getter( $this->getFirstPrimarykeyId() )  );

    return $data;
  }


}