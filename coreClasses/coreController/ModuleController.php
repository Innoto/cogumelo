<?php


/**
* ModuleController Class
*
* Controls all features of module system
* Learn more about modules in https://github.com/Innoto/cogumelo/wiki/Cogumelo-basics#wiki-modules
*
* @author: pablinhob
*/

require_once( COGUMELO_LOCATION . "/coreClasses/coreController/RequestController.php" );

class ModuleController
{

  var $url_path;
  var $module_paths = array();


  public function __construct( $url_path = false, $from_shell = false ) {
    $this->url_path = $url_path;
    $this->setModules();

    $this->includeModules();

    if( !$from_shell ) {
      $this->execModules();
    }
  }


  public function setModules() {
    global $C_ENABLED_MODULES;

    if ( !is_array( $C_ENABLED_MODULES ) ) {
      return;
    }

    foreach( $C_ENABLED_MODULES as $module_name ) {
      if( $module_main_class = self::getRealFilePath($module_name.'.php' ,$module_name) ) {
        $this->module_paths[$module_name] = dirname($module_main_class); // get module.php container
      }
      else {
        $this->module_paths[$module_name] = false;
        Cogumelo::error("Module not found: ".$module_name);
      }
    }
  }


  public function execModules() {
    global $C_INDEX_MODULES;

    foreach( $C_INDEX_MODULES as $module_name ) {
      $this->execModule($module_name);
    }
  }

  public function execModule( $module_name ) {
    if($this->module_paths[$module_name] == false) {
      Cogumelo::error("Module '".$module_name. "' not found.");
    }
    else {
      $modulo = new $module_name();
      $this->request = new RequestController( $modulo->getUrlPatternsToArray(), $this->url_path, $this->module_paths[$module_name] );
      $this->url_path = $this->request->getLeftoeverUrl();
      Cogumelo::debug("Reading UrlPatterns from: ".$module_name);
    }
  }

  public function includeModules() {

    global $C_ENABLED_MODULES;

    foreach( $C_ENABLED_MODULES as $module_name ) {
      $mod_path = $this->module_paths[$module_name];
      require_once($mod_path.'/'.$module_name.'.php');
    }
  }

  public function getLeftUrl() {

    return $this->url_path;
  }


  static public function getRealFilePath( $file_relative_path, $module = false ) {
    $retPath = false;

    if(!$module) {
      $retPath = SITE_PATH.$file_relative_path;
    }
    else {
      global $C_ENABLED_MODULES;
      if(in_array($module, $C_ENABLED_MODULES)) {
        // APP modules
        if( file_exists(SITE_PATH.'/modules/'.$module.'/'.$file_relative_path) ) {
          $retPath = SITE_PATH.'/modules/'.$module.'/'.$file_relative_path;
        }
        // DIST modules
        else if( COGUMELO_DIST_LOCATION != false && file_exists( COGUMELO_DIST_LOCATION.'/distModules/'.$module.'/'.$file_relative_path ) ) {
          $retPath = COGUMELO_DIST_LOCATION.'/distModules/'.$module.'/'.$file_relative_path;
        }
        // CORE modules
        else if( file_exists( COGUMELO_LOCATION.'/coreModules/'.$module.'/'.$file_relative_path ) ) {
          $retPath = COGUMELO_LOCATION.'/coreModules/'.$module.'/'.$file_relative_path;
        }
        else {
          Cogumelo::error("ModuleController: '".$file_relative_path."'' not found into module '".$module."' ");
        }

      }
      else {
        Cogumelo::error('ModuleController: Module named as "'.$module.'" is not enabled. Add it to $C_ENABLED_MODULES setup.php array' );
      }
    }
    return $retPath;
  }

  /**
   * Default Template Handler
   *
   * Called when Smarty's file: resource is unable to load a requested file
   *
   * @param string   $type     resource type (e.g. "file", "string", "eval", "resource")
   * @param string   $name     resource name (e.g. "foo/bar.tpl")
   * @param string  &$content  template's content
   * @param integer &$modified template's modification time
   * @param Smarty   $smarty   Smarty instance
   * @return string|boolean   path to file or boolean true if $content and $modified
   *                          have been filled, boolean false if no default template
   *                          could be loaded
   */
  static public function cogumeloSmartyTemplateHandlerFunc( $type, $name, &$content, &$modified, Smarty $smarty ) {

    $newName = false;

    error_log( 'cogumeloSmartyTemplateHandlerFunc: ' );
    error_log( '  type: ' . $type );
    error_log( '  name: ' . $name );
    error_log( '  content: ' . print_r( $content, true ) );
    error_log( '  modified: ' . print_r( $modified, true ) );
    //error_log( '  smarty: ' . print_r( $smarty, true ) );
    //print_r( $smarty );

    if( $type == 'file' ) {

      // Caso 1: Busco con getRealFilePath
      if( $newName === false ) {
        $tmpName = ModuleController::getRealFilePath( 'classes/view/templates/'.$name );
        if( file_exists ( $tmpName ) ) {
          $newName = $tmpName;
          error_log( 'Solucion getRealFilePath: ' . $newName );
        }
      }

      // Caso 2: Si se necesita un tpl que no es el principal del obj Smarty, miro en su mismo dir
      if( $newName === false && isset( $smarty->tpl ) ) {
        $smartyTpl = pathinfo( $smarty->tpl );
        if( $smartyTpl[ 'basename' ] !== $name ) {
          $tmpName = $smartyTpl[ 'dirname' ] .'/'. $name;
          if( file_exists ( $tmpName ) ) {
            $newName = $tmpName;
            error_log( 'Solucion misma carpeta: ' . $newName );
          }
        }
      }

    }

    /*
      // return corrected filepath
      return "/tmp/some/foobar.tpl";

      // return a template directly
      $content = "the template source";
      $modified = time();
      return true;

      // tell smarty that we failed
      return false;
    */

    return $newName;
  }


}
