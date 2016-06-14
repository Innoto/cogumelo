<?php

Cogumelo::load('coreView/View.php');
cogumeloSession::autoIncludes();


/**
 * Gestión de sesiones Cogumelo.
 *
 * @package Module CogumeloSession
 **/
class CogumeloSessionView extends View {

  private $controller = false;

  public function __construct( $base_dir = false ) {
    parent::__construct( $base_dir );

    $this->controller = new CogumeloSessionController();
  }


  /**
   * Evaluate the access conditions and report if can continue
   * @return bool : true -> Access allowed
   */
  public function accessCheck() {
    return true;
  }


  public function jsonTokenSession() {
    $info = array(
      'TokenSessionName' => session_name(),
      'TokenSessionID' => $this->controller->getTokenSessionID(),
      'SendOptions' => [ 'POST', 'COOKIE', 'HEADER' ]
    );
    // phpinfo();
    $this->sendJsonResponse( $info );
  }


  /**
   * Envía el JSON con el Ok o los errores al navegador
   *
   * @param mixed $moreInfo Added to json in the 'moreInfo' field
   *
   * @return string
   */
  public function sendJsonResponse( $info ) {
    $json = json_encode( $info );

    header('Content-Type: application/json; charset=utf-8');
    echo $json;

    return $json;
  }

} // class CogumeloSessionView extends View
