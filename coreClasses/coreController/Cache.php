<?php


/**
* Cache Class
*
* This class encapsulates the memcached library
*
* @author: pablinhob
*/


class Cache {


  var $mc = false;

  public function __construct() {
    $this->mc = new Memcached();

    $hostArray = Cogumelo::getSetupValue( 'memcached:hostArray' );
    if( $hostArray ) {
      foreach( $hostArray as $host ) {
        $this->mc->addServer( $host['host'], $host['port'] );
      }
    }
  }

  /**
   * @param string $query is the query string
   * @param array $variables the variables for the query prepared statment
   */
  public function getCache( $query ) {
    return $this->mc->get( $query);
  }


  /**
   * @param string $query is the query string
   * @param array $variables the variables for the query prepared statment
   */
  public function setCache( $query, $data ){
    return $this->mc->set( $query, $data, Cogumelo::getSetupValue( 'memcached:expirationTime' ) );
  }

}




