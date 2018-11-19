<?php

Cogumelo::load('coreView/View.php');
cogumeloSession::autoIncludes();


/**
 * Gestión de sesiones Cogumelo.
 *
 * @package Module CogumeloSession
 */
class CogumeloSessionView extends View {

  private $controller = false;

  public function __construct( $base_dir = false ) {
    // error_log( __METHOD__ );

    parent::__construct( $base_dir );

    $this->controller = new CogumeloSessionController();
  }


  /**
   * Evaluate the access conditions and report if can continue
   *
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

    error_log( __METHOD__.' tokenSessionID = '.$info['TokenSessionID'] );

    $this->sendJsonResponse( $info );
  }

  /*
    Exemplos con jquery (borrando as cookies para forzar o paso de "Token session ID")

    // COGUMELO GET session info
    $.ajax({
      url: '/cgml-session.json', type: 'GET',
      data: formData, cache: false, contentType: false, processData: false,
      success: function setStatusSuccess( $jsonData, $textStatus, $jqXHR ) {
        cogumelo.log( 'jsonData: ', $jsonData );
      }
    });


    // User Login
    var formData = new FormData();
    formData.append( 'CGMLTOKENSESSID', 'fkvf8lohog874797vkcfs3vq16' );
    formData.append( 'user', 'jmporto@innoto.es' );
    formData.append( 'pass', 'olameu' );
    $.ajax({
      url: 'http://galiciaagochada/api/core/userlogin', type: 'POST',
      data: formData, cache: false, contentType: false, processData: false,
      success: function setStatusSuccess( $jsonData, $textStatus, $jqXHR ) {
        cogumelo.log( 'jsonData: ', $jsonData );
      }
    });


    // GET User session
    $.ajax({
      url: 'http://galiciaagochada/api/core/usersession', type: 'GET',
      headers: {'X-CGMLTOKENSESSID': 'fkvf8lohog874797vkcfs3vq16'},
      cache: false, contentType: false, processData: false,
      success: function setStatusSuccess( $jsonData, $textStatus, $jqXHR ) {
        cogumelo.log( 'jsonData: ', $jsonData );
      }
    });


    // User Logout
    var formData = new FormData();
    formData.append( 'CGMLTOKENSESSID', 'fkvf8lohog874797vkcfs3vq16' );
    $.ajax({
      url: 'http://galiciaagochada/api/core/userlogout', type: 'POST',
      data: formData, cache: false, contentType: false, processData: false,
      success: function setStatusSuccess( $jsonData, $textStatus, $jqXHR ) {
        cogumelo.log( 'jsonData: ', $jsonData );
      }
    });
  */

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
