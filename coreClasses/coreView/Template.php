<?php

Cogumelo::load('coreController/ModuleController.php');

//
//  Template Class (Extends smarty library)
//

class Template extends Smarty
{
  var $tpl;
  var $baseDir;

  var $blocks = array();

  var $css_autoincludes = array();
  var $css_includes = array();
  var $js_autoincludes = array();
  var $js_includes = array();

  /**
   * Globals
   **/
  var $cgmSmartyConfigDir = SMARTY_CONFIG;
  var $cgmSmartyCompileDir = SMARTY_COMPILE;
  var $cgmSmartyCacheDir = SMARTY_CACHE;
  var $cgmMediaserverCompileLess = MEDIASERVER_COMPILE_LESS;
  var $cgmMediaserverHost = MEDIASERVER_HOST;
  var $cgmMediaserverUrlDir = MOD_MEDIASERVER_URL_DIR;


  /**
   * Carga la configuracion inicial
   *
   * @param string $baseDir
   **/
  public function __construct( $baseDir ) {
    parent::__construct();

    $this->baseDir = $baseDir;

    // En caso de que Smarty no encuentre un TPL, usa este metodo para buscarlo
    $this->default_template_handler_func = 'ModuleController::cogumeloSmartyTemplateHandlerFunc';

    // Inicializamos atributos internos de SMARTY
    $this->config_dir = $this->cgmSmartyConfigDir;
    $this->compile_dir = $this->cgmSmartyCompileDir;
    $this->cache_dir = $this->cgmSmartyCacheDir;

    // Smarty Hack: http://www.smarty.net/forums/viewtopic.php?t=21352&sid=88c6bbab5fb1fd84d3e4f18857d3d10e
    Smarty::muteExpectedErrors();
  }


  /**
   Establece el contenido de un bloque
   *
   * @param string $blockName
   * @param string $blockObject
   **/
  public function setBlock( $blockName, $blockObject ) {
    $this->blocks[ $blockName ] = array( $blockObject );
  }

  /**
   Añade otro template al contenido de un bloque
   *
   * @param string $blockName
   * @param string $blockObject
   **/
  public function addToBlock( $blockName, $blockObject ) {
    $this->blocks[ $blockName ][] = $blockObject;
  }


  /**
   Añade un script JS para cargar en el HTML
   *
   * @param string $file_path
   * @param string $module
   * @param bool $is_autoinclude
   **/
  public function addClientScript( $file_path, $module = false, $is_autoinclude = false ) {

    switch( $module ) {
      case false:
        $base_path = '/'.$this->cgmMediaserverUrlDir.'/';
        break;
      case 'vendor':
        $base_path = $this->cgmMediaserverHost.'vendor/';
        break;
      case 'vendor/bower':
        $base_path = $this->cgmMediaserverHost.'vendor/bower/';
        break;
      case 'vendor/manual':
        $base_path = $this->cgmMediaserverHost.'vendor/manual/';
        break;
      default:
        $base_path = '/'.$this->cgmMediaserverUrlDir.'/module/'.$module.'/';
        break;
    }

    $include_chain = '<script type="text/javascript" src="'.$base_path.$file_path.'"></script>';

    if( $is_autoinclude ){
      if ( in_array( $include_chain, $this->js_autoincludes ) === false ) {
        $this->js_autoincludes[] = $include_chain;
      }
    }
    else {
      if ( in_array( $include_chain, $this->js_includes ) === false ) {
        $this->js_includes[] = $include_chain;
      }
    }
  }

  /**
   Añade un CSS para cargar en el HTML
   *
   * @param string $file_path
   * @param string $module
   * @param bool $is_autoinclude
   **/
  public function addClientStyles( $file_path, $module = false, $is_autoinclude = false ) {

    switch( $module ) {
      case false:
        $base_path = '/'.$this->cgmMediaserverUrlDir.'/';
        break;
      case 'vendor':
        $base_path = $this->cgmMediaserverHost.'vendor/';
        break;
      case 'vendor/bower':
        $base_path = $this->cgmMediaserverHost.'vendor/bower/';
        break;
      case 'vendor/manual':
        $base_path = $this->cgmMediaserverHost.'vendor/manual/';
        break;
      default:
        $base_path = '/'.$this->cgmMediaserverUrlDir.'/module/'.$module.'/';
        break;
    }

    if( !$this->cgmMediaserverCompileLess && substr($file_path, -5) == '.less' ) {
      $file_rel = "stylesheet/less";
    }
    else {
      $file_rel = "stylesheet";
    }

    $include_chain = '<link rel="'.$file_rel.'" type="text/css" href="'.$base_path.$file_path.'">';

    if( $is_autoinclude ) {
      if ( in_array( $include_chain, $this->css_autoincludes ) === false ) {
        $this->css_autoincludes[] = $include_chain;
      }
    }
    else {
      if ( in_array( $include_chain, $this->css_includes ) === false ) {
        $this->css_includes[] = $include_chain;
      }
    }
  }

  /**
   Crea el HTML que carga los Scripts
   *
   * @param bool $ignoreAutoincludes
   * @return string $is_autoinclude
   **/
  public function getClientScriptHtml( $ignoreAutoincludes = false ) {
    if( $ignoreAutoincludes ) {
      $html = implode( "\n", array_unique( $this->js_includes ) );
    }
    else {
      $html = implode( "\n", array_unique( array_merge( $this->js_autoincludes, $this->js_includes ) ) );
    }

    return( $html );
  }

  /**
   Crea el HTML que carga los Styles
   *
   * @param bool $ignoreAutoincludes
   * @return string $is_autoinclude
   **/
  public function getClientStylesHtml( $ignoreAutoincludes = false ) {
    if( $ignoreAutoincludes ) {
      $html = implode( "\n", array_unique( $this->css_includes ) );
    }
    else {
      $html = implode( "\n", array_unique( array_merge( $this->css_autoincludes, $this->css_includes ) ) );
    }

    return( $html );
  }



  /**
   Establece el template a utilizar
   *
   * @param string $file_name
   * @param string $module
   **/
  public function setTpl( $file_name, $module = false ) {

    $this->tpl = ModuleController::getRealFilePath( 'classes/view/templates/'.$file_name, $module );
  }

  /**
   Crea el HTML a partir de los datos y plantillas indicados y lo devuelve como STRING
   *
   * @return string HTML generado
   **/
  public function execToString() {

    return( $this->exec( true ) );
  }

  /**
   Crea el HTML a partir de los datos y plantillas indicados
   *
   * @global string $cogumeloIncludesCSS
   * @global string $cogumeloIncludesJS
   * @param bool $toString
   * @return string $htmlCode
   **/
  public function exec( $toString = false ) {

    $htmlCode = '';

    if( $this->tpl && file_exists( $this->tpl ) ) {

      global $cogumeloIncludesCSS;
      global $cogumeloIncludesJS;

      if( is_array( $cogumeloIncludesCSS ) ) {
        foreach( $cogumeloIncludesCSS as $fileCss ) {
          $this->addClientStyles( $fileCss['src'], $fileCss['module'], true );
        }
      }

      if( is_array( $cogumeloIncludesJS ) ){
        foreach( $cogumeloIncludesJS as $fileJs ){
          $this->addClientScript( $fileJs['src'], $fileJs['module'], true );
        }
      }

      // conf Variables
      $lessConfInclude = '';
      $jsConfInclude = '<script type="text/javascript" src="'.$this->cgmMediaserverHost.$this->cgmMediaserverUrlDir.'/jsConfConstants.js'.'"></script>'."\n";

      if( $this->cgmMediaserverCompileLess == false ) {
        $lessConfInclude = '<link rel="stylesheet/less" type="text/css" href="'.$this->cgmMediaserverHost.$this->cgmMediaserverUrlDir.'/lessConfConstants.less'.'">'."\n";
      }

      // assign
      $this->assign( 'css_includes', $lessConfInclude . $this->getClientStylesHtml() );
      $this->assign( 'js_includes', $jsConfInclude . $this->lessClientCompiler() . $this->getClientScriptHtml() );



      foreach( $this->blocks as $blockName => $blockObjects ) {
        $htmlBlock = '';

        foreach( $blockObjects as $blockTemplate ) {
          $htmlBlock .= $blockTemplate->execBlock();
        }

        $this->assign( $blockName, $htmlBlock );
      }



      if( $toString ) {
        $htmlCode = $this->fetch( $this->tpl );
      }
      else {
        $this->display( $this->tpl );
      }
      Cogumelo::debug( 'Template class displays tpl '.$this->tpl );
    }
    else {
      Cogumelo::error( 'Template: no tpl file defined or not found: '.$this->tpl );
    }

    if( $toString ) {
      return( $htmlCode );
    }
  }

  /**
   Crea el HTML a partir de los datos y plantillas indicados sin esqueleto
   *
   * @return string $htmlCode
   **/
  public function execBlock() {

    $htmlCode = '';

    if( $this->tpl && file_exists( $this->tpl ) ) {
      // assign
      $this->assign( 'css_includes', $this->getClientStylesHtml( true ) );
      $this->assign( 'js_includes', $this->getClientScriptHtml( true ) );



      foreach( $this->blocks as $blockName => $blockObjects ) {
        $htmlBlock = '';

        foreach( $blockObjects as $blockTemplate ) {
          $htmlBlock .= $blockTemplate->execBlock();
        }

        $this->assign( $blockName, $htmlBlock );
      }



      $htmlCode = $this->fetch( $this->tpl );
      Cogumelo::debug( 'Template class displays tpl '.$this->tpl );
    }
    else {
      Cogumelo::error( 'Template: no tpl file defined or not found: '.$this->tpl );
    }

    return( $htmlCode );
  }

  /**
   Introduce o script para compilar o LESS con JS
   *
   * @return string $ret
   **/
  public function lessClientCompiler() {
    $ret = '';

    if( !$this->cgmMediaserverCompileLess ) {
      $ret = "\n".'<script>less = { env: "development", async: false, fileAsync: false, poll: 1000, '.
        'functions: { }, dumpLineNumbers: "all", relativeUrls: true, errorReporting: "console" }; </script>'."\n".
        '<script type="text/javascript" src="/vendor/bower/less/dist/less.min.js"></script>';
    }

    return $ret;
  }

}

