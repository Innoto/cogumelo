<?php

Cogumelo::load('coreController/ModuleController.php');


//
//  Template Class (Extends smarty library)
//

class Template extends Smarty {

  var $tpl = false;
  var $baseDir;

  var $fileBacktrace = false;

  var $fragments = array();

  var $css_autoincludes = array();
  var $css_includes = array();
  var $js_autoincludes = array();
  var $js_includes = array();

  /**
   * Globals
   **/
  var $cgmSmartyConfigDir = false;
  var $cgmSmartyCompileDir = false;
  var $cgmSmartyCacheDir = false;

  var $cgmMediaserverCompileLess = false;
  var $cgmMediaserverHost = false;
  var $cgmMediaserverUrlDir = false;
  var $cgmMediaUrlDir = false;


  /**
   * Carga la configuracion inicial
   *
   * @param string $baseDir
   **/
  public function __construct( $baseDir = false ) {

    if( Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) === true ){
      require_once( Cogumelo::getSetupValue( 'setup:appTmpPath' ).'/CACHE_FLUSH_TIMESTAMP.php' );
    }

    // Call Smarty's constructor
    $this->cgmSmartyConfigDir = Cogumelo::getSetupValue( 'smarty:configPath' );
    $this->cgmSmartyCompileDir = Cogumelo::getSetupValue( 'smarty:compilePath' );
    $this->cgmSmartyCacheDir = Cogumelo::getSetupValue( 'smarty:cachePath' );

    $this->cgmMediaserverCompileLess = Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' );
    $this->cgmMediaserverHost = Cogumelo::getSetupValue( 'mod:mediaserver:host' );
    $this->cgmMediaUrlDir = Cogumelo::getSetupValue( 'mod:mediaserver:path' );

    if( Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) ) {
      $this->cgmMediaserverUrlDir = Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' );
    }
    else {
      $this->cgmMediaserverUrlDir = Cogumelo::getSetupValue( 'mod:mediaserver:path' );
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

    $this->loadSmartyPublicConf();


    // Smarty Hack: http://www.smarty.net/forums/viewtopic.php?t=21352&sid=88c6bbab5fb1fd84d3e4f18857d3d10e
    Smarty::muteExpectedErrors(); // IGNORANDO ERRORES de Smarty
  }


  public function loadSmartyPublicConf() {
    $data = array( 'publicConf' => array() );

    $publicConf = Cogumelo::getSetupValue( 'mod:mediaserver:publicConf:smarty:globalVars' );
    if( $publicConf && is_array( $publicConf ) && count( $publicConf ) > 0 ) {
      foreach( $publicConf as $globalKey ) {
        if( isset( $GLOBALS[ $globalKey ] ) ) {
          $data['publicConf'][ $globalKey ] = $GLOBALS[ $globalKey ];
        }
      }
    }
    $setupFields = Cogumelo::getSetupValue( 'mod:mediaserver:publicConf:smarty:setupFields' );
    if( $setupFields && is_array( $setupFields ) && count( $setupFields ) > 0 ) {
      foreach( $setupFields as $setupField ) {
        $data['publicConf'][ strtr( $setupField, ':', '_' ) ] = Cogumelo::getSetupValue( $setupField );
      }
    }
    $publicConf = Cogumelo::getSetupValue( 'mod:mediaserver:publicConf:smarty:vars' );
    if( $publicConf && is_array( $publicConf ) && count( $publicConf ) > 0 ) {
      foreach( $publicConf as $name => $value ) {
        $data['publicConf'][ $name ] = $value;
      }
    }

    $this->assign( 'cogumelo', $data );



    // global $MEDIASERVER_SMARTY_GLOBALS, $MEDIASERVER_SMARTY_CONSTANTS;
    // if( is_array( $MEDIASERVER_SMARTY_GLOBALS ) && count( $MEDIASERVER_SMARTY_GLOBALS ) > 0 ) {
    //   foreach( $MEDIASERVER_SMARTY_GLOBALS as $globalKey ) {
    //     if( isset( $GLOBALS[ $globalKey ] ) ) {
    //       $this->assign( 'GLOBAL_'.$globalKey, $GLOBALS[ $globalKey ] );
    //     }
    //   }
    // }
    // if( is_array( $MEDIASERVER_SMARTY_CONSTANTS ) && count( $MEDIASERVER_SMARTY_CONSTANTS ) > 0 ) {
    //   foreach( $MEDIASERVER_SMARTY_CONSTANTS as $key => $value ) {
    //     $this->assign( $key, $value );
    //   }
    // }

  }



  /**
   Establece el contenido de un Fragmento
   *
   * @param string $fragmentName
   * @param string $fragmentObject
   **/
  public function setFragment( $fragmentName, $fragmentObject ) {
    $this->fragments[ $fragmentName ] = array();
    $this->addToFragment( $fragmentName, $fragmentObject );
  }

  /**
   Añade otro template al contenido de un Fragmento
   *
   * @param string $fragmentName
   * @param string $fragmentObject
   **/
  public function addToFragment( $fragmentName, $fragmentObject ) {
    if( gettype( $fragmentObject ) === 'object' && get_class( $fragmentObject ) === 'Template' ) {
      $this->fragments[ $fragmentName ][] = $fragmentObject;
    }
    else {
      Cogumelo::error( 'ERROR: Intento de añadir algo que no es un Template ('.gettype( $fragmentObject ).') al Fragmento '.$fragmentName );
      error_log( 'ERROR: Intento de añadir algo que no es un Template ('.gettype( $fragmentObject ).') al Fragmento '.$fragmentName );
      foreach( debug_backtrace( true, 5 ) as $trace ) {
        error_log( $trace['file'] .' ('. $trace['line'] .') '. $trace['function'] );
      }
    }
  }


  // Metodos "ALIAS" que hay que dejar de usar
  public function setBlock( $fragmentName, $fragmentObject ) {
    error_log( '---DEPRECATED--- Cambiar setBlock por setFragment en '. debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 1 )[0]['file'] );
    $this->setFragment( $fragmentName, $fragmentObject );
  }
  public function addToBlock( $fragmentName, $fragmentObject ) {
    error_log( '---DEPRECATED--- Cambiar addToBlock por addToFragment en '. debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 1 )[0]['file'] );
    $this->addToFragment( $fragmentName, $fragmentObject );
  }


  /**
   Añade un script JS para cargar en el HTML
   *
   * @param string $file_path
   * @param string $module
   * @param bool $is_autoinclude
   **/
  public function addClientScript( $file_path, $module = false, $is_autoinclude = false ) {


    if( mb_substr( $file_path, -3 ) === '.js'  && Cogumelo::getSetupValue( 'mod:mediaserver:notCacheJs' ) === true ){
      $mediaPath = $this->cgmMediaserverHost . $this->cgmMediaUrlDir;
    }
    else {
      $mediaPath = $this->cgmMediaserverHost . $this->cgmMediaserverUrlDir;
    }

    switch( $module ) {
      case false:
        $base_path = $mediaPath.'/';
        break;
      case 'vendor':
      case 'vendor/bower':
      case 'vendor/yarn':
      case 'vendor/composer':
      case 'vendor/manual':
        $base_path = $this->cgmMediaserverHost . $module . '/';
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

    $mediaPath = Cogumelo::getSetupValue( 'mod:mediaserver:cachePath' );

    switch( $module ) {
      case false:
        $base_path = $this->cgmMediaserverHost.$mediaPath.'/';
        break;
      case 'vendor':
      case 'vendor/yarn':
      case 'vendor/bower':
      case 'vendor/composer':
      case 'vendor/manual':
        $base_path = $this->cgmMediaserverHost.$module.'/';
        break;
      default:
        $base_path = $this->cgmMediaserverHost.$mediaPath.'/module/'.$module.'/';
        break;
    }



    if( mb_substr($file_path, -5) == '.scss' ) {
      $scssCompiledExtension = '.css';
    }
    else {
      $scssCompiledExtension  = '';
    }

    $includeObj = array(
      'rel' => 'stylesheet',
      'type' => 'text/css',
      'src' => $base_path.$file_path.$scssCompiledExtension
    );
    $includeRef = $base_path.$file_path.$scssCompiledExtension;


    if( $is_autoinclude ) {
      $this->css_autoincludes[$includeRef] = $includeObj;
    }
    else {
      $this->css_includes[$includeRef] = $includeObj;
    }
  }

  /**
   * Crea el HTML que carga los Scripts
   *
   * @param bool $ignoreAutoincludes
   *
   * @return string $html
   **/
  public function getClientScriptHtml( $ignoreAutoincludes = false ) {
    $itemsToInclude = array();
    $html = '';

    $itemsToInclude = $this->getClientScriptArray( $ignoreAutoincludes );

    // generate the javascript include call
    $coma = '';
    foreach( $itemsToInclude as $include ) {

      $includeLog = $include;

      if( Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) === false ){
        $includeLog['url'] = '/'.Cogumelo::getSetupValue( 'mod:mediaserver:path' ).'/jsLog.js?ref='. $includeLog['url'];
        $html .= '  '.$coma.str_replace('\\/', '/', json_encode( $includeLog ) ) . "\n";
        $coma = ',';
      }

      $include['url'] .= '?'.$this->getAnticacheParameter(); // forzar caché do navegador para ese día

      $html .= '  '.$coma.str_replace('\\/', '/', json_encode( $include ) ) . "\n";
      $coma=',';
    }

    return( $html );
  }

  /**
   * Crea una lista con los Scripts a cargar
   *
   * @param bool $ignoreAutoincludes
   *
   * @return array $itemsToInclude
   **/
  public function getClientScriptArray( $ignoreAutoincludes = false ) {
    $itemsToInclude = array();

    if( !$ignoreAutoincludes ) {
      foreach( $this->js_autoincludes as $includeKey => $include ) {
        $itemsToInclude[ $includeKey ] = array( 'url'=> $include['src'], 'expire' => 1 );
      }
    }

    foreach( $this->js_includes as $includeKey => $include ) {
      $itemsToInclude[ $includeKey ] = array( 'url'=> $include['src'], 'skipCache' => 'true' );
    }

    // Carga recursivamente los estilos de los fragmentos
    foreach( $this->fragments as $fragmentName => $fragmentObjects ) {
      foreach( $fragmentObjects as $fragmentTemplate ) {
        $itemsToInclude = array_merge( $itemsToInclude, $fragmentTemplate->getClientScriptArray( $ignoreAutoincludes ) );
      }
    }

    return( $itemsToInclude );
  }



  /**
   * Crea el HTML que carga los Styles
   *
   * @param bool $ignoreAutoincludes
   *
   * @return string $html
   **/
  public function getClientStylesHtml( $ignoreAutoincludes = false ) {
    $itemsToInclude = array();
    $html = '';

    $itemsToInclude = array_merge( $itemsToInclude, $this->getClientStylesArray( $ignoreAutoincludes ) );

    // generate the javascript include call
    foreach( $itemsToInclude as $include ) {
      $html .=  $include   . "\n";
    }

    return( $html . "\n\n");
  }

  /**
   * Crea una lista con los Styles a cargar
   *
   * @param bool $ignoreAutoincludes
   *
   * @return array $itemsToInclude
   **/
  public function getClientStylesArray( $ignoreAutoincludes = false ) {
    $itemsToInclude = array();

    if( !$ignoreAutoincludes ) {
      foreach( $this->css_autoincludes as $includeKey => $include ) {
        //$itemsToInclude[$includeKey] = array('src'=> $include['src'], 'rel' => $include['rel'] , 'type'=> $include['type'] );
        $itemsToInclude[ $includeKey ] = "<link href='".$include['src'].'?'.$this->getAnticacheParameter()."' type='". $include['type'] ."' rel='". $include['rel'] ."' >";
      }
    }

    foreach( $this->css_includes as $includeKey => $include ) {
      //$itemsToInclude[$includeKey] = array('src'=> $include['src'], 'rel' => $include['rel'] , 'type'=> $include['type'] );
      $itemsToInclude[ $includeKey ] = "<link href='".$include['src'].'?'.$this->getAnticacheParameter()."' type='". $include['type'] ."' rel='". $include['rel'] ."' >";
    }

    // Carga recursivamente los estilos de los fragmentos
    foreach( $this->fragments as $fragmentName => $fragmentObjects ) {
      foreach( $fragmentObjects as $fragmentTemplate ) {
        $itemsToInclude = array_merge( $itemsToInclude, $fragmentTemplate->getClientStylesArray( $ignoreAutoincludes ) );
      }
    }

    return( $itemsToInclude );
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
      if( mb_strpos( $tplData, 'string:' ) === 0 || mb_strpos( $tplData, 'eval:' ) === 0 ) {
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

  /*
  public function __toString() {

    return( $this->exec( true ) );
  }
  */

  /**
   Crea el resultado Minimizado a partir de los datos y plantillas indicados
   *
   * @param bool $toString
   *
   * @return bool / string HTML generado
   **/
  public function execMinimify( $toString = false ) {
    $result = false;

    $htmlCode = $this->exec( true );

    $regexFrom = [
      '/\s+$/m',
      '/^\s+/m',
      '/(<\/?\w+>)\s+(<\/?\w+>)/',
      '/>\s+</',
    ];
    $regexTo = [
      ' ',
      ' ',
      '$1 $2',
      '> <',
    ];

    $result = preg_replace( $regexFrom, $regexTo, $htmlCode );

    if( !$toString ) {
      echo $result;
      $result = true;
    }

    return( $result );
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


      $mainClientIncludes = "\n";
      // Basic includes and includers
      $mainClientIncludes .= '<script src="'.$this->cgmMediaserverHost.'vendor/yarn/jquery/dist/jquery.min.js"></script>' . "\n";
      $mainClientIncludes .= '<script src="'.$this->cgmMediaserverHost.'vendor/yarn/popper.js/dist/umd/popper.min.js"></script>' . "\n";
      $mainClientIncludes .= '<script defer src="'.$this->cgmMediaserverHost.'vendor/yarn/bootstrap/dist/js/bootstrap.min.js"></script>' . "\n";
      //$clientIncludes .= '<script src="http://rsvpjs-builds.s3.amazonaws.com/rsvp-latest.min.js"></script>' . "\n";
      //$clientIncludes .= '<script src="http://addyosmani.com/basket.js/dist/basket.min.js"></script>' . "\n";
      $mainClientIncludes .= '<script src="'.$this->cgmMediaserverHost.'vendor/manual/rsvp/rsvp-3.2.1.min.js"></script>' . "\n";
      $mainClientIncludes .= '<script src="'.$this->cgmMediaserverHost.'vendor/manual/basket/basket-v0.5.2.min.js"></script>' . "\n";
      $mainClientIncludes .= '<script src="'.Cogumelo::getSetupValue( 'publicConf:vars:mediaJs' ).'/module/common/js/cogumeloLog.js"></script>' . "\n";
      $mainClientIncludes .= '<script src="'.$langUrl.'/media/jsConfConstants.js"></script>' . "\n";
      $mainClientIncludes .= '<script src="'.$langUrl.'/jsTranslations/getJson.js"></script>' . "\n";
    //  $mainClientIncludes .= $this->getClientStylesHtml();



      $clientIncludes = "\n";
      $clientIncludes .= "<script>\n";

      // LOCALSTORAGE CLEAR AFTER X MINUTES
      $clientIncludes .= "if(typeof Storage !== 'undefined') {\n";
      $clientIncludes .= "  var cogumeloLocalStorageLastUpdate = localStorage.getItem('cogumeloLocalStorageLastUpdate');\n";
      $clientIncludes .= "  var currentTimestamp = new Date().getTime();\n";
      $clientIncludes .= "  var localStorageMaxTime = ".  1000 * 60 * Cogumelo::getSetupValue( 'clientLocalStorage:lifetime' ) .";\n";

      $clientIncludes .= "  if( cogumeloLocalStorageLastUpdate ) {\n";
      $clientIncludes .= "    if( (currentTimestamp-cogumeloLocalStorageLastUpdate) > localStorageMaxTime ){\n";
      $clientIncludes .= "      localStorage.clear(); cogumelo.log('Cogumelo: Cleaning Localstorage data')\n";
      $clientIncludes .= "      localStorage.setItem('cogumeloLocalStorageLastUpdate', currentTimestamp );\n";
      $clientIncludes .= "    }\n";
      $clientIncludes .= "  }\n";
      $clientIncludes .= "  else { localStorage.setItem('cogumeloLocalStorageLastUpdate', currentTimestamp ); }\n";
      $clientIncludes .= "}\n";

      // AJAX PRESET
      $clientIncludes .= "".'$.ajaxPrefilter(function( options, originalOptions, jqXHR ) { options.async = true; });' . "\n";
      $clientIncludes .= '$.holdReady( true );'."\n";
      //if( !$this->cgmMediaserverCompileLess ) {
      if( Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) === false || ( Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) === true && Cogumelo::getSetupValue( 'mod:mediaserver:notCacheJs' ) === true ) ) {
        $clientIncludes .= 'basket.clear();'."\n";
      }
      $clientIncludes .= 'basket.require('. "\n";
      $clientIncludes .= $this->getClientScriptHtml() ;
      $clientIncludes .= ').then(function () { cogumelo.log(\'JS files already loaded\');$.holdReady( false ); });'."\n\n";
      $clientIncludes .= "</script>\n\n\n";

      // Hasta ahora era Script
      $clientIncludesScript = $clientIncludes;


      // Ahora vamos con Styles
      $clientIncludesStyles = "\n" . $this->getClientStylesHtml();

      // Mezclamos Script y Styles
      $clientIncludes .= $clientIncludesStyles;
      //$clientIncludes .= '<script src="/vendor/manual/sass.link/sass.link.src.js"></script>';


      $this->assign( 'client_includes_only_scripts', $clientIncludesScript );
      $this->assign( 'client_includes_only_styles', $clientIncludesStyles );
      $this->assign( 'client_includes', $clientIncludes );
      $this->assign( 'main_client_includes', $mainClientIncludes );


      foreach( $this->fragments as $fragmentName => $fragmentObjects ) {
        $htmlFragment = '';
        foreach( $fragmentObjects as $fragmentTemplate ) {
          $htmlFragment .= $fragmentTemplate->execFragment();
        }
        $this->assign( $fragmentName, $htmlFragment );
      }


      // $this->loadFilter( 'output', 'trimwhitespace' );


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
  public function execFragment() {


    // error_log( 'Template->execFragment() === ' . $this->tpl );

    $htmlCode = '';

    if( $this->tpl ) {
      // assign
      $clientIncludes = "\n";

      $clientIncludes .= "<script>\n";

      $clientIncludes .= "".'$.ajaxPrefilter(function( options, originalOptions, jqXHR ) { options.async = true; });' . "\n";


      $clientIncludes .= '$.holdReady( true );'."\n";
      //if( !$this->cgmMediaserverCompileLess ) {
      if( Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) === false || (Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) === true &&  Cogumelo::getSetupValue( 'mod:mediaserver:notCacheJs' ) == true ) ) {
        $clientIncludes .= 'basket.clear();'."\n";
      }
      $clientIncludes .= 'basket.require('. "\n";
      $clientIncludes .= $this->getClientScriptHtml() ;
      $clientIncludes .= ').then(function () { $.holdReady( false ); });'."\n\n";
      $clientIncludes .= "</script>\n\n\n";




      // Hasta ahora era Script
      $clientIncludesScript = $clientIncludes;


      // Ahora vamos con Styles
      $clientIncludesStyles =  '';


      $this->assign( 'client_includes_only_scripts', $clientIncludesScript );
      $this->assign( 'client_includes_only_styles', $clientIncludesStyles );



      $this->assign( 'client_includes',  $clientIncludes );

      foreach( $this->fragments as $fragmentName => $fragmentObjects ) {
        $htmlFragment = '';

        foreach( $fragmentObjects as $fragmentTemplate ) {
          $htmlFragment .= $fragmentTemplate->execFragment();
        }

        $this->assign( $fragmentName, $htmlFragment );
      }

      $htmlCode = $this->fetch( $this->tpl );
      Cogumelo::debug( 'Template class displays tpl '.$this->tpl );
    }
    else {
      Cogumelo::error( 'Template: no tpl file defined or not found: '.$this->tpl );
    }

    return( $htmlCode );
  }

  public function execBlock() {
    $this->execFragment();
  }


  function getAnticacheParameter() {


    if (Cogumelo::getSetupValue( 'mod:mediaserver:productionMode' ) === false ) {
      $param = md5(date("ymdGis"));
    }
    else {
      $param = md5( CACHE_FLUSH_TIMESTAMP );
    }

    return $param;
  }

}
