<?php


/**
 * Gestión de formularios. Campos, Validaciones, Html, Ficheros, ...
 *
 * @package Module CogumeloSession
 */
class CogumeloSessionController {

  private $tokenSessionName = 'CGMLTOKENSESSID';
  private $tokenSessionID = false;


  /**
   * Constructor. Crea el TokenSessionID o lo carga del entorno y lo asigna a $C_SESSION_ID.
   */
  public function __construct() {
    // error_log( __METHOD__ );

    // $sessionSavePath = session_save_path();
    // if( !is_dir ( $sessionSavePath ) ) {
    //   mkdir( $sessionSavePath, 0700, true );
    // }

    global $C_SESSION_ID;
    if( isset( $C_SESSION_ID ) ) {
      $this->tokenSessionID = $C_SESSION_ID;
    }
  }



  public function prepareTokenSessionEnvironment() {
    // error_log( __METHOD__ );

    $tkSID = false;
    $remoteAddr = false;

    $tkName = $this->getTokenSessionName();

    // error_log( '...' );
    // error_log( '(Notice) prepareTokenSessionEnvironment INI' );
    // error_log( '$_COOKIE = '.json_encode($_COOKIE) );

    // No se ejecuta en php modo cliente
    if( php_sapi_name() !== 'cli' ) {

      session_name( $tkName );


      if( isset( $_COOKIE[ $tkName ] ) ) {
        $tkSID = $_COOKIE[ $tkName ];
      }
      else {
        if( isset( $_POST[ $tkName ] ) && trim( $_POST[ $tkName ] ) !== '' ) {
          $tkSID = $_POST[ $tkName ];
        }
        elseif( isset( $_SERVER[ 'HTTP_X_'.$tkName ] ) && trim( $_SERVER[ 'HTTP_X_'.$tkName ] ) !== '' ) {
          $tkSID = $_SERVER[ 'HTTP_X_'.$tkName ];
        }
      }


      if( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
        $remoteAddr = $_SERVER['HTTP_X_REAL_IP'];
      }
      elseif( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $remoteAddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }
      else {
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
      }


      if( $tkSID ) {
        session_id( $tkSID );
        session_start();
        if( isset( $_SESSION[ 'cogumeloSessionNew' ] ) ) {
          $this->tokenSessionID = session_id();
          $_SESSION[ 'cogumeloSessionNew' ] = false;
          $_SESSION[ 'cogumeloSessionTimePrev' ] = ( $_SESSION[ 'cogumeloSessionTimeLast' ] ) ?
            $_SESSION[ 'cogumeloSessionTimeLast' ] : $_SESSION[ 'cogumeloSessionTimeCreate' ];
          $_SESSION[ 'cogumeloSessionTimeLast' ] = time();
          if( $_SESSION[ 'cogumeloSessionRemoteAddr' ] !== $remoteAddr ) {
            $_SESSION[ 'cogumeloSessionRemoteAddrPrev' ] = $_SESSION[ 'cogumeloSessionRemoteAddr' ];
            $_SESSION[ 'cogumeloSessionRemoteAddrChange' ] = true;
            $_SESSION[ 'cogumeloSessionRemoteAddr' ] = $remoteAddr;
          }
        }
        else {
          Cogumelo::debug( 'CogumeloSessionController: ## Invalid TokenSessionID' );
          Cogumelo::debug( 'CogumeloSessionController: ## $_SESSION = '.json_encode($_SESSION) );
          session_unset();
          session_destroy();
          $tkSID = false;
        }
      }


      // $tkSID puede borrarse en el anterior if() invalidando el ID obtenido y dando lugar a uno nuevo
      if( !$tkSID ) {
        session_start();
        session_regenerate_id(true);
        // error_log( '(Notice) NEW TokenSessionID -> NEW session' );
        $this->tokenSessionID = session_id();
        $_SESSION[ 'cogumeloSessionId' ] = $this->tokenSessionID;
        $_SESSION[ 'cogumeloSessionNew' ] = true;
        $_SESSION[ 'cogumeloSessionTimeCreate' ] = time();
        $_SESSION[ 'cogumeloSessionTimePrev' ] = false;
        $_SESSION[ 'cogumeloSessionTimeLast' ] = false;
        $_SESSION[ 'cogumeloSessionRemoteAddr' ] = $remoteAddr;
        $_SESSION[ 'cogumeloSessionRemoteAddrPrev' ] = false;
        $_SESSION[ 'cogumeloSessionRemoteAddrChange' ] = false;
      }
    } // if( !== 'cli' )


    global $C_SESSION_ID;
    $C_SESSION_ID = $this->getTokenSessionID();


    // error_log( __METHOD__.' $_SESSION = '.json_encode($_SESSION) );

    error_log( __METHOD__.' tokenSessionID = '.$this->tokenSessionID );


    return $tkSID;
  }




  /**
   * Recupera el TokenSessionID único.
   * @return string
   */
  public function getTokenSessionName() {
    // error_log( __METHOD__ );

    return $this->tokenSessionName;
  }


  /**
   * Recupera el TokenSessionID único.
   * @return string
   */
  public function getTokenSessionID() {
    // error_log( __METHOD__ );

    return $this->tokenSessionID;
  }

} // END CogumeloSessionController class
