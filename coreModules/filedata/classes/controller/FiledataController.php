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



} // FiledataController