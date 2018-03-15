<?php


/**
 * Cache Class
 *
 * This class encapsulates the memcached library
 */


class Cache {

  private $cacheCtrl = null;


  public function __construct() {
    $cacheSetup = Cogumelo::getSetupValue('cogumelo:cache');

    if( !$this->cacheCtrl && !empty( $cacheSetup['redis'] ) ) {
      Cogumelo::load('coreController/CacheRedis.php');
      $this->cacheCtrl = new CacheRedis( $cacheSetup['redis'] );
    }

    if( !$this->cacheCtrl && !empty( $cacheSetup['memcached'] ) ) {
      Cogumelo::load('coreController/CacheMemcached.php');
      $this->cacheCtrl = new CacheMemcached( $cacheSetup['memcached'] );
    }

    if( !$this->cacheCtrl ) {
      $memcachedSetup = Cogumelo::getSetupValue('memcached');
      if( !empty( $memcachedSetup ) ) {
        Cogumelo::load('coreController/CacheMemcached.php');
        $this->cacheCtrl = new CacheMemcached( $memcachedSetup );
      }
    }
  }

  /**
   * Recupera un contenido
   *
   * @param string $key Identifies the requested data
   */
  public function getCache( $key ) {
    $result = null;

    if( $this->cacheCtrl ) {
      $result = $this->cacheCtrl->getCache( $key );
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
  public function setCache( $key, $data, $expirationTime = false ) {
    $result = null;

    if( $this->cacheCtrl ) {
      $result = $this->cacheCtrl->setCache( $key, $data, $expirationTime );
    }

    return $result;
  }


  /**
   * Borra todos nuestros contenidos cache
   */
  public function flush() {
    $result = null;

    if( $this->cacheCtrl ) {
      $result = $this->cacheCtrl->flush();
    }

    return $result;
  }
}
