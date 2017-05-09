<?php
Cogumelo::load('coreView/View.php');



class FiledataWeb extends View {

  // Ruta a partir de la que se crean los directorios y ficheros subidos
  var $filesAppPath = false;


  public function __construct( $baseDir = false ){
    parent::__construct( $baseDir );

    filedata::autoIncludes();

    $this->filesAppPath = Cogumelo::getSetupValue( 'mod:filedata:filePath' );
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
    Visualizamos un fichero de Form
  */
  public function webFormFileShow( $urlParams ) {
    // error_log( 'FiledataWeb: webFormFileShow()' . $urlParams['fileId'] );

    $aKey = empty( $urlParams['aKey'] ) ? false : $urlParams['aKey'];
    $fileName = empty( $urlParams['fileName'] ) ? false : mb_substr( mb_strrchr( $urlParams['fileName'], '/' ), 1 );

    $this->fileSendCommon( $urlParams['fileId'], $fileName, $aKey, $this->filesAppPath, 'web' );
  } // function webFormFileShow()



  /**
    Descargamos un fichero de Form
  */
  public function webFormFileDownload( $urlParams ) {
    // error_log( 'FiledataWeb: webFormFileShow()' . $urlParams['fileId'] );

    $aKey = empty( $urlParams['aKey'] ) ? false : $urlParams['aKey'];
    $fileName = empty( $urlParams['fileName'] ) ? false : mb_substr( mb_strrchr( $urlParams['fileName'], '/' ), 1 );

    $this->fileSendCommon( $urlParams['fileId'], $fileName, $aKey, $this->filesAppPath, 'download' );
  } // function webFormFileShow()



  /**
    Visualizamos el fichero
  */
  private function fileSendCommon( $fileId, $fileName, $aKey, $basePath, $destination = 'web' ) {
    // error_log( "FiledataWeb: fileSendCommon( $fileId, $basePath, $destination )" );
    $fileInfo = false;
    $error = false;

    $verifyAKeyUrl = Cogumelo::GetSetupValue( 'mod:filedata:verifyAKeyUrl' );
    $disableRawUrl = Cogumelo::GetSetupValue( 'mod:filedata:disableRawUrl' );


    if( $fileId && ( $fileName || !$disableRawUrl ) && ( $aKey || !$verifyAKeyUrl ) ) {
      filedata::load('controller/FiledataController.php');
      $filedataCtrl = new FiledataController();
      $fileInfo = $filedataCtrl->loadFileInfo( $fileId );
    }

    if( $fileInfo ) {
      if( !$disableRawUrl || $fileName === $fileInfo['name'] ) {
        if( !$verifyAKeyUrl || $aKey === $fileInfo['aKey'] ) {
          if( $fileInfo['validatedAccess'] ) {
            switch( $destination ) {
              case 'download':
                if( !$this->webDownloadFile( $fileInfo, $basePath ) ) {
                  $error = 'ND';
                }
                break;
              default:
                if( !$this->webShowFile( $fileInfo, $basePath ) ) {
                  $error = 'NS';
                }
                break;
            }
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
      cogumelo::error( 'fileSend: Imposible cargar el elemento solicitado. ('.$error.')' );
    }
  } // function fileSendCommon()



  public function webFormPublic( $name ) {
    //var_dump($name[1]);
    $n = str_replace(
      '../',
      '',
      urldecode(
        urldecode($name[1]) // elfinder codifica doble
      )
    );

    $fileInfo = [
      'type' => 'image',
      'originalName' => $n,
      'absLocation' => $n
    ];

    if( !$this->webShowFile( $fileInfo , cogumeloGetSetupValue( 'mod:filedata:filePathPublic').'/' ) ) {
      cogumelo::error( 'Imposible mostrar el elemento solicitado: '.$n );
    }
  }


  /**
    Visualizamos el fichero en la web
  */
  private function webShowFile( $fileInfo, $basePath = '' ) {
    // error_log( 'FiledataWeb: webShowFile() ' . print_r( $fileInfo, true ) );

    $filePath = $basePath . $fileInfo['absLocation'];

    // error_log( 'FiledataWeb: filePath = ' . $filePath );
    // error_log( 'FiledataWeb: filesize = ' . filesize( $filePath ) );

    if( file_exists( $filePath ) ) {
      //header( 'Content-Description: File Transfer' );
      //header( 'Content-Disposition: attachment; filename=' . basename($filePath) );
      //header( 'Expires: 0');
      //header( 'Cache-Control: must-revalidate');
      //header( 'Pragma: public');
      //header( 'Content-Length: ' . $fileInfo['size'] );
      //header( 'Content-Length: ' . filesize( $filePath ) );
      header( 'Content-Disposition: inline; filename="' . $fileInfo['originalName'] . '"' );
      header( 'Content-Type: '. $fileInfo['type'] );
      readfile( $filePath );
      exit;
    }
    else {
      return false;
    }
  } // function webShowFile()


  /**
    Descargamos el fichero
  */
  private function webDownloadFile( $fileInfo, $basePath = '' ) {
    // error_log( 'FiledataWeb: webDownloadFile() ' . print_r( $fileInfo, true ) );

    $filePath = $basePath . $fileInfo['absLocation'];

    if( file_exists( $filePath ) ) {
      header( 'Content-Description: File Transfer' );
      header( 'Content-Type: application/octet-stream' );
      header( 'Content-Disposition: attachment; filename=' . basename($filePath) );
      header( 'Expires: 0');
      header( 'Cache-Control: must-revalidate');
      header( 'Pragma: public');
      header( 'Content-Length: ' . filesize($filePath));
      readfile( $filePath );
      exit;
    }
    else {
      return false;
    }
  } // function webDownloadFile()


} // class FiledataWeb extends View
