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
    
    global $MEMCACHED_HOST_ARRAY;
    foreach( $MEMCACHED_HOST_ARRAY as $host) {
      $this->mc->addServer($host['host'], $host['port']);
    }

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
    return $this->mc->set( $query, $data, MEMCACHED_EXPIRATION_TIME); 
  }

}




