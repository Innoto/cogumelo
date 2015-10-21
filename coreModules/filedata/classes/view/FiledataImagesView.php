<?php
Cogumelo::load('coreView/View.php');
filedata::autoIncludes();
filedata::load('controller/FiledataImagesController.php');


class FiledataImagesView extends View {


  // Ruta a partir de la que se crean los directorios y ficheros subidos
  var $filesAppPath = MOD_FORM_FILES_APP_PATH;
  // Ruta a partir de la que se crean los directorios y ficheros procesados
  var $filesCachePath = MOD_FILEDATA_CACHE_PATH;
  // Ruta a partir de la que trabaja el servidor web
  var $webBasePath = WEB_BASE_PATH;


  public function __construct( $baseDir = false ){
    parent::__construct( $baseDir );

    filedata::autoIncludes();
  }

  /**
    Evaluate the access conditions and report if can continue
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

    error_log( 'FiledataWeb: showImg(): ' . print_r( $urlParams, true ) );

    if( isset( $urlParams[ 'fileId' ] ) ) {
      $imageCtrl = new FiledataImagesController( $urlParams[ 'fileId' ] );

      if( $imageCtrl->fileInfo ) {

        $imgInfo = array(
          'type' => $imageCtrl->fileInfo['type']
        );

        if( isset( $urlParams[ 'profile' ]  ) ) {
          $urlParams[ 'profile' ] = substr( strrchr( $urlParams[ 'profile' ], '/' ), 1 );
        }
        else {
          $urlParams[ 'profile' ] = '';
        }

        $imgInfo['route'] = $imageCtrl->getRouteProfile( $urlParams[ 'profile' ] );


        if( file_exists( $imgInfo['route'] ) && strpos( $imgInfo['route'], $this->filesCachePath ) === 0 ) {
          $urlRedirect = substr( $imgInfo['route'], strlen( $this->webBasePath ) );
          error_log( "FiledataWeb: showImg(): urlRedirect = $urlRedirect" );
          Cogumelo::redirect( SITE_HOST . $urlRedirect );
        }
        else {
          $clearName = '';
          if( isset( $urlParams[ 'fileName' ]  ) ) {
            $clearName = substr( strrchr( $urlParams[ 'fileName' ], '/' ), 1 );
          }
          $imgInfo['name'] = $clearName !== '' ? $clearName : $imageCtrl->fileInfo['originalName'];
          $imageCtrl->sendImage( $imgInfo );
          // XA NON SE PODEN MANDAR COSAS AO NAVEGADOR
        }
        // XA NON SE PODEN MANDAR COSAS AO NAVEGADOR
      }
      else {
        cogumelo::error( 'Imposible mostrar el elemento solicitado.1' );
      }
    }
    else {
      cogumelo::error( 'Imposible mostrar el elemento solicitado.2' );
    }

  } // function showImg()



} // class FiledataImagesView extends View

