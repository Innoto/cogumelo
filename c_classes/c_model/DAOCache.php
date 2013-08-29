<?php


/**
* DAOCache Class
*
* This class encapsulates the memcached library
*
* @author: pablinhob
*/


class DAOCache {


  static function ini() {
    
  }

  /*
  * @param string $query is the query string
  * @param array $variables the variables for the query prepared statment
  */
  function getCache($query, $variables){
    self::ini();
  }


  /*
  * @param string $query is the query string
  * @param array $variables the variables for the query prepared statment
  */
  function setCache($query, $variables){
    self::ini();
  }

}




