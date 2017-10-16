<?php
cogumeloSession::autoIncludes();


/**
 * Gestión de formularios. Campos, Validaciones, Html, Ficheros, ...
 *
 * @package Module cogumeloAccessibility
 */
class CogumeloAccessibilityController {

  public $tkName = 'cogumeloAccessibilityMode';
  public $getParam = 'wca'; // Use tkName

  private $sessionCtrl = false;

  /**
   * Constructor. Crea el TokenSessionID o lo carga del entorno y lo asigna a $C_SESSION_ID.
   */
  public function __construct() {
    $this->sessionCtrl = new CogumeloSessionController();

    $setupGetParam = Cogumelo::getSetupValue('mod:cogumeloAccessibility:getParam');
    if( !empty($setupGetParam) ) {
      $this->getParam = $setupGetParam;
    }
  }



  public function evalAccessibilityMode() {
    $accessibilityMode = 0;

    if( isset( $_GET[ $this->getParam ] ) ) {
      Cogumelo::log( __METHOD__.' $_GET', 'AccessibilityMode' );
      $accessibilityMode = $_GET[ $this->getParam ];
    }
    elseif( isset( $_POST[ $this->tkName ] ) ) {
      Cogumelo::log( __METHOD__.' $_POST', 'AccessibilityMode' );
      $accessibilityMode = $_POST[ $this->tkName ];
    }
    elseif( isset( $_SERVER[ 'HTTP_X_'.$this->tkName ] ) ) {
      Cogumelo::log( __METHOD__.' HTTP_X', 'AccessibilityMode' );
      $accessibilityMode = $_SERVER[ 'HTTP_X_'.$this->tkName ];
    }
    elseif( isset( $_COOKIE[ $this->tkName ] ) ) {
      Cogumelo::log( __METHOD__.' $_COOKIE', 'AccessibilityMode' );
      $accessibilityMode = $_COOKIE[ $this->tkName ];
    }
    elseif( isset( $_SESSION[ $this->tkName ] ) ) {
      // Cogumelo::log( __METHOD__.' $_SESSION', 'AccessibilityMode' );
      $accessibilityMode = $_SESSION[ $this->tkName ];
    }
    elseif( Cogumelo::issetSetupValue('mod:cogumeloAccessibility:mode') ) {
      Cogumelo::log( __METHOD__.' getSetupValue', 'AccessibilityMode' );
      $accessibilityMode = Cogumelo::getSetupValue('mod:cogumeloAccessibility:mode');
    }

    // Limpieza
    $accessibilityMode = ( $accessibilityMode === '1' || $accessibilityMode === 1 ) ? 1 : 0;

    $_SESSION[ $this->tkName ] = $accessibilityMode;
    Cogumelo::setSetupValue( 'mod:cogumeloAccessibility:mode', $accessibilityMode );
    Cogumelo::addSetupValue( 'mod:mediaserver:publicConf:javascript:setupFields', 'mod:cogumeloAccessibility:mode' );

    // Cogumelo::log( __METHOD__.' = '.$accessibilityMode, 'AccessibilityMode' );

    return $accessibilityMode;
  }

} // END CogumeloSessionController class
