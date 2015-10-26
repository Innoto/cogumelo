<?php


class FiledataImagesController {


  var $filedataCtrl = false;

  // Ruta a partir de la que se crean los directorios y ficheros subidos
  var $filesAppPath = MOD_FILEDATA_APP_PATH;
  // Ruta a partir de la que se crean los directorios y ficheros procesados
  var $filesCachePath = MOD_FILEDATA_CACHE_PATH;

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
    error_log( "FiledataImagesController: setProfile( $profile )" );

    global $IMAGE_PROFILES;

    if( $profile && isset( $IMAGE_PROFILES[ $profile ] ) ) {
      $conf = $IMAGE_PROFILES[ $profile ];

      $this->profile = array();
      $this->profile['idName'] = $profile;

      $this->profile['width'] = ( isset( $conf['width'] ) ) ? $conf['width'] : 0; // 0 by default
      $this->profile['height'] = ( isset( $conf['height'] ) ) ? $conf['height'] : 0; // 0 by default

      $this->profile['cut'] = ( isset( $conf['cut'] ) ) ? $conf['cut'] : true; // true by default
      $this->profile['enlarge'] = ( isset( $conf['enlarge'] ) ) ? $conf['enlarge'] : true; // true by default
      // $this->profile['no_cache'] = ( isset( $conf['no_cache'] ) ) ? $conf['no_cache'] : false; // false by default

      $this->profile['saveFormat'] = ( isset( $conf['saveFormat'] ) ) ? $conf['saveFormat'] : false;
      $this->profile['saveName'] = ( isset( $conf['saveName'] ) ) ? $conf['saveName'] : false;
      $this->profile['saveQuality'] = ( isset( $conf['saveQuality'] ) ) ? $conf['saveQuality'] : false;

      error_log( "FiledataImagesController: this->profile = $this->profile['idName']" );
    }
    else {
      $this->profile=false;
    }

    return $this->profile;
  }

  public function getRouteProfile( $profile ) {
    error_log( "FiledataImagesController: getRouteProfile( $profile )" );
    $imgRoute = false;
    $imgRouteOriginal = $this->filesAppPath . $this->fileInfo['absLocation'];

    if( $this->fileInfo ) {
      if( $this->setProfile( $profile ) ) {
        $imgRoute = $this->filesCachePath .'/'. $this->fileInfo['id'] .'/'.
          $this->profile['idName'] .'/'. $this->fileInfo['name'];
        error_log( "FiledataImagesController: getRouteProfile( $profile ): $imgRoute" );

        /**
         QUITAR !!!
        */
        unlink( $imgRoute );

        if( !file_exists( $imgRoute ) ) {
          $imgRoute = $this->createImageProfile( $imgRouteOriginal, $imgRoute );
        }
      }
      else {
        // Original
        $imgRoute = $this->filesCachePath .'/'. $this->fileInfo['id'] .'/'. $this->fileInfo['name'];
        error_log( "FiledataImagesController: getRouteProfile( NONE ): $imgRoute" );
        if( !file_exists( $imgRoute ) ) {
          $toRouteDir = pathinfo( $imgRoute, PATHINFO_DIRNAME );
          error_log( "toRouteDir = $toRouteDir" );
          if( !file_exists( $toRouteDir ) ) {
            error_log( "mkdir $toRouteDir" );
            mkdir ( $toRouteDir, 0770, true );
          }
          if( !copy( $imgRouteOriginal, $imgRoute ) ) {
            error_log( "FiledataImagesController: ERROR in copy( $imgRouteOriginal, $imgRoute )" );
          }
        }
      }
    }

    if( !$imgRoute || !file_exists( $imgRoute ) ) {
      $imgRoute = $imgRouteOriginal;
    }
    else {
      $imgRouteInfo = pathinfo( $imgRoute );
      $linkIdRoute = $imgRouteInfo['dirname'] .'/'. $this->fileInfo['id'] .'.'. $imgRouteInfo['extension'];
      if( !file_exists( $linkIdRoute ) ) {
        error_log( "symlink( $imgRoute, $linkIdRoute )" );
        symlink( $imgRoute, $linkIdRoute );
      }
    }
    error_log( "FiledataImagesController: getRouteProfile = $imgRoute" );
    return $imgRoute;
  }


  public function createImageProfile( $fromRoute, $toRoute ) {
    error_log( 'FiledataImagesController: createImageProfile(): ' );
    error_log( $fromRoute );
    error_log( $toRoute );

    $resultOK = true;

    if( $this->profile && file_exists( $fromRoute ) && !file_exists( $toRoute ) ) {
      $im = new Imagick();
      $imageFH = fopen( $fromRoute, 'rb' );

      $im->readimagefile( $imageFH );
      $im->trimImage( 0 );
      $im->setImagePage( 0, 0, 0, 0 );

      $imSize = $im->getImageGeometry();
      $x = $imSize['width'];
      $y = $imSize['height'];
      $tx = $this->profile['width'];
      $ty = $this->profile['height'];
      error_log( "Datos iniciales $x $y $tx $ty ---" );

      if( $tx !== 0 || $ty !== 0 ) {
        // Cambios en las medidas
        $escalar = false;
        if( $this->profile['enlarge'] || ( $tx<=$x && $ty<=$y ) ) {
          // Se ampliar ou xa e suficientemente grande
          $escalar = true;
          if( $this->profile['cut'] ) {
            // Escala para axustar un eixo e corta no outro
            if( $ty < intval( $tx*$y/$x ) ) {
              $ty = intval( $tx*$y/$x );
            }
            else {
              $tx = intval( $ty*$x/$y );
            }
          }
          else { // Escala para axustar un eixo e queda corto o outro
            if( $ty > intval( $tx*$y/$x ) ) {
              $ty = intval( $tx*$y/$x );
            }
            else {
              $tx = intval( $ty*$x/$y );
            }
          }
        }
        if( !( $this->profile['enlarge'] || ( $tx<=$x && $ty<=$y ) ) && ( $tx<$x || $ty<$y ) ) {
          // Se non ampliar e sobra solo por un lado
          if( $this->profile['cut'] ) {
            // Manten un eixo e corta no outro
            $escalar = false;
            if( $ty < $y ) {
              $tx = $x;
            }
            else {
              $ty = $y;
            }
            error_log( "Cortar sin ampliar: $x $y $tx $ty ---" );
          }
          else { // Reduce para axustar un eixo e queda corto o outro
            $escalar = true;
            if( $ty > intval( $tx*$y/$x ) ) {
              $ty = intval( $tx*$y/$x );
            }
            else {
              $tx = intval( $ty*$x/$y );
            }
          }
        }

        error_log( "Datos recalculados $x $y $tx $ty ---" );

        if( $escalar ) {

          error_log( "Valores para escalar: $x $y $tx $ty ---" );

          $im->scaleImage( $tx, $ty, false );

          $imSize = $im->getImageGeometry();
          $x = $imSize['width'];
          $y = $imSize['height'];
          $tx = $this->profile['width'];
          $ty = $this->profile['height'];

          error_log( "Xa escalado: $x $y $tx $ty ---" );
        }

        if( $tx < $x || $ty < $y ) {
          $px = intval( ($x-$tx)/2 );
          $py = intval( ($y-$ty)/2 );
          $im->cropImage( $tx, $ty, $px, $py );
          error_log( "Valores para cortar $x $y $tx $ty $px $py ---" );
        }
      }


      if( $this->profile['saveQuality'] ) {
        $im->setImageCompressionQuality( $this->profile['saveQuality'] );
      }


      // DEBUG info:
      $imSize = $im->getImageGeometry();
      $x = $imSize['width'];
      $y = $imSize['height'];
      $tx = $this->profile['width'];
      $ty = $this->profile['height'];
      error_log( "Datos finales $x $y $tx $ty ---" );


      $toRouteInfo = pathinfo( $toRoute );
      // [dirname]/[basename]
      // [dirname]/[filename].[extension]

      error_log( "toRouteInfo = " . print_r( $toRouteInfo, true ) );
      if( !file_exists( $toRouteInfo['dirname'] ) ) {
        error_log( 'mkdir '.$toRouteInfo['dirname'] );
        mkdir ( $toRouteInfo['dirname'], 0770, true );
      }

      if( is_writable( $toRouteInfo['dirname'] ) ) {

        if( $this->profile['saveFormat'] ) {
          $saveFormatExt = false;
          switch( $this->profile['saveFormat'] ) {
            case 'JPEG':
              $saveFormatExt = 'jpg';
              break;
            case 'PNG':
              $saveFormatExt = 'png';
              break;
          }
          if( $saveFormatExt ) {
            $im->setImageFormat( $this->profile['saveFormat'] );
            $toRoute = $toRouteInfo['dirname'] .'/'. $toRouteInfo['filename'] .'.'. $saveFormatExt;
          }
        }

        if( $this->profile['saveName'] ) {
          $toRoute = $toRouteInfo['dirname'] .'/'. $this->profile['saveName'];
        }

        //$im->setImageCompressionQuality(90);


        error_log( 'FiledataImagesController: createImageProfile: writeImage '.$toRoute );
        $im->writeImage( $toRoute );
      }
      else {
        $toRoute = false;
        cogumelo::error( "Imposible guardar la imagen en $toRoute" );
      }
      $im->clear();
      $im->destroy();
    }

    return $toRoute;
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

      ob_flush();
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