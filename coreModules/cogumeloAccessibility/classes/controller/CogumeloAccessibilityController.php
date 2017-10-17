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
      $accessibilityMode = $_GET[ $this->getParam ];
      Cogumelo::log( ' $_GET -> '.$accessibilityMode, 'AccessibilityMode' );
    }
    elseif( isset( $_POST[ $this->tkName ] ) ) {
      $accessibilityMode = $_POST[ $this->tkName ];
      Cogumelo::log( ' $_POST -> '.$accessibilityMode, 'AccessibilityMode' );
    }
    elseif( isset( $_SERVER[ 'HTTP_X_'.$this->tkName ] ) ) {
      $accessibilityMode = $_SERVER[ 'HTTP_X_'.$this->tkName ];
      Cogumelo::log( ' HTTP_X -> '.$accessibilityMode, 'AccessibilityMode' );
    }
    elseif( isset( $_COOKIE[ $this->tkName ] ) ) {
      $accessibilityMode = $_COOKIE[ $this->tkName ];
      Cogumelo::log( ' $_COOKIE -> '.$accessibilityMode, 'AccessibilityMode' );
    }
    elseif( isset( $_SESSION[ $this->tkName ] ) ) {
      $accessibilityMode = $_SESSION[ $this->tkName ];
      // Cogumelo::log( ' $_SESSION -> '.$accessibilityMode, 'AccessibilityMode' );
    }
    elseif( Cogumelo::issetSetupValue('mod:cogumeloAccessibility:mode') ) {
      $accessibilityMode = Cogumelo::getSetupValue('mod:cogumeloAccessibility:mode');
      Cogumelo::log( ' getSetupValue -> '.$accessibilityMode, 'AccessibilityMode' );
    }

    // Limpieza
    $accessibilityMode = ( $accessibilityMode === '1' || $accessibilityMode === 1 ) ? 1 : 0;

    $_SESSION[ $this->tkName ] = $accessibilityMode;
    Cogumelo::setSetupValue( 'mod:cogumeloAccessibility:mode', $accessibilityMode );
    Cogumelo::addSetupValue( 'mod:mediaserver:publicConf:javascript:setupFields', 'mod:cogumeloAccessibility:mode' );
    Cogumelo::addSetupValue( 'mod:mediaserver:publicConf:smarty:setupFields', 'mod:cogumeloAccessibility:mode' );

    // Cogumelo::log( ' $accessibilityMode -> '.$accessibilityMode, 'AccessibilityMode' );

    return $accessibilityMode;
  }

} // END CogumeloSessionController class
