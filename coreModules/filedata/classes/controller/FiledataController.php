<?php


class FiledataController {

  /**
    Ruta a partir de la que se crean los directorios y ficheros subidos
  */
  const FILES_APP_PATH = MOD_FORM_FILES_APP_PATH;

  var $fileId = false;
  var $fileInfo = false;

  public function __construct( $fileId = false ) {
    error_log( 'FiledataController __construct: ' . $fileId );

    if( $fileId ) {
      $this->fileInfo = $this->loadFileInfo( $fileId );
      if( $this->fileInfo ) {
        $this->fileId = $fileId;
      }
    }
  }



  /**
    Load File info
  */
  public function loadFileInfo( $fileId ) {
    error_log( 'FiledataController: loadFileInfo(): ' . $fileId );

    if( $this->fileId !== $fileId || $this->fileInfo === false ) {
      $this->fileId = false;
      $this->fileInfo = false;

      $fileModel = new filedataModel();
      if( $fileList = $fileModel->listItems( array( 'filters' => array( 'id' => $fileId ) ) ) ) {
        if( $fileObj = $fileList->fetch() ) {
          $this->fileId = $fileId;
          $this->fileInfo = $fileObj->getAllData( 'onlydata' );
        }
      }
    }

    error_log( print_r( $this->fileInfo, true ) );
    return $this->fileInfo;
  } // function loadFileInfo()


  /**
    Creates a database FiledataModel register and save
  */
  public function saveFile( $originFile, $relativeDestPath , $fileName, $originFileIsRelative = true  ) {

    if( $originFileIsRelative ) {
      $absFrom = APP_BASE_PATH. $originFile;
    }
    else {
      $absFrom = $originFile;
    }

    //die($absFrom);
    filedata::load('model/FiledataModel.php');
    $fileDB = false;

    if( file_exists($absFrom) ) {

      $fileDB = new FiledataModel( array('originalName' => $fileName ) );

      $fileDB->save();

      //$secureFileName = $this->secureFileName( $fileName );

      $secureFileName = $fileName;

      if( file_exists( $relativeDestPath.'/'.$secureFileName ) ){
        $realDestName = $fileDB->getter('id') .$secureFileName;
      }
      else {
        $realDestName = $secureFileName;
      }

      if( !is_dir(MOD_FILEDATA_APP_PATH.$relativeDestPath) ) {
        mkdir(MOD_FILEDATA_APP_PATH.$relativeDestPath, 0755, true);
      }

      if( copy ( $absFrom, MOD_FILEDATA_APP_PATH.$relativeDestPath.'/'.$realDestName ) ) {
        $finfo = new finfo(FILEINFO_MIME, "/usr/share/misc/magic");
        $fileDB->setter('type', $finfo->file($absFrom) );
        //finfo_close($finfo);

        $fileDB->setter('size', filesize( $absFrom ) );
        $fileDB->setter('name', $realDestName );
        $fileDB->setter('absLocation', $relativeDestPath.'/'.$realDestName );

        $fileDB->save();
      }
      else {
        cogumelo::error( 'FiledataController cant copy the file to: '. MOD_FILEDATA_APP_PATH.$relativeDestPath.'/'.$realDestName);
        $fileDB->delete();
      }

    }
    else {
      cogumelo::error( 'FiledataController cant find the file path to save: '.$absFrom);
    }

    return $fileDB;
  } // function saveFile()


  /**
    Crea un nombre de fichero seguro a partir del nombre de fichero deseado
    @param string $fileName Nombre del campo
    @return string
   */
  public function secureFileName( $fileName ) {
    // error_log( 'secureFileName: '.$fileName );
    $maxLength = 200;

    $fileName = str_replace( $this->replaceAcents[ 'from' ], $this->replaceAcents[ 'to' ], $fileName );
    $fileName = preg_replace( '/[^0-9a-z_\.-]/i', '_', $fileName );

    $sobran = mb_strlen( $fileName, 'UTF-8' ) - $maxLength;
    if( $sobran < 0 ) {
      $sobran = 0;
    }

    $tmpExtPos = strrpos( $fileName, '.' );
    if( $tmpExtPos > 0 && ( $tmpExtPos - $sobran ) >= 8 ) {
      // Si hay extensión y al cortar el nombre quedan 8 o más letras, recorto solo el nombre
      $tmpName = substr( $fileName, 0, $tmpExtPos - $sobran );
      $tmpExt = substr( $fileName, 1 + $tmpExtPos );
      $fileName = $tmpName . '.' . $tmpExt;
    }
    else {
      // Recote por el final
      $fileName = substr( $fileName, 0, $maxLength );
    }

    // error_log( 'secureFileName RET: '.$fileName );

    return $fileName;
  } // function secureFileName()


} // FiledataController
