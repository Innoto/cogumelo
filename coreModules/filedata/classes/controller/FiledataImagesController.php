<?php


class FiledataImagesController {

  var $filedataCtrl = false;

  var $fileId = false;
  var $fileInfo = false;

  var $filePath = false;
  var $fileName = false;

  var $profile = false;


  public function __construct( $fileId = false ) {
    error_log( 'FiledataImagesController __construct: ' . $fileId );
    $filedataCtrl = new FiledataController( $fileId );
    $this->fileId = $filedataCtrl->fileId;
    $this->fileInfo = $filedataCtrl->fileInfo;
  }


  public function loadFileInfo( $fileId ) {
    error_log( 'FiledataImagesController: loadFileInfo(): ' . $fileId );

    if( $this->fileId !== $fileId || $this->fileInfo === false ) {
      $filedataCtrl->loadFileInfo( $fileId );
      $this->fileId = $filedataCtrl->fileId;
      $this->fileInfo = $filedataCtrl->fileInfo;
    }

    error_log( print_r( $this->fileInfo, true ) );
    return $this->fileInfo;
  }

  public function setProfile( $profile ) {
    global $IMAGE_PROFILES;
    $IMAGE_PROFILES = array(
      'alto' => array( 'width' => 400, 'height' => 200 ),
      'ancho' => array( 'width' => 200, 'height' => 400 )
    );

    if( $profile && isset( $IMAGE_PROFILES[ $profile ] ) ) {
      $conf = $IMAGE_PROFILES[ $profile ];

      $this->profile = array();
      $this->profile['width'] = $conf['width'];
      $this->profile['height'] = $conf['height'];
      $this->profile['cut'] = ( isset( $conf['cut'] ) ) ? $conf['cut'] : true; // true by default
      $this->profile['enlarge'] = ( isset( $conf['enlarge'] ) ) ? $conf['enlarge'] : true; // true by default
      $this->profile['no_cache'] = ( isset( $conf['no_cache'] ) ) ? $conf['no_cache'] : false; // false by default
    }
    else {
      $this->profile=false;
    }
  }


  public function sendImage( $imgInfo ) {
    $result = false;

    if( file_exists( $imgInfo['route'] ) ) {

      if( !isset( $imgInfo['type'] ) ) {
        $imgInfo['type'] = mime_content_type( $imgInfo['route'] );
      }

      if( !isset( $imgInfo['name'] ) ) {
        $imgInfo['name'] = substr( strrchr( $imgInfo['route'], '/' ), 1 );
      }

      // print headers
      header( 'Content-Disposition: inline; filename="' . $imgInfo['name'] . '"' );
      header( 'Content-Type: ' . $imgInfo['type'] );
      header( 'Content-Length: ' . filesize( $imgInfo['route'] ) );

      header( 'Expires: 0' );
      header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
      header( 'Pragma: public' );

      ob_clean();
      flush();

      // print image
      readfile( $imgInfo['route'] );
      $result = true;
    }
    else {
      cogumelo::error( 'Fichero no encontrado: ' . $imgInfo['route'] );
    }

    return $result;
  }

} // FiledataImagesController