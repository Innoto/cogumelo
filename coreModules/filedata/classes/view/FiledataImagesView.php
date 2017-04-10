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

    $this->filesAppPath = Cogumelo::getSetupValue( 'mod:filedata:filePath' );
    $this->filesCachePath = Cogumelo::getSetupValue( 'mod:filedata:cachePath' );
    $this->disableRawUrlProfile = Cogumelo::GetSetupValue( 'mod:filedata:disableRawUrlProfile' );
    $this->webBasePath = Cogumelo::getSetupValue( 'setup:webBasePath' );
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
    $error = false;

    $fileName = isset( $urlParams['fileName'] ) ? substr( strrchr( $urlParams['fileName'], '/' ), 1 ) : false;

    if( isset( $urlParams['fileId'] ) && ( $fileName || !$this->disableRawUrlProfile ) ) {
      $imageCtrl = new FiledataImagesController( $urlParams['fileId'] );
      $fileInfo = $imageCtrl->fileInfo;

      if( $fileInfo ) {
        if( !$this->disableRawUrlProfile || $fileName === $fileInfo['name'] ) {
          if( $fileInfo['validatedAccess'] ) {
            $imgInfo = array(
              'type' => $fileInfo['type']
            );

            if( isset( $urlParams['profile']  ) ) {
              $urlParams['profile'] = substr( strrchr( $urlParams['profile'], '/' ), 1 );
            }
            else {
              $urlParams['profile'] = '';
            }

            $imgInfo['route'] = $imageCtrl->getRouteProfile( $urlParams['profile'] );

            if( !empty( $fileInfo['privateMode'] ) ) {
              $imageCtrl->profile['cache'] = false;
            }

            if( $imageCtrl->profile['cache'] && file_exists( $imgInfo['route'] ) && strpos( $imgInfo['route'], $this->filesCachePath ) === 0 ) {
              $urlRedirect = substr( $imgInfo['route'], strlen( $this->webBasePath ) );
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
          $error = 'NN';
        }
      }
      else {
        $error = 'NL';
      }
    }
    else {
      $error = 'NI';
    }

    if( $error ) {
      header('HTTP/1.0 404 Not Found');
      cogumelo::error( 'showImg: Imposible mostrar el elemento solicitado. ('.$error.')' );
    }
  } // function showImg()



} // class FiledataImagesView extends View

