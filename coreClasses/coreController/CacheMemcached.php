<?php


/**
 * CacheMemcached Class
 *
 * This class encapsulates the Memcached library
 *
 * @author: pablinhob
 */


class CacheMemcached {

  private $cacheCtrl = null;
  private $cacheSetup = false;
  private $keyPrefix = 'CGMLPHPCACHE';
  private $expirationTime = 0;


  public function __construct( $setup ) {

    $this->cacheSetup = $setup;

    if( !empty( $this->cacheSetup['hostArray'] ) && class_exists('Memcached') ) {
      $this->cacheCtrl = new Memcached();

      
      $status = $this->cacheCtrl->addServers( $this->cacheSetup['hostArray'] );
      // $status = false;
      // foreach( $this->cacheSetup['hostArray'] as $host ) {
      //   $status = $status || $this->cacheCtrl->addServer( $host['host'], $host['port'] );
      // }







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

      if( $expirationTime !== 0 && $this->cacheCtrl->set( $key, $data, $expirationTime ) ) {
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
      if( $this->cacheCtrl->getResultCode() !== Memcached::RES_SUCCESS ) {
        $result = null;
        Cogumelo::log( __METHOD__.' - key: '.$key.' FAIL!!!', 'cache' );
      }
      else {
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

      $allKeys = $this->cacheCtrl->getAllKeys();
      if( $this->cacheCtrl->getResultCode() === Memcached::RES_SUCCESS ) {
        $cacheKeys = !empty( $allKeys ) ? array_filter( $allKeys, $this->isCacheKey ) : false;
        if( !empty( $cacheKeys ) ) {
          // Cogumelo::log( __METHOD__.' - cacheKeys: '.json_encode( $cacheKeys ), 'cache' );
          $this->cacheCtrl->deleteMulti( $cacheKeys );
        }
      }
      else {
        // Si no es posible el borrado parcial se realiza un borrado total
        $this->cacheCtrl->flush();
      }

      if( $this->cacheCtrl->getResultCode() === Memcached::RES_SUCCESS ) {
        $result = true;
      }
    }

    return $result;
  }

  private function isCacheKey( $keyName ) {
    return( strpos( $keyName, $this->keyPrefix .':') === 0 );
  }
}
