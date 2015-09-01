<?php
Cogumelo::load('coreView/View.php');



class FiledataWeb extends View {

  /**
    Ruta a partir de la que se crean los directorios y ficheros subidos
  */
  const FILES_APP_PATH = MOD_FORM_FILES_APP_PATH;


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
    Visualizamos un fichero
  */
  public function webFileShow( $urlParams ) {
    // error_log( 'FiledataWeb: webFileShow()' . $urlParams['1'] );
    $this->fileSendCommon( $urlParams['1'], self::FILES_APP_PATH, 'web' );
  } // function webFileShow()



  /**
    Visualizamos un fichero de Form
  */
  public function webFormFileShow( $urlParams ) {
    // error_log( 'FiledataWeb: webFormFileShow()' . $urlParams['1'] );
    $this->fileSendCommon( $urlParams['1'], self::FILES_APP_PATH, 'web' );
  } // function webFormFileShow()



  /**
    Descargamos un fichero de Form
  */
  public function webFormFileDownload( $urlParams ) {
    // error_log( 'FiledataWeb: webFormFileShow()' . $urlParams['1'] );
    $this->fileSendCommon( $urlParams['1'], self::FILES_APP_PATH, 'download' );
  } // function webFormFileShow()



  /**
    Visualizamos el fichero
  */
  public function fileSendCommon( $fileId, $basePath = false, $destination = 'web' ) {
    // error_log( "FiledataWeb: fileSendCommon( $fileId, $basePath, $destination )" );

    $fileInfo = $this->loadFileInfo( $fileId );

    if( $fileInfo ) {
      switch( $destination ) {
        case 'download':
          if( !$this->webDownloadFile( $fileInfo, $basePath ) ) {
            cogumelo::error( 'Imposible enviar el elemento solicitado.' );
          }
          break;
        default:
          if( !$this->webShowFile( $fileInfo, $basePath ) ) {
            cogumelo::error( 'Imposible mostrar el elemento solicitado.' );
          }
          break;
      }
    }
    else {
      cogumelo::error( 'Imposible cargar el elemento solicitado.' );
    }
  } // function fileSendCommon()



  /**
    Load File info
  */
  public function loadFileInfo( $fileId ) {
    // error_log( 'FiledataWeb: loadFileInfo(): ' . $fileId );

    $fileInfo = false;

    $fileModel = new filedataModel();
    $fileList = $fileModel->listItems( array( 'filters' => array( 'id' => $fileId ) ) );
    $fileObj = $fileList->fetch();

    if( $fileObj ) {
      $allData = $fileObj->getAllData();
      $fileInfo = $allData['data'];
    }

    return $fileInfo;
  } // function loadFileInfo()


  /**
    Visualizamos el fichero en la web
  */
  public function webShowFile( $fileInfo, $basePath = '' ) {
    // error_log( 'FiledataWeb: webShowFile() ' . print_r( $fileInfo, true ) );

    $filePath = $basePath . $fileInfo['absLocation'];

    error_log( 'FiledataWeb: filePath = ' . $filePath );
    // error_log( 'FiledataWeb: filesize = ' . filesize( $filePath ) );

    if( file_exists( $filePath ) ) {
      //header( 'Content-Description: File Transfer' );
      //header( 'Content-Disposition: attachment; filename=' . basename($filePath) );
      //header( 'Expires: 0');
      //header( 'Cache-Control: must-revalidate');
      //header( 'Pragma: public');
      //header( 'Content-Length: ' . $fileInfo['size'] );
      header( 'Content-Disposition: inline; filename="' . $fileInfo['originalName'] . '"' );
      //header( 'Content-Length: ' . filesize( $filePath ) );
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
  public function webDownloadFile( $fileInfo, $basePath = '' ) {
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

