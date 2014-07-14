<?php

Cogumelo::load('c_model/DAOResult');


class MysqlDAOResult extends DAOResult {

  var $result;
  var $VO;
  var $cache = false;


  function __construct($voObj, $result, $isCachedResult = false) {
    $this->VO = $voObj;
    $this->result = $result;
    if( $isCachedResult ) {
      $this->cache = $result;
    }
  }


  // fetch just one result
  function fetch() {

    if($this->cache){ // is cached query ?
      $ret_obj = $this->cacheFetch();
    }
    else {
     

        //Cogumelo::objDebug($this->result);
      if( 
        is_object( $this->result ) && 
        $row = $this->result->fetch_assoc() 
      ) {
        $ret_obj = $this->VOGenerator( $row );
      }
      else {
        $ret_obj = null;
      }
    }

    return $ret_obj;
  }


  // resturn an VO array with all the result rows
  function fetchAll() {

    if($this->cache){ // is cached query ?
        $list = $this->cacheFetchAll();
    }
    else {

        $list = array();

        while( $row = $this->result->fetch_assoc() ) {
          $rowVO = $this->VOGenerator( $row);
          $list[ $rowVO->getter($rowVO->getFirstPrimarykeyId()) ] = $rowVO;
        }
    }

    $this->reset_fetch();
    
    return $list;
    
  }

  // count total numer of query result
  function count() {

    if($this->cache){ // is cached query ?
      $ret = $this->cache_count();
    }
    else {
      $ret = $this->result->count();
    }

    return $ret;
  }


  //
  // Creates an VO from a query result;
  // Returns: Obj/false
  function VOGenerator($row) // antes utilizaba & na variable res
  {
    //Cogumelo::objDebug($row);
    return new $this->VO($row);

  }

  function fetchAllRaw() {

        //$this->reset_fetch();
    $list = array();

    while( $row = $this->result->fetch_assoc() ) {
      $list[] = $row;
    }
    
    $this->reset_fetch();

    return $list; 
  }

  function resetFetch() {
     //return mysql_data_seek($this->result ,0);
     $this->result->data_seek(0);

  }


}