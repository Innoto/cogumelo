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
    $fileName = false;
    if( isset( $urlParams['fileName'] ) && mb_strlen( $urlParams['fileName'] ) > 1 ) {
      $fileName = substr( strrchr( $urlParams['fileName'], '/' ), 1 );
    }
    $this->fileSendCommon( $urlParams['fileId'], $fileName, $this->filesAppPath, 'web' );
  } // function webFormFileShow()



  /**
    Descargamos un fichero de Form
  */
  public function webFormFileDownload( $urlParams ) {
    // error_log( 'FiledataWeb: webFormFileShow()' . $urlParams['fileId'] );
    $fileName = false;
    if( isset( $urlParams['fileName'] ) && mb_strlen( $urlParams['fileName'] ) > 1 ) {
      $fileName = substr( strrchr( $urlParams['fileName'], '/' ), 1 );
    }
    $this->fileSendCommon( $urlParams['fileId'], $fileName, $this->filesAppPath, 'download' );
  } // function webFormFileShow()



  /**
    Visualizamos el fichero
  */
  private function fileSendCommon( $fileId, $fileName, $basePath = false, $destination = 'web' ) {
    // error_log( "FiledataWeb: fileSendCommon( $fileId, $basePath, $destination )" );
    $fileInfo = false;

    $error = false;

    $disableRawUrl = Cogumelo::GetSetupValue( 'mod:filedata:disableRawUrl' );

    if( $fileId && ( $fileName || !$disableRawUrl ) ) {
      $fileInfo = $this->loadFileInfo( $fileId );
    }

    if( $fileInfo ) {
      if( !$disableRawUrl || $fileName === $fileInfo['name'] ) {
        if( $fileInfo['validatedAccess'] ) {
          switch( $destination ) {
            case 'download':
              if( !$this->webDownloadFile( $fileInfo, $basePath ) ) {
                $error = 1;
              }
              break;
            default:
              if( !$this->webShowFile( $fileInfo, $basePath ) ) {
                $error = 2;
              }
              break;
          }
        }
        else {
          $error = 5;
        }
      }
      else {
        $error = 3;
      }
    }
    else {
      $error = 4;
    }

    if( $error ) {
      header('HTTP/1.0 404 Not Found');
      cogumelo::error( 'Imposible cargar el elemento solicitado. ('.$error.')' );
    }
  } // function fileSendCommon()


  public function validateAccess( $fileInfo ) {
    $validated = false;

    if( isset( $fileInfo['privateMode'] ) && $fileInfo['privateMode'] > 0 ) {
      if( isset( $fileInfo['user'] ) && $fileInfo['user'] !== null ) {
        error_log( 'Verificando usuario logueado para acceder a fichero...' );

        $useraccesscontrol = new UserAccessController();
        $user = $useraccesscontrol->getSessiondata();
        if( $user && $user['data']['active'] ) {
          unset( $user['data']['password'] );
          error_log( 'USER: '.json_encode( $user ) );
          if( $user['data']['id'] === $fileInfo['user'] ) {
            // El fichero es del usuario actual
            error_log( 'Verificado por ID' );
            $validated = true;
          }
          else {
            $validRoles = [ 'filedata:privateAccess' ];
            if( $useraccesscontrol->checkPermissions( $validRoles, 'admin:full' ) ) {
              // Permiso de acceso a todos los ficheros
              error_log( 'Verificado por Rol' );
              $validated = true;
            }
          }
        }
      }
    }
    else {
      $validated = true;
    }

    return $validated;
  }


  /**
    Load File info
  */
  public function loadFileInfo( $fileId ) {
    error_log( 'FiledataWeb: loadFileInfo(): ' . $fileId );

    $fileInfo = false;

    $fileModel = new filedataModel();
    $fileList = $fileModel->listItems( array( 'filters' => array( 'id' => $fileId ) ) );
    $fileObj = ( gettype( $fileList ) === 'object' ) ? $fileList->fetch() : false;
    $fileInfo = ( gettype( $fileObj ) === 'object' ) ? $fileObj->getAllData('onlydata') : false;

    if( $fileInfo ) {
      $fileInfo['validatedAccess'] = $this->validateAccess( $fileInfo );
    }

    return $fileInfo;
  } // function loadFileInfo()


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
