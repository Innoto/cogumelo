<?php


/**
* RequestController Class
*
* This is the controller that cogumelo class and modules sys use to manage http requests
*
* @author: pablinhob
*/
class RequestController
{
  var $url_path;
  var $include_base_path;
  var $leftover_url = "";
  var $is_last_request = false;
  var $urlPatterns = array();

  var $view404 = 'MasterView::page404';


  public function __construct( $urlPatterns, $url_path, $include_base_path = false ) {
    //error_log( 'RequestController::__construct: urlPatterns, '.$url_path.', '.$include_base_path );
    // error_log( 'urlPatterns='.print_r( $urlPatterns, true ) );

    $this->urlPatterns = $urlPatterns;
    $this->url_path = $url_path;

    if( $include_base_path ) {
      $this->include_base_path = $include_base_path;
    }
    else {
      $this->include_base_path = SITE_PATH;
      $this->is_last_request = true; // is last request on app
    }

    $this->exec();
  }


  public function exec() {
    foreach( $this->urlPatterns as $url_pattern_key => $url_pattern_action ) {

      if( preg_match( $url_pattern_key, $this->url_path, $m_url ) ) {
        if( array_key_exists( 1, $m_url ) ) {
          $this->readPatternAction( $m_url, $url_pattern_action );
        }
        else {
          $this->readPatternAction( '', $url_pattern_action );
        }
        if( array_key_exists( 2, $m_url ) ) {
          $this->leftover_url = $m_url[ '2' ];
        }
        return;
      }
    }

    // if is last request and any pattern found
    if( $this->is_last_request ) {
      // Cogumelo::error( "URL not found ".$_SERVER['REQUEST_URI']."\n" );
      $this->notAppUrl();
    }
    else {
      $this->leftover_url = $this->url_path;
    }
  }


  private function readPatternAction( $url_path, $url_pattern_action ) {
    if( preg_match( '^(redirect:)(.*)^', $url_pattern_action, $m ) ) {
      self::redirect( $m[2] );
    }
    else if( preg_match( '^(noendview:)(.*)^', $url_pattern_action, $m ) ) {
      $this->leftover_url = $url_path[1];
      $this->view( $url_path, $m[2] );
    }
    else if( preg_match( '^(view:)(.*)^', $url_pattern_action, $m ) ) {
      $this->leftover_url = '';
      $this->view( $url_path, $m[2] );
      exit;
    }
    else {
      Cogumelo::error(__METHOD__." error. No valid pattern = '$matched'");
    }
  }


  public static function redirect( $redirect_url, $httpCode = '301' ) {
    //error_log( 'RequestController::redirect '.$redirect_url );
    /*
    if( $httpCode === '301' ) {
      header( 'HTTP/1.1 301 Moved Permanently' );
    }
    else {
      header( 'HTTP/1.1 '.$httpCode );
    }
    */
    header( 'Location: '.$redirect_url, true, $httpCode );
    ob_flush();
    flush();
    exit;
  }


  public function view( $url_path, $view_reference ) {
    //error_log( 'RequestController::view: '.$url_path.', '.$view_reference  );

    preg_match( '^(.*)::(.*)^', $view_reference, $m );
    $classname = $m['1'];
    $methodname = $m['2'];

    // require class script from views folder
    include( $this->include_base_path .'/classes/view/'. $classname.'.php' );

    eval( '$current_view = new '.$classname.'( $this->include_base_path );' );

    if( $url_path === '' ) {
      eval( '$current_view->'.$methodname.'();' );
    }
    else {
      eval( '$current_view->'.$methodname.'( $url_path );' );
      // eval( '$current_view->'.$methodname.'(array("'.implode( '","', $url_path).'") );' );
    }
  }


  public function getLeftoeverUrl() {
    //error_log( 'RequestController::getLeftoeverUrl' );

    return $this->leftover_url;
  }


  public function notAppUrl() {
    error_log( 'RequestController::notAppUrl '.$this->url_path );

    $alternative = false;

    // require class script from views folder
    // include( '/home/proxectos/geozzy/distModules/geozzy/classes/controller/UrlAliasController.php' );
    if( defined( 'COGUMELO_APP_URL_ALIAS_CONTROLLER' ) && file_exists( COGUMELO_APP_URL_ALIAS_CONTROLLER ) ) {
      include( COGUMELO_APP_URL_ALIAS_CONTROLLER );
      $urlAliasController = new UrlAliasController();
      $alternative = $urlAliasController->getAlternative( $this->url_path );
    }


    if( $alternative ) {
      error_log( 'RequestController::notAppUrl $alternative= '. print_r( $alternative, true ) );
      if( $alternative[ 'code' ] === 'alias' ) {
        error_log( 'RequestController::notAppUrl Alias-viewUrl '.$alternative[ 'url' ] );
        global $_C;
        $_C->viewUrl( $alternative[ 'url' ] );
        /**
          TODO: NO USA LANG PORQUE FALLA viewUrl
          $_C->viewUrl( $langUrl . $alternative[ 'url' ] );
        */
      }
      else {
        error_log( 'RequestController::notAppUrl Redirect '.$alternative[ 'code' ].' a '.$alternative[ 'url' ] );
        $this->redirect( $alternative[ 'url' ], $alternative[ 'code' ] );
      }
    }
    else {
      Cogumelo::error( "URL not found ".$_SERVER['REQUEST_URI']."\n" );
      $this->view( '', $this->view404 );
    }
    exit();
  }

  // gets the url parameters, validate them in function of the validation passed and returns and array with pairs key=>value
  static function processUrlParams($urlParams, $validation) {
    error_log( 'RequestController::processUrlParams' );

    $url_parts = explode('/', ltrim($urlParams[1], '/') );
    $params = array();

    if( sizeof( $url_parts ) > 1 ) {
      for($i=0;$i<sizeof($url_parts);$i=$i+2){
        $par = $url_parts[$i];
        if ($validation[$par]){
          if (preg_match ( $validation[$par] , $url_parts[$i+1])){
            $params[$url_parts[$i]] = $url_parts[$i+1];
          }
        }
      }
    }

    return $params;
  }

}
