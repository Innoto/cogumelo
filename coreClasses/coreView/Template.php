<?php

Cogumelo::load('coreController/ModuleController.php');

//
//  Template Class (Extends smarty library)
//

class Template extends Smarty
{
  var $tpl = false;
  var $baseDir;

  var $fileBacktrace = false;

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

  var $cgmMediaserverCompileLess = MEDIASERVER_PRODUCTION_MODE;
  var $cgmMediaserverHost = MEDIASERVER_HOST;
  var $cgmMediaserverUrlDir = false;
  var $cgmMediaUrlDir = MOD_MEDIASERVER_URL_DIR;


  /**
   * Carga la configuracion inicial
   *
   * @param string $baseDir
   **/
  public function __construct( $baseDir = false ) {
    // Call Smarty's constructor

    if( MEDIASERVER_PRODUCTION_MODE ) {
      $this->cgmMediaserverUrlDir = MEDIASERVER_FINAL_CACHE_PATH;
    }
    else {
      $this->cgmMediaserverUrlDir = MOD_MEDIASERVER_URL_DIR;
    }

    parent::__construct();

    $this->baseDir = $baseDir;

    // En caso de que Smarty no encuentre un TPL, usa este metodo para buscarlo
    $this->default_template_handler_func = 'ModuleController::cogumeloSmartyTemplateHandlerFunc';


    // Inicializamos atributos internos de SMARTY
    // $this->setTemplateDir( $this->cgmSmartyTplDir ); // Intentando evitar error "smarty_resource.php line:744"
    $this->setConfigDir( $this->cgmSmartyConfigDir );
    $this->setCompileDir( $this->cgmSmartyCompileDir );
    $this->setCacheDir( $this->cgmSmartyCacheDir );


    global $MEDIASERVER_SMARTY_GLOBALS, $MEDIASERVER_SMARTY_CONSTANTS;
    if( is_array( $MEDIASERVER_SMARTY_GLOBALS ) && count( $MEDIASERVER_SMARTY_GLOBALS ) > 0 ) {
      foreach( $MEDIASERVER_SMARTY_GLOBALS as $globalKey ) {
        if( isset( $GLOBALS[ $globalKey ] ) ) {
          $this->assign( 'GLOBAL_'.$globalKey, $GLOBALS[ $globalKey ] );
        }
      }
    }
    if( is_array( $MEDIASERVER_SMARTY_CONSTANTS ) && count( $MEDIASERVER_SMARTY_CONSTANTS ) > 0 ) {
      foreach( $MEDIASERVER_SMARTY_CONSTANTS as $key => $value ) {
        $this->assign( $key, $value );
      }
    }


    // Smarty Hack: http://www.smarty.net/forums/viewtopic.php?t=21352&sid=88c6bbab5fb1fd84d3e4f18857d3d10e
    Smarty::muteExpectedErrors(); // IGNORANDO ERRORES de Smarty
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


    if( substr( $file_path, -3) == '.js'  && MEDIASERVER_NOT_CACHE_JS == true ){
      $mediaPath = '/' . $this->cgmMediaUrlDir;
    }
    else {
      $mediaPath = $this->cgmMediaserverHost . $this->cgmMediaserverUrlDir;
    }

    switch( $module ) {
      case false:
        $base_path = $mediaPath.'/';
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
        $base_path = $mediaPath.'/module/'.$module.'/';
        break;
    }


    $includeObj = array(
      'type' => "text/javascript",
      'src' => $base_path.$file_path
    );


    $includeRef = $base_path.$file_path;


    if( $is_autoinclude ) {
        $this->js_autoincludes[$includeRef] = $includeObj;
    }
    else {
        $this->js_includes[$includeRef] = $includeObj;
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


    if( $this->cgmMediaserverCompileLess && substr($file_path, -5) == '.less' ) {
      $lessCompiledExtension = '.css';
    }
    else {
      $lessCompiledExtension  = '';
    }

    $includeObj = array(
      'rel' => $file_rel,
      'type' => "text/css",
      'src' => $base_path.$file_path . $lessCompiledExtension
    );


    $includeRef = $base_path.$file_path.$lessCompiledExtension;


    if( $is_autoinclude ) {
        $this->css_autoincludes[$includeRef] = $includeObj;
    }
    else {
        $this->css_includes[$includeRef] = $includeObj;
    }
  }

  /**
   Crea el HTML que carga los Scripts
   *
   * @param bool $ignoreAutoincludes
   *
   * @return string $is_autoinclude
   **/

  public function getClientScriptHtml( $ignoreAutoincludes = false ) {

    global $C_LANG;
    $itemsToInclude = array();
    $html = '';


    if( $C_LANG ) {
      $langUrl = $C_LANG.'/';
    }
    else {
      $langUrl = '';
    }
    //echo $langUrl . $itemsToInclude[$this->cgmMediaserverHost.$this->cgmMediaserverUrlDir.'/jsConfConstants.js';
    /*
    $itemsToInclude[ '/' . $langUrl . $this->cgmMediaUrlDir.'/jsConfConstants.js' ] = array(
      'src'=> '/' . $langUrl . $this->cgmMediaUrlDir.'/jsConfConstants.js',
      'rel' => false ,
      'type' => 'text/javascript',
      'onlyOnce' => true
    );
    */

    if( !$ignoreAutoincludes ) {
      foreach( $this->js_autoincludes as $includeKey => $include ) {
        $itemsToInclude[ $includeKey ] = array( 'src'=> $include['src'], 'rel' => false , 'type'=> $include['type'] );
      }
    }

    foreach( $this->js_includes as $includeKey => $include ) {
      $itemsToInclude[ $includeKey ] = array( 'src'=> $include['src'], 'rel' => false , 'type'=> $include['type'] );
    }


    // generate the javascript include call

    foreach( $itemsToInclude as $include ) {
      $html .= "\t".str_replace('\\/', '/', json_encode( $include ) ) . ",  \n";
    }



    return( $html );
  }

  /**
   Crea el HTML que carga los Styles
   *
   * @param bool $ignoreAutoincludes
   *
   * @return string $is_autoinclude
   **/
  public function getClientStylesHtml( $ignoreAutoincludes = false ) {
    $itemsToInclude = array();
    $html = '';

    if( $this->cgmMediaserverCompileLess == false ) {
      $src = $this->cgmMediaserverHost.$this->cgmMediaserverUrlDir.'/lessConfConstants.less';
      $itemsToInclude[$src] =  array('src'=> $src, 'rel' => "stylesheet/less" , 'type'=> 'text/css', 'onlyOnce' => true );
    }



    if( !$ignoreAutoincludes ) {
      foreach( $this->css_autoincludes as $includeKey => $include ) {
        $itemsToInclude[$includeKey] = array('src'=> $include['src'], 'rel' => $include['rel'] , 'type'=> $include['type'] );
      }
    }

    foreach( $this->css_includes as $includeKey => $include ) {
      $itemsToInclude[$includeKey] = array('src'=> $include['src'], 'rel' => $include['rel'] , 'type'=> $include['type'] );
    }


    // generate the javascript include call
    foreach( $itemsToInclude as $include ) {
      $html .= "\t".str_replace('\\/', '/', json_encode( $include ) ) . ",\n";
    }





    return( $html );
  }



  /**
   Establece el template a utilizar
   *
   * @param string $file_name
   * @param string $module
   **/
  public function setTpl( $tplData = false, $module = false ) {
    // error_log( 'Template->setTpl('.$tplData.', '.$module.')' );

    // Esto nos puede permitir referenciar TPLs "al lado" de la clase que esta usando este metodo
    // $debugBacktrace = debug_backtrace( false, 1 );
    // error_log( 'debug_backtrace: ' . print_r( $debugBacktrace, true ) );
    // $this->fileBacktrace = $debugBacktrace['0']['file'];

    if( $tplData ) {
      if( strpos( $tplData, 'string:' ) === 0 || strpos( $tplData, 'eval:' ) === 0 ) {
        $this->tpl = $tplData;
      }
      else {
        // Asumimos que es un fichero
        $tplFile = ModuleController::getRealFilePath( 'classes/view/templates/'.$tplData, $module );
        if( $tplFile ) {
          $this->tpl = $tplFile;
        }
        else {
          $this->tpl = false;
        }
      }
    }
    else {
      $this->tpl = false;
    }

    // error_log( 'Template = ' . $this->tpl );
    return $this->tpl;
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
   * @param bool $toString
   *
   * @global string $cogumeloIncludesCSS
   * @global string $cogumeloIncludesJS
   *
   * @return string $htmlCode
   **/
  public function exec( $toString = false ) {

    // error_log( 'Template->exec('.$toString.') === ' . $this->tpl );

    $htmlCode = '';

    if( $this->tpl ) {

      global $cogumeloIncludesCSS;
      global $cogumeloIncludesJS;
      global $C_LANG;


      $langUrl = '/'.$C_LANG;

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



      // Basic includes and includers
      $clientIncludes = "\n";
      $clientIncludes .= '<script type="text/javascript">jqueryIsLoaded = ( typeof $ !== "undefined" );</script>' . "\n";
      $clientIncludes .= '<script type="text/javascript" src="/vendor/bower/jquery/dist/jquery.min.js"></script>' . "\n";
      $clientIncludes .= '<script type="text/javascript" src="/media/module/common/js/Includes.js"></script>' . "\n";
      $clientIncludes .= '<script type="text/javascript" src="'.$langUrl.'/media/jsConfConstants.js"></script>' . "\n";
      $clientIncludes .= '<script type="text/javascript" src="'.$langUrl.'/jsTranslations/getJson.js"></script>' . "\n";
      if( !$this->cgmMediaserverCompileLess ) {
        $clientIncludes .= '<script>less = { env: "development", async: false, fileAsync: false, poll: 1000, '.
          'functions: { }, dumpLineNumbers: "all", relativeUrls: true, errorReporting: "console" }; </script>'."\n".
          '<script type="text/javascript" src="/vendor/bower/less/dist/less.min.js"></script>';
      }



      $clientIncludes .= "\t<script>\n";


      $clientIncludes .= '$.holdReady( true );'."\n";

      $clientIncludes .= 'cogumelo.includes(['. "\n";
      $clientIncludes .= $this->getClientStylesHtml();
      $clientIncludes .= $this->getClientScriptHtml() ;
      $clientIncludes .= ']);'."\n\n";
      $clientIncludes .= "\t</script>\n";


      $this->assign('client_includes', $clientIncludes );


/*
      $this->assign('js_includes', $jsConfInclude . $this->lessClientCompiler() . $this->getClientScriptHtml() );
      $this->assign('css_includes', $lessConfInclude . $this->getClientStylesHtml() );
*/




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

    // error_log( 'Template->execBlock() === ' . $this->tpl );

    $htmlCode = '';

    if( $this->tpl ) {
      // assign

      $this->assign('client_includes',  $this->getClientScriptHtml( true ) . $this->getClientStylesHtml( true )  );


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



}
