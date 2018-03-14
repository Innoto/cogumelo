<?php


/**
 * CacheRedis Class
 *
 * This class encapsulates the Redis library
 */




class CacheRedis {

  private $cacheCtrl = null;
  private $cacheSetup = false;
  private $keyPrefix = 'CGMLPHPCACHE';
  private $expirationTime = 0;


  public function __construct( $setup ) {

    $this->cacheSetup = $setup;

    if( !empty( $this->cacheSetup['host'] ) && class_exists('Redis') ) {
      $this->cacheCtrl = new Redis();

      if( empty( $this->cacheSetup['port'] ) ) {
        $this->cacheSetup['port'] = 6379; // port 6379 by default - same connection like before.
      }
      $status = $this->cacheCtrl->pconnect( $this->cacheSetup['host'], $this->cacheSetup['port'] );

      if( $status && !empty( $this->cacheSetup['database'] ) ) {
        $status = $this->cacheCtrl->select( $this->cacheSetup['database'] );  // switch to DB n
      }

      if( $status && !empty( $this->cacheSetup['auth'] ) ) {
        $status = $this->auth( $this->cacheSetup['auth'] );
      }

      if( $status ) {
        if( !empty( $this->cacheSetup['subPrefix'] ) ) {
          $this->keyPrefix .= '_'.$this->cacheSetup['subPrefix'];
        }
        elseif( $prjIdName=Cogumelo::getSetupValue('project:idName') ) {
          $this->keyPrefix .= '_'.$prjIdName;
        }
        elseif( $dbName=Cogumelo::getSetupValue('db:name') ) {
          $this->keyPrefix .= '_'.$dbName;
        }

        if( isset( $this->cacheSetup['expirationTime'] ) ) {
          $this->expirationTime = intval( $this->cacheSetup['expirationTime'] );
        }
      }
      else {
        unset( $this->cacheCtrl );
        $this->cacheCtrl = false;
      }
    }
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
      $key = $this->keyPrefix .':'. $key;

      if( empty( $expirationTime ) || !is_numeric( $expirationTime ) ) {
        $expirationTime = $this->expirationTime;
      }
      else {
        $expirationTime = intval( $expirationTime );
      }

      Cogumelo::log( __METHOD__.' - key: '.$key.' exp: '.$expirationTime, 'cache' );

      if( $expirationTime !== 0 && $this->cacheCtrl->setEx( $key, $expirationTime, serialize( $data ) ) ) {
        $result = true;
      }
    }

    return $result;
  }


  /**
   * Recupera un contenido
   *
   * @param string $key Identifies the requested data
   */
  public function getCache( $key ) {
    $result = null;

    if( $this->cacheCtrl ) {
      $key = $this->keyPrefix .':'. $key;

      $result = $this->cacheCtrl->get( $key );
      if( $result === false ) {
        $result = null;
        Cogumelo::log( __METHOD__.' - key: '.$key.' FAIL!!!', 'cache' );
      }
      else {
        $result = unserialize( $result );
        Cogumelo::log( __METHOD__.' - key: '.$key.' Atopado :)', 'cache' );
      }
    }

    return $result;
  }


  /**
   * Borra todos nuestros contenidos cache
   */
  public function flush() {
    Cogumelo::log( __METHOD__, 'cache' );
    $result = null;

    if( $this->cacheCtrl ) {
      $cacheKeys = $this->cacheCtrl->keys( $this->keyPrefix .':*' );
      Cogumelo::log( __METHOD__.' - cacheKeys: '.json_encode( $cacheKeys ), 'cache' );
      if( $this->cacheCtrl->delete( $cacheKeys ) ) {
        $result = true;
      }
    }

    return $result;
  }
}
