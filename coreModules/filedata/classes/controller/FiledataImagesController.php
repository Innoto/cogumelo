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
    //error_log( 'FiledataImagesController __construct: ' . $fileId );

    $filedataCtrl = new FiledataController( $fileId );
    $this->fileId = $filedataCtrl->fileId;
    $this->fileInfo = $filedataCtrl->fileInfo;
  }


  public function loadFileInfo( $fileId ) {
    //error_log( 'FiledataImagesController: loadFileInfo(): ' . $fileId );

    if( $this->fileId !== $fileId || $this->fileInfo === false ) {
      $filedataCtrl->loadFileInfo( $fileId );
      $this->fileId = $filedataCtrl->fileId;
      $this->fileInfo = $filedataCtrl->fileInfo;
    }

    //error_log( print_r( $this->fileInfo, true ) );
    return $this->fileInfo;
  }

  public function setProfile( $profile ) {
    //error_log( "FiledataImagesController: setProfile( $profile )" );

    global $IMAGE_PROFILES;

    if( $profile && isset( $IMAGE_PROFILES[ $profile ] ) ) {
      $conf = $IMAGE_PROFILES[ $profile ];

      //$this->profile = array();
      $this->profile = $IMAGE_PROFILES[ $profile ];
      $this->profile['idName'] = $profile;

      $this->profile['width'] = ( isset( $conf['width'] ) ) ? $conf['width'] : 0; // 0 by default
      $this->profile['height'] = ( isset( $conf['height'] ) ) ? $conf['height'] : 0; // 0 by default

      $this->profile['cut'] = ( isset( $conf['cut'] ) ) ? $conf['cut'] : true; // true by default
      $this->profile['enlarge'] = ( isset( $conf['enlarge'] ) ) ? $conf['enlarge'] : true; // true by default

      $this->profile['saveFormat'] = ( isset( $conf['saveFormat'] ) ) ? $conf['saveFormat'] : false;
      $this->profile['saveName'] = ( isset( $conf['saveName'] ) ) ? $conf['saveName'] : false;
      $this->profile['saveQuality'] = ( isset( $conf['saveQuality'] ) ) ? $conf['saveQuality'] : false;

      $this->profile['cache'] = ( isset( $conf['cache'] ) ) ? $conf['cache'] : true; // true by default

      //error_log( 'FiledataImagesController: this->profile = '.$this->profile['idName'] );
    }
    else {
      $this->profile=false;
    }

    return $this->profile;
  }

  public function getRouteProfile( $profile ) {
    //error_log( "FiledataImagesController: getRouteProfile( $profile )" );
    $imgRoute = false;
    $imgRouteOriginal = $this->filesAppPath . $this->fileInfo['absLocation'];

    if( $this->fileInfo ) {
      if( $this->setProfile( $profile ) ) {
        $imgRoute = $this->filesCachePath .'/'. $this->fileInfo['id'] .'/'.
          $this->profile['idName'] .'/'. $this->fileInfo['name'];
        //error_log( "FiledataImagesController: getRouteProfile( $profile ): $imgRoute" );

        if( !$this->profile['cache'] || !file_exists( $imgRoute ) ) {
          if( file_exists( $imgRoute ) ) {
            unlink( $imgRoute );
          }
          $imgRoute = $this->createImageProfile( $imgRouteOriginal, $imgRoute );
        }
      }
      else {
        // Original
        $imgRoute = $this->filesCachePath .'/'. $this->fileInfo['id'] .'/'. $this->fileInfo['name'];
        //error_log( "FiledataImagesController: getRouteProfile( NONE ): $imgRoute" );
        if( $this->profile['cache'] && !file_exists( $imgRoute ) ) {
          $toRouteDir = pathinfo( $imgRoute, PATHINFO_DIRNAME );
          //error_log( "toRouteDir = $toRouteDir" );
          if( !file_exists( $toRouteDir ) ) {
            //error_log( "mkdir $toRouteDir" );
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
      if( $this->profile['cache'] && !file_exists( $linkIdRoute ) ) {
        //error_log( "symlink( $imgRoute, $linkIdRoute )" );
        symlink( $imgRoute, $linkIdRoute );
      }
    }
    //error_log( "FiledataImagesController: getRouteProfile = $imgRoute" );
    return $imgRoute;
  }


  public function createImageProfile( $fromRoute, $toRoute ) {
    error_log( '---' );error_log( '---' );error_log( '---' );
    error_log( 'FiledataImagesController: createImageProfile(): ' );
    error_log( $fromRoute );
    error_log( 'mime_content_type: '.mime_content_type( $fromRoute ) );
    error_log( $toRoute );

    $resultOK = true;

    if( $this->profile && file_exists( $fromRoute ) && !file_exists( $toRoute ) ) {
      $im = new Imagick();
      $mimeType = mime_content_type( $fromRoute );

      if( isset( $this->profile['backgroundColor'] ) ) {
        $im->setBackgroundColor( new ImagickPixel( $this->profile['backgroundColor'] ) );
      }
      else {
        $im->setBackgroundColor( new ImagickPixel( 'transparent' ) );
      }


      if( strpos( $mimeType, 'image/svg' ) === 0 ) {
        // Imagenes SVG
        $svg = file_get_contents( $fromRoute );

        // Machaco el tamaño de lienzo
        $svg = preg_replace( '/(width|height)="(.*?)"/', '${1}="128px"', $svg );
        //error_log( 'SVG: '.$svg );

        if( isset( $this->profile['rasterResolution'] ) ) {
          // Para dar calidad en la carga de formatos raster
          $im->setResolution( $this->profile['rasterResolution']['x'], $this->profile['rasterResolution']['y'] );
        }
        else {
          // Buscamos que la conversión a px iguale o supere el tamaño solicitado
          $imSvg = new Imagick();
          $density = 120;
          $imSvg->setResolution( $density, $density );
          $imSvg->readImageBlob( $svg );
          $imSvg->trimImage( 0 ); // Recorta deixando so a imaxe
          $imSvg->setImagePage( 0, 0, 0, 0 ); // Reset del tamaño de lienzo
          $imSvgSize = $imSvg->getImageGeometry();
          $x = $imSvgSize['width'];
          $y = $imSvgSize['height'];
          $tx = $this->profile['width'];
          $ty = $this->profile['height'];
          error_log( "SVG iniciales $x $y $tx $ty $density ---" );
          if( $this->profile['cut'] ) {
            // Escala para axustar un eixo e corta no outro
            if( $ty < intval( $tx*$y/$x ) ) {
              $ty = intval( $tx*$y/$x );
              $density = 1 + intval( $density * $ty / $y );
            }
            else {
              $tx = intval( $ty*$x/$y );
              $density = 1 + intval( $density * $tx / $x );
            }
          }
          else { // Escala para axustar un eixo e queda corto o outro
            if( $ty > intval( $tx*$y/$x ) ) {
              $ty = intval( $tx*$y/$x );
              $density = 1 + intval( $density * $ty / $y );
            }
            else {
              $tx = intval( $ty*$x/$y );
              $density = 1 + intval( $density * $tx / $x );
            }
          }
          error_log( "SVG finales $x $y $tx $ty $density ---" );
          $imSvg->clear();
          $imSvg->destroy();

          $im->setResolution( $density, $density );
        }


        if( isset( $this->profile['rasterColor'] ) ) {
          // Para dar color en la carga de formatos raster
          $svg = preg_replace( '/(style="fill:)(#[0-9a-f]+)(")/', '${1}'.$this->profile['rasterColor'].'${3}', $svg );
        }

        //error_log( 'SVG: '.$svg );
        $im->readImageBlob( $svg );
        $im->trimImage( 0 ); // Recorta deixando so a imaxe
        $im->setImagePage( 0, 0, 0, 0 ); // Reset del tamaño de lienzo
      }
      else {
        // Imagenes no SVG
        $im->readimagefile( fopen( $fromRoute, 'rb' ) );
        // $im->trimImage( 0 ); // Recorta deixando so a imaxe
        $im->setImagePage( 0, 0, 0, 0 ); // Reset del tamaño de lienzo
      }


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
      $dbSize = $im->getImageGeometry();
      error_log( 'Datos finales '.$dbSize['width'].' '.$dbSize['height'].' '.$this->profile['width'].' '.$this->profile['height'].' ---' );


      $toRouteInfo = pathinfo( $toRoute );
      // [dirname]/[basename]
      // [dirname]/[filename].[extension]

      //error_log( "toRouteInfo = " . print_r( $toRouteInfo, true ) );
      if( !file_exists( $toRouteInfo['dirname'] ) ) {
        //error_log( 'mkdir '.$toRouteInfo['dirname'] );
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


        //error_log( 'FiledataImagesController: createImageProfile: writeImage '.$toRoute );
        $im->writeImage( $toRoute );
      }
      else {
        $toRoute = false;
        cogumelo::error( "Imposible guardar la imagen en $toRoute" );
      }
      $im->clear();
      $im->destroy();
    }

    error_log( '---' );error_log( '---' );error_log( '---' );
    return $toRoute;
  }


  public function sendImage( $imgInfo ) {
    error_log( 'FiledataImagesController: sendImage '. print_r( $imgInfo, true ) );

    $result = false;

    if( file_exists( $imgInfo['route'] ) ) {

      /*
      if( !isset( $imgInfo['type'] ) ) {
        $imgInfo['type'] = mime_content_type( $imgInfo['route'] );
      }
      */
      $imgInfo['type'] = mime_content_type( $imgInfo['route'] );

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

      // TODO: Revisar
      if( isset( $this->profile['cache'] ) && !$this->profile['cache'] ) {
        unlink( $imgInfo['route'] );
      }

      $result = true;
    }
    else {
      cogumelo::error( 'Fichero no encontrado: ' . $imgInfo['route'] );
    }

    return $result;
  }

} // FiledataImagesController