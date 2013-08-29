<?php


/**
* DAOCache Class
*
* This class encapsulates the memcached library
*
* @author: pablinhob
*/


class DAOCache {


  var $mc = false;

  function __construct() {
    $this->mc = new Memcached();
    
    $this->mc->addServer("localhost", 11211);

  }

  /*
  * @param string $query is the query string
  * @param array $variables the variables for the query prepared statment
  */
  function getCache($query){
    return $this->mc->get( $query); 
  }


  /*
  * @param string $query is the query string
  * @param array $variables the variables for the query prepared statment
  */
  function setCache($query, $data){
    return $this->mc->set( $query, $data); 
  }

}




