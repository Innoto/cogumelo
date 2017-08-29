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
   * Recupera un contenido
   *
   * @param string $key Identifies the requested data
   */
  public function getCache( $key ) {
    // error_log( __METHOD__.' - key: '.$key );

    $result = $this->mc->get( $key );
    if( $this->mc->getResultCode() !== Memcached::RES_SUCCESS ) {
      $result = null;
      Cogumelo::log( __METHOD__.' - key: '.$key.' FAIL!!!', 'cache' );
    }
    else {
      Cogumelo::log( __METHOD__.' - key: '.$key.' Atopado :)', 'cache' );
    }

    return $result;
  }


  /**
   * Recupera un contenido
   *
   * @param string $key Identifies the data to be saved
   * @param mixed $data Content to save
   * @param mixed $expirationTime Expiration time. (default or fail: use setup value)
   */
  public function setCache( $key, $data, $expirationTime = false ){

    if( empty( $expirationTime ) || !is_numeric( $expirationTime ) ) {
      $expirationTime = Cogumelo::getSetupValue( 'memcached:expirationTime' );
    }
    else {
      $expirationTime = intval( $expirationTime );
    }

    Cogumelo::log( __METHOD__.' - key: '.$key.' exp: '.$expirationTime, 'cache' );

    return $this->mc->set( $key, $data, $expirationTime );
  }


  /**
   * Borra todos los contenidos
   */
  public function flush() {
    Cogumelo::log( __METHOD__, 'cache' );
    return $this->mc->flush();
  }
}
