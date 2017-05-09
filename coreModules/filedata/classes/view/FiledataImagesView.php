<?php
Cogumelo::load('coreView/View.php');
filedata::autoIncludes();
filedata::load('controller/FiledataImagesController.php');


class FiledataImagesView extends View {


  // Ruta a partir de la que se crean los directorios y ficheros subidos
  var $filesAppPath = false;
  // Ruta a partir de la que se crean los directorios y ficheros procesados
  var $filesCachePath = false;
  // Ruta a partir de la que trabaja el servidor web
  var $webBasePath = false;


  public function __construct( $baseDir = false ){
    parent::__construct();

    filedata::autoIncludes();

    $this->webBasePath = Cogumelo::getSetupValue( 'setup:webBasePath' );

    $this->filesAppPath = Cogumelo::getSetupValue( 'mod:filedata:filePath' );
    $this->filesCachePath = Cogumelo::getSetupValue( 'mod:filedata:cachePath' );

    $this->verifyAKeyUrl = Cogumelo::GetSetupValue( 'mod:filedata:verifyAKeyUrl' );
    $this->disableRawUrlProfile = Cogumelo::GetSetupValue( 'mod:filedata:disableRawUrlProfile' );
  }

  /**
   * Evaluate the access conditions and report if can continue
   *
   * @return bool : true -> Access allowed
   **/
  public function accessCheck() {

    return true;
  }



  /**
    Mostramos una imagen de Form
  */
  public function showImg( $urlParams = false ) {
    // error_log( 'FiledataImagesView: showImg(): ' . print_r( $urlParams, true ) );
    $fileInfo = false;
    $error = false;

    $fileId = $urlParams['fileId'];
    $aKey = empty( $urlParams['aKey'] ) ? false : $urlParams['aKey'];
    $fileName = empty( $urlParams['fileName'] ) ? false : mb_substr( mb_strrchr( $urlParams['fileName'], '/' ), 1 );

    if( $fileId && ( $fileName || !$this->disableRawUrlProfile ) && ( $aKey || !$this->verifyAKeyUrl ) ) {
      $imageCtrl = new FiledataImagesController( $fileId );
      $fileInfo = $imageCtrl->fileInfo;
    }



    if( $fileInfo ) {
      if( !$this->disableRawUrlProfile || $fileName === $fileInfo['name'] ) {
        if( !$this->verifyAKeyUrl || $aKey === $fileInfo['aKey'] ) {
          if( $fileInfo['validatedAccess'] ) {
            $imgInfo = [ 'type' => $fileInfo['type'] ];

            if( isset( $urlParams['profile']  ) ) {
              $urlParams['profile'] = mb_substr( mb_strrchr( $urlParams['profile'], '/' ), 1 );
            }
            else {
              $urlParams['profile'] = '';
            }

            $imgInfo['route'] = $imageCtrl->getRouteProfile( $urlParams['profile'] );

            if( !empty( $fileInfo['privateMode'] ) ) {
              $imageCtrl->profile['cache'] = false;
            }

            if( $imageCtrl->profile['cache'] && file_exists( $imgInfo['route'] ) && mb_strpos( $imgInfo['route'], $this->filesCachePath ) === 0 ) {
              $urlRedirect = mb_substr( $imgInfo['route'], mb_strlen( $this->webBasePath ) );
              // error_log( "FiledataImagesView: showImg(): urlRedirect = $urlRedirect" );
              Cogumelo::redirect( SITE_HOST . $urlRedirect );
              // YA NO SE PUEDE ENVIAR NADA AL NAVEGADOR
            }
            else {
              $imgInfo['name'] = !empty( $fileName ) ? $fileName : $fileInfo['name'];
              // $imgInfo['name'] = !empty( $fileName ) ? $fileName : $fileInfo['originalName'];
              if( !$imageCtrl->sendImage( $imgInfo ) ) {
                $error = 'NS';
              }
              // ELSE: YA NO SE PUEDE ENVIAR NADA AL NAVEGADOR
            }
            // YA NO SE PUEDE ENVIAR NADA AL NAVEGADOR
          }
          else {
            $error = 'NV';
          }
        }
        else {
          $error = 'NK';
        }
      }
      else {
        $error = 'NN';
      }
    }
    else {
      $error = 'NL';
    }

    if( $error ) {
      header('HTTP/1.0 404 Not Found');
      cogumelo::error( 'showImg: Imposible mostrar el elemento solicitado. ('.$error.')' );
    }
  } // function showImg()



} // class FiledataImagesView extends View

