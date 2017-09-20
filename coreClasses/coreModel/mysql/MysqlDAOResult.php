<?php

Cogumelo::load('coreModel/DAOResult.php');


/**
 * DAO Result Mysql
 *
 * @package Cogumelo Model
 */
class MysqlDAOResult extends DAOResult {

  var $result;
  var $VO;
  var $cache = false;

  /**
   * Sets data of VO
   *
   * @param object $voObj
   * @param object $result
   * @param boolean $isCacheResult
   *
   * @return object
   */
  function __construct($voObj, $result, $isCachedResult = false) {
    $this->VO = $voObj;
    $this->result = $result;
    if( $isCachedResult ) {
      $this->cache = $result;
    }
  }



  /**
   * fetch just one result
   *
   * @return object
   */
  function fetch() {

    if($this->cache){ // is cached query ?
      $ret_obj = $this->cacheFetch();
    }
    else {
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


  /**
   * array of VOs
   *
   * @return array
   */
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

    if( is_object( $this->result ) ) {
      $this->resetFetch();
    }

    return $list;

  }

  /**
   * count result
   *
   * @return integer
   */
  function count() {

    if($this->cache){ // is cached query ?
      $ret = $this->cache_count();
    }
    else {
      $ret = $this->result->count();
    }

    return $ret;
  }


  /**
   * Creates an VO os Model from a query result
   *
   * @return object
   */
  function VOGenerator($row) // antes utilizaba & na variable res
  {

    // exclude null values
    foreach ($row as $k => $v ) {
      if( $v === null ) {
        unset( $row[$k] );
      }
    }

    return new $this->VO($row);
  }


  /**
   * Query result without VO or Model declaration
   *
   * @return array
   */
  function fetchAllRaw() {

        //$this->reset_fetch();
    $list = array();

    while( $row = $this->result->fetch_assoc() ) {
      $list[] = $row;
    }
    if( is_object( $this->result ) ) {
      $this->resetFetch();
    }


    return $list;
  }


  /**
   * Reset fetch
   *
   * @return void
   */
  function resetFetch() {
     //return mysql_data_seek($this->result ,0);
     $this->result->data_seek(0);

  }


}
