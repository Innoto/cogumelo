<?php


/**
 * Gestión de formularios. Campos, Validaciones, Html, Ficheros, ...
 *
 * @package Module CogumeloSession
 */
class CogumeloSessionController {

  private $tokenSessionID = false;

  /**
   * Constructor. Crea el TokenSessionID o lo carga del entorno y lo asigna a $C_SESSION_ID.
   */
  public function __construct() {
    global $C_SESSION_ID;
    $this->tokenSessionID = $C_SESSION_ID;
  }

  public function prepareTokenSessionEnvironment() {
    $tkName = 'CGMLTOKENSESSID';
    session_name( $tkName );

    if( !isset( $_COOKIE[ $tkName ] ) ) {
      if( isset( $_POST[ $tkName ] ) && trim( $_POST[ $tkName ] ) !== '' ) {
        session_id( $_POST[ $tkName ] );
      }
      elseif( isset( $_SERVER[ 'HTTP_X_'.$tkName ] ) && trim( $_SERVER[ 'HTTP_X_'.$tkName ] ) !== '' ) {
        session_id( $_SERVER[ 'HTTP_X_'.$tkName ] );
      }
    }

    // -H "Authorization: Bearer mytoken123"
    // https://tools.ietf.org/html/rfc1945#section-11

    return $tkName;
  }

  /**
   * Recupera el TokenSessionID único.
   * @return string
   */
  public function getTokenSessionID() {
    return $this->tokenSessionID;
  }

} // END CogumeloSessionController class
