<?php
cogumeloSession::autoIncludes();


/**
 * Gestión de formularios. Campos, Validaciones, Html, Ficheros, ...
 *
 * @package Module cogumeloAccessibility
 */
class CogumeloAccessibilityController {

  public $tkName = 'cogumeloAccessibilityMode';
  public $getParam = null; // Use tkName

  private $sessionCtrl = false;

  /**
   * Constructor. Crea el TokenSessionID o lo carga del entorno y lo asigna a $C_SESSION_ID.
   */
  public function __construct() {
    $this->sessionCtrl = new CogumeloSessionController();

    $setupGetParam = Cogumelo::getSetupValue('mod:cogumeloAccessibility:getParam');
    $this->getParam = ( empty($setupGetParam) ) ? $this->tkName : $setupGetParam;
  }



  public function evalAccessibilityMode() {
    $accessibilityMode = 0;

    if( isset( $_GET[ $this->getParam ] ) && trim( $_GET[ $this->getParam ] ) !== '' ) {
      error_log( __METHOD__.' $_GET' );
      $accessibilityMode = $_GET[ $this->getParam ];
    }
    elseif( isset( $_POST[ $this->tkName ] ) && trim( $_POST[ $this->tkName ] ) !== '' ) {
      error_log( __METHOD__.' $_POST' );
      $accessibilityMode = $_POST[ $this->tkName ];
    }
    elseif( isset( $_SERVER[ 'HTTP_X_'.$this->tkName ] ) && trim( $_SERVER[ 'HTTP_X_'.$this->tkName ] ) !== '' ) {
      error_log( __METHOD__.' HTTP_X' );
      $accessibilityMode = $_SERVER[ 'HTTP_X_'.$this->tkName ];
    }
    elseif( isset( $_COOKIE[ $this->tkName ] ) ) {
      error_log( __METHOD__.' $_COOKIE' );
      $accessibilityMode = $_COOKIE[ $this->tkName ];
    }
    elseif( isset( $_SESSION[ $this->tkName ] ) ) {
      error_log( __METHOD__.' $_SESSION' );
      $accessibilityMode = $_SESSION[ $this->tkName ];
    }
    elseif( Cogumelo::issetSetupValue('mod:cogumeloAccessibility:mode') ) {
      error_log( __METHOD__.' getSetupValue' );
      $accessibilityMode = Cogumelo::getSetupValue('mod:cogumeloAccessibility:mode');
    }

    $_SESSION[ $this->tkName ] = $accessibilityMode;
    Cogumelo::setSetupValue( 'mod:cogumeloAccessibility:mode', $accessibilityMode );
    Cogumelo::addSetupValue( 'mod:mediaserver:publicConf:javascript:setupFields', 'mod:cogumeloAccessibility:mode' );

    error_log( __METHOD__.' = '.$accessibilityMode );

    return $accessibilityMode;
  }

} // END CogumeloSessionController class
