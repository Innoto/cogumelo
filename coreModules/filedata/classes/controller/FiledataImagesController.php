<?php


/**
 * PHPMD: Suppress all warnings from these rules.
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class FiledataImagesController {


  var $filedataCtrl = false;

  // Ruta a partir de la que se crean los directorios y ficheros subidos
  var $filesAppPath = false;
  // Ruta a partir de la que se crean los directorios y ficheros procesados
  var $filesCachePath = false;

  var $fileId = false;
  var $fileInfo = false;

  var $filePath = false;
  var $fileName = false;

  var $profile = false;


  public function __construct( $fileId = false ) {
    // error_log( 'FiledataImagesController __construct: ' . $fileId );

    filedata::load('controller/FiledataController.php');
    $this->filedataCtrl = new FiledataController();
    $this->fileInfo = !empty( $fileId ) ? $this->filedataCtrl->loadFileInfo( $fileId ) : false;
    $this->fileId = !empty( $this->fileInfo ) ? $this->fileInfo['id'] : false;

    $this->filesAppPath = Cogumelo::getSetupValue( 'mod:filedata:filePath' );
    $this->filesCachePath = Cogumelo::getSetupValue( 'mod:filedata:cachePath' );

    $this->verifyAKeyUrl = Cogumelo::getSetupValue( 'mod:filedata:verifyAKeyUrl' );
    $this->disableRawUrlProfile = Cogumelo::getSetupValue( 'mod:filedata:disableRawUrlProfile' );
  }


  public function setProfile( $profile ) {
    // error_log( "FiledataImagesController: setProfile( $profile )" );

    $conf = Cogumelo::getSetupValue( 'mod:filedata:profile:'.$profile );

    if( $conf ) {
      $this->profile = $conf;
      $this->profile['idName'] = $profile;

      $this->profile['width'] = ( isset( $conf['width'] ) ) ? $conf['width'] : 0; // 0 by default
      $this->profile['height'] = ( isset( $conf['height'] ) ) ? $conf['height'] : 0; // 0 by default

      $this->profile['cut'] = ( isset( $conf['cut'] ) ) ? $conf['cut'] : true; // true by default
      $this->profile['enlarge'] = ( isset( $conf['enlarge'] ) ) ? $conf['enlarge'] : true; // true by default

      $this->profile['backgroundColor'] = ( isset( $conf['backgroundColor'] ) ) ? $conf['backgroundColor'] : 'transparent'; // 'transparent' by default

      $this->profile['saveFormat'] = ( isset( $conf['saveFormat'] ) ) ? $conf['saveFormat'] : false;
      $this->profile['saveName'] = ( isset( $conf['saveName'] ) ) ? $conf['saveName'] : false;
      $this->profile['saveQuality'] = ( isset( $conf['saveQuality'] ) ) ? $conf['saveQuality'] : false;

      $this->profile['cache'] = ( isset( $conf['cache'] ) ) ? $conf['cache'] : true; // true by default

      if( !empty( $this->fileInfo['privateMode'] ) ) {
        $this->profile['cache'] = false;
      }

      if( isset( $this->profile['padding'] ) && count( $this->profile['padding'] ) !== 4 ) {
        if( !is_array( $this->profile['padding'] ) ) {
          $p = $this->profile['padding'];
          $this->profile['padding'] = array( $p, $p, $p, $p );
        }
        else {
          switch( count( $this->profile['padding'] ) ) {
            case '1':
              $p = $this->profile['padding'];
              $this->profile['padding'] = array( $p, $p, $p, $p );
              break;
            case '2':
              $this->profile['padding'][] = $this->profile['padding']['0'];
              $this->profile['padding'][] = $this->profile['padding']['1'];
              break;
            case '3':
              $this->profile['padding'][] = $this->profile['padding']['1'];
              break;
          }
        }
      }

      if( isset( $this->profile['backgroundImg'] ) ) {
        if( !file_exists( $this->profile['backgroundImg'] ) ) {
          if( file_exists( WEB_BASE_PATH . $this->profile['backgroundImg'] ) ) {
            $this->profile['backgroundImg'] = WEB_BASE_PATH . $this->profile['backgroundImg'];
          }
          else {
            if( preg_match( '#^/module/(?P<module>.*?)(?P<tplPath>/.*)$#u', $this->profile['backgroundImg'], $matches ) ) {
              // Contenido dentro de un modulo
              $tplFile = ModuleController::getRealFilePath( 'classes/view/templates'.$matches['tplPath'], $matches['module'] );
              if( $tplFile ) {
                // error_log( 'Ficheiro '.$matches['tplPath'].' en Modulo '.$matches['module'] );
                $this->profile['backgroundImg'] = $tplFile;
              }
              else {
                error_log( 'ERROR: Non existe '.$matches['tplPath'].' en Modulo '.$matches['module'] );
                unset( $this->profile['backgroundImg'] );
              }
            }
            else {
              if( preg_match( '#^/app(?P<imgPath>/.*)$#u', $this->profile['backgroundImg'], $matches ) ) {
                // Contenido dentro de APP
                if( file_exists( APP_BASE_PATH . $matches['imgPath'] ) ) {
                  // error_log( 'Ficheiro '.$matches['imgPath'].' en APP' );
                  $this->profile['backgroundImg'] = APP_BASE_PATH . $matches['imgPath'];
                }
                else {
                  error_log( 'ERROR: Non existe '.$matches['imgPath'].' en APP' );
                  unset( $this->profile['backgroundImg'] );
                }
              }
              else {
                error_log( 'ERROR: Non existe '.$this->profile['backgroundImg'] );
                unset( $this->profile['backgroundImg'] );
              }
            }
          }
        }
      }

      // Cogumelo::debug( __METHOD__.' - this->profile = '.$this->profile['idName'] );
    }
    else {
      $this->profile=false;
    }

    return $this->profile;
  }

  public function getRouteProfile( $profile ) {
    Cogumelo::debug( __METHOD__ ." - ( $profile )" );
    $imgRoute = false;
    $imgRouteOriginal = false;
    $urlId = false;

    if( $this->fileInfo ) {

      $imgRouteOriginal = $this->filesAppPath . $this->fileInfo['absLocation'];
      // $urlId = ( $this->verifyAKeyUrl ) ? $this->fileInfo['id'].'-a'.$this->fileInfo['aKey'] : $this->fileInfo['id'];
      $urlId = $this->fileInfo['id'].'-a'.$this->fileInfo['aKey'];

      if( $this->setProfile( $profile ) ) {
        $imgRoute = $this->filesCachePath .'/'. $urlId .'/'. $this->profile['idName'] .'/'. $this->fileInfo['name'];
        Cogumelo::debug( __METHOD__." - imgRoute: $imgRoute" );







        if( !empty($imgRoute) ) {
          $imgRouteInfo = pathinfo( $imgRoute );
          $imgRouteProfile = false;

          switch( $this->profile['saveFormat'] ) {
            case 'JPEG':
              $saveFormatExt = 'jpg';
              break;
            case 'PNG':
              $saveFormatExt = 'png';
              break;
          }
          if( !empty( $saveFormatExt ) ) {
            $imgRouteProfile = $imgRouteInfo['dirname'] .'/'. $imgRouteInfo['filename'] .'.'. $saveFormatExt;
          }
          if( $this->profile['saveName'] ) {
            $imgRouteProfile = $imgRouteInfo['dirname'] .'/'. $this->profile['saveName'];
          }

          if( $imgRouteProfile && file_exists( $imgRouteProfile ) ) {
            Cogumelo::debug( __METHOD__.' - imgRouteProfile OK' );
            $imgRoute = $imgRouteProfile;
          }
        }
        Cogumelo::debug( __METHOD__." - imgRouteProfile: $imgRouteProfile" );







        if( !$this->profile['cache'] || !file_exists( $imgRoute ) ) {
          if( file_exists( $imgRoute ) ) {
            // Cogumelo::debug( __METHOD__.' - unlink '.$imgRoute );
            // unlink( $imgRoute );

            $cacheRouteInfo = pathinfo( $imgRoute );
            Cogumelo::debug( __METHOD__.' NO Cache - rmdirRec unlink '.$cacheRouteInfo['dirname'].'/' );
            $this->rmdirRec( $cacheRouteInfo['dirname'].'/' );
          }
          $imgRoute = $this->createImageProfile( $imgRouteOriginal, $imgRoute );
        }
      }
      else {
        // Original

        $imgRoute = $this->filesCachePath .'/'. $urlId .'/'. $this->fileInfo['name'];
        Cogumelo::debug( __METHOD__." - ( NONE ): $imgRoute" );

        if( !file_exists( $imgRoute ) ) {
          $toRouteDir = pathinfo( $imgRoute, PATHINFO_DIRNAME );
          Cogumelo::debug( __METHOD__." - toRouteDir = $toRouteDir" );
          if( !file_exists( $toRouteDir ) ) {
            Cogumelo::debug( __METHOD__.' - mkdir '.$toRouteDir );
            $maskPrev = umask( 0 );
            mkdir( $toRouteDir, 0775, true );
            umask( $maskPrev );
          }

          // Si se puede, aseguramos un acceso al directorio sin aKey
          $linkDirId = $this->filesCachePath .'/'. $this->fileInfo['id'];
          if( !$this->verifyAKeyUrl && !file_exists( $linkDirId ) ) {
            Cogumelo::debug( __METHOD__.' - symlink-0 '.$urlId.' , '.$linkDirId );
            symlink( $urlId, $linkDirId );
          }

          if( !copy( $imgRouteOriginal, $imgRoute ) ) {
            Cogumelo::error( __METHOD__." - ERROR in copy( $imgRouteOriginal, $imgRoute )" );
            Cogumelo::debug( __METHOD__." - ERROR in copy( $imgRouteOriginal, $imgRoute )" );
          }
        }
      }
    }

    if( !$imgRoute || !file_exists( $imgRoute ) ) {
      $imgRoute = $imgRouteOriginal;
    }
    else {
      if( false !== $this->profile['cache'] ) {
        $imgRouteInfo = pathinfo( $imgRoute );

        // Si se puede, aseguramos un acceso al directorio sin aKey
        $linkDirId = $this->filesCachePath .'/'. $this->fileInfo['id'];
        if( !$this->verifyAKeyUrl && !file_exists( $linkDirId ) ) {
          Cogumelo::debug( __METHOD__.' - symlink-1 '.$urlId.' , '.$linkDirId );
          symlink( $urlId, $linkDirId );
        }

        // RealName link
        $linkRealNameFile = $imgRouteInfo['dirname'] .'/'. $this->fileInfo['name'];
        if( !file_exists( $linkRealNameFile ) ) {
          Cogumelo::debug( __METHOD__.' - symlink-2 '.$imgRouteInfo['basename'].' , '.$linkRealNameFile );
          symlink( $imgRouteInfo['basename'], $linkRealNameFile );
        }

        // Si se puede, aseguramos un acceso al fichero usando ID como nombre
        $linkIdNameFile = $imgRouteInfo['dirname'] .'/'. $this->fileInfo['id'] .'.'. $imgRouteInfo['extension'];
        if( !$this->disableRawUrlProfile && !file_exists( $linkIdNameFile ) ) {
          Cogumelo::debug( __METHOD__.' - symlink-3 '.$imgRouteInfo['basename'].' , '.$linkIdNameFile );
          symlink( $imgRouteInfo['basename'], $linkIdNameFile );
        }

      }
    }

    Cogumelo::debug( __METHOD__." - RET = $imgRoute" );
    return $imgRoute;
  }


  public function createImageProfile( $fromRoute, $toRoute, $toEncode = false ) {
    Cogumelo::debug( __METHOD__.' - REQUEST_URI '. $_SERVER["REQUEST_URI"]  );
    // error_log( __METHOD__.' '. debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 1 )[0]['file'] );
    // error_log( '---' );error_log( '---' );error_log( '---' );
    // error_log( 'FiledataImagesController: createImageProfile(): ' );
    // error_log( $fromRoute );
    // error_log( 'mime_content_type: '.mime_content_type( $fromRoute ) );
    // error_log( $toRoute );

    $result = false;

    $mimeTypeOrg = false;
    if( file_exists( $fromRoute ) ) {
      $mimeType = mime_content_type( $fromRoute );
      if( mb_strpos( $mimeType, 'image' ) === 0 ) {
        $mimeTypeOrg = $mimeType;
      }
    }


    $saveFormatExt = false;
    switch( $this->profile['saveFormat'] ) {
      case 'JPEG':
        $saveFormatExt = 'jpg';
        break;
      case 'PNG':
        $saveFormatExt = 'png';
        break;
    }


    $toRouteInfo = pathinfo( $toRoute );
    if( file_exists( $toRoute ) ) {
      Cogumelo::debug( __METHOD__.' - toRoute' );
      $result = $toRoute;
    }
    elseif( !empty($toRoute) ) {
      $toRouteReal = false;
      if( $saveFormatExt ) {
        $toRouteReal = $toRouteInfo['dirname'] .'/'. $toRouteInfo['filename'] .'.'. $saveFormatExt;
      }
      if( $this->profile['saveName'] ) {
        $toRouteReal = $toRouteInfo['dirname'] .'/'. $this->profile['saveName'];
      }

      if( $toRouteReal && file_exists( $toRouteReal ) ) {
        Cogumelo::debug( __METHOD__.' - toRouteReal' );
        $toRoute = $toRouteReal;
        $result = $toRoute;
      }
    }

    if( $this->profile && $mimeTypeOrg && ( $result !== $toRoute || $toEncode ) ) {

      $tmpFlag = ( file_exists( $toRoute ) ) ? 'SI' : 'NON';
      Cogumelo::debug( __METHOD__.' - file_exists '. $tmpFlag );
      $tmpFlag = ( $toEncode ) ? 'SI' : 'NON';
      Cogumelo::debug( __METHOD__.' - toEncode '.$tmpFlag );



      $im = new Imagick();

      //$im->setBackgroundColor( new ImagickPixel( $this->profile['backgroundColor'] ) );
      $im->setBackgroundColor( new ImagickPixel( 'transparent' ) );


      if( mb_strpos( $mimeTypeOrg, 'image/svg' ) === 0 ) {
        // Imagenes SVG
        $this->loadPreprocessedSvg( $im, $fromRoute );
      }
      else {
        // Imagenes no SVG
        $im->readimagefile( fopen( $fromRoute, 'rb' ) );
        // $im->trimImage( 0 ); // Recorta deixando so a imaxe
        $im->setImagePage( 0, 0, 0, 0 ); // Reset del tamaño de lienzo
      }


      /*
       * ORIENTATION image
       * "Imagick::ORIENTATION_VALUE", with "VALUE" values of:
       * UNDEFINED (0), TOPLEFT (1), TOPRIGHT (2), BOTTOMRIGHT (3), BOTTOMLEFT (4), LEFTTOP (5), RIGHTTOP (6), RIGHTBOTTOM (7), and LEFTBOTTOM (8)
       */
      $imageOrientation = $im->getImageOrientation();
      switch( $imageOrientation ) {
        case Imagick::ORIENTATION_BOTTOMRIGHT: //value (integer): 3
          $im->rotateimage( '#fff', 180 );  //rotate 180º 
          break;
        case Imagick::ORIENTATION_RIGHTTOP: //value (integer): 6
          $im->rotateimage( '#fff', 90 ); //rotate 90º
          break;
        case Imagick::ORIENTATION_LEFTBOTTOM: //value (integer): 8 
          $im->rotateimage( '#fff', 270 );  //rotate 270º
          break;
      }
      

      $imSize = $im->getImageGeometry();
      $x = $imSize['width'];
      $y = $imSize['height'];
      $tx = $this->profile['width'];
      $ty = $this->profile['height'];
      if( isset( $this->profile['padding'] ) ) {
        $tx = $tx - $this->profile['padding']['1'] - $this->profile['padding']['3'];
        $ty = $ty - $this->profile['padding']['0'] - $this->profile['padding']['2'];
      }
      // error_log( "Datos iniciales $x $y $tx $ty ---" );


      // RESIZE
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
            // error_log( "Cortar sin ampliar: $x $y $tx $ty ---" );
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

        // error_log( "Datos recalculados $x $y $tx $ty ---" );

        if( $escalar ) {

          // error_log( "Valores para escalar: $x $y $tx $ty ---" );

          $im->scaleImage( $tx, $ty, false );

          $imSize = $im->getImageGeometry();
          $x = $imSize['width'];
          $y = $imSize['height'];
          $tx = $this->profile['width'];
          $ty = $this->profile['height'];

          // error_log( "Xa escalado: $x $y $tx $ty ---" );
        }

        if( $tx < $x || $ty < $y ) {
          $px = intval( ($x-$tx)/2 );
          $py = intval( ($y-$ty)/2 );
          $im->cropImage( $tx, $ty, $px, $py );
          // error_log( "Valores para cortar $x $y $tx $ty $px $py ---" );
        }
      }


      // PADDING
      if( isset( $this->profile['padding'] ) ) {
        $marco = new Imagick();
        //$marco->newImage( $this->profile['width'], $this->profile['height'], new ImagickPixel( $this->profile['backgroundColor'] ) );
        $marco->newImage( $this->profile['width'], $this->profile['height'], new ImagickPixel( 'transparent' ) );
        $marco->setImageVirtualPixelMethod( Imagick::VIRTUALPIXELMETHOD_TRANSPARENT );
        $marco->compositeImage( $im, Imagick::COMPOSITE_DEFAULT, $this->profile['padding']['3'], $this->profile['padding']['0'] );
        $im->clear();
        $im = $marco;
      }


      // CHAPA
      if( isset( $this->profile['backgroundImg'] ) ) {
        $chapa = new Imagick( $this->profile['backgroundImg'] );
        $im->setImageVirtualPixelMethod( Imagick::VIRTUALPIXELMETHOD_TRANSPARENT );
        //$im->setImageArtifact( 'compose:args', "1,0,-0.5,0.5" );
        $im->compositeImage( $chapa, Imagick::COMPOSITE_OVERLAY, 0, 0 );
        $chapa->clear();
      }


      // backgroundColor
      if( isset( $this->profile['backgroundColor'] ) ) {
        $fonfo = new Imagick();
        $imSize = $im->getImageGeometry();
        $fonfo->newImage( $imSize['width'], $imSize['height'], new ImagickPixel( $this->profile['backgroundColor'] ) );
        $fonfo->setImageVirtualPixelMethod( Imagick::VIRTUALPIXELMETHOD_TRANSPARENT );
        $fonfo->compositeImage( $im, Imagick::COMPOSITE_DEFAULT, 0, 0 );
        $im->clear();
        $im = $fonfo;
      }


      if( $this->profile['saveQuality'] ) {
        $im->setImageCompressionQuality( $this->profile['saveQuality'] );
      }

      if( $this->profile['saveFormat'] ) {
        $im->setImageFormat( $this->profile['saveFormat'] );
      }


      // DEBUG info:
      // $dbSize = $im->getImageGeometry();
      // error_log( 'Datos finales '.$dbSize['width'].' '.$dbSize['height'].' '.
      //   $this->profile['width'].' '.$this->profile['height'].' ---' );


      if( !$toEncode ) {
        // Save and get URL

        // error_log( "toRouteInfo = " . print_r( $toRouteInfo, true ) );
        if( !file_exists( $toRouteInfo['dirname'] ) ) {
          // error_log( 'mkdir '.$toRouteInfo['dirname'] );
          $maskPrev = umask( 0 );
          mkdir( $toRouteInfo['dirname'], 0775, true );
          umask( $maskPrev );
        }

        if( is_writable( $toRouteInfo['dirname'] ) ) {
          if( $this->profile['saveFormat'] ) {
            if( $saveFormatExt ) {
              $toRoute = $toRouteInfo['dirname'] .'/'. $toRouteInfo['filename'] .'.'. $saveFormatExt;
            }
          }

          if( $this->profile['saveName'] ) {
            $toRoute = $toRouteInfo['dirname'] .'/'. $this->profile['saveName'];
          }
          //$im->setImageCompressionQuality(90);
          //error_log( 'FiledataImagesController: createImageProfile: writeImage '.$toRoute );
          Cogumelo::debug( __METHOD__.' - writeImage '.$toRoute );
          $im->writeImage( $toRoute );
        }
        else {
          $toRoute = false;
          Cogumelo::debug( __METHOD__.' - Imposible guardar la imagen en $toRoute' );
          Cogumelo::error( __METHOD__.' - Imposible guardar la imagen en $toRoute' );
        }


        if( $toRoute && file_exists( $toRoute ) ) {
          $imgRouteInfo = pathinfo( $toRoute );

          // Si se puede, aseguramos un acceso al directorio sin aKey
          $linkDirId = $this->filesCachePath .'/'. $this->fileInfo['id'];
          $urlId = $this->fileInfo['id'].'-a'.$this->fileInfo['aKey'];
          if( !$this->verifyAKeyUrl && !file_exists( $linkDirId ) ) {
            // error_log( 'symlink de '.$urlId.' como '.$linkDirId );
            Cogumelo::debug( __METHOD__.' - symlink-1 '.$urlId.', '.$linkDirId );
            symlink( $urlId, $linkDirId );
          }

          // RealName link
          $linkRealNameFile = $imgRouteInfo['dirname'] .'/'. $this->fileInfo['name'];
          if( !file_exists( $linkRealNameFile ) ) {
            Cogumelo::debug( __METHOD__.' - symlink-2 '.$imgRouteInfo['basename'].', '.$linkRealNameFile );
            symlink( $imgRouteInfo['basename'], $linkRealNameFile );
          }

          // Si se puede, aseguramos un acceso al fichero usando ID como nombre
          $linkIdNameFile = $imgRouteInfo['dirname'] .'/'. $this->fileInfo['id'] .'.'. $imgRouteInfo['extension'];
          if( !$this->disableRawUrlProfile && !file_exists( $linkIdNameFile ) ) {
            Cogumelo::debug( __METHOD__.' - symlink-3 '.$imgRouteInfo['basename'].', '.$linkIdNameFile );
            symlink( $imgRouteInfo['basename'], $linkIdNameFile );
          }
        }


        $result = $toRoute;
      }
      else {
        // base64 JPEG SRC Encode
        $im->setImageFormat('JPEG');
        $maxQuality = 75;
        if( !isset( $this->profile['saveQuality'] ) || $this->profile['saveQuality'] > $maxQuality ) {
          $im->setImageCompressionQuality( $maxQuality );
        }

        $result = 'data:image/jpg;base64,'.base64_encode( $im->getImageBlob() );
        Cogumelo::debug( __METHOD__.' - jpg base64 SRC Encode strlen: '.mb_strlen($result) );
      }


      $im->clear();
      $im->destroy();
    }

    // error_log( '---' );error_log( '---' );error_log( '---' );
    return $result;
  }

  private function loadPreprocessedSvg( $im, $fromRoute ) {
    // error_log( __METHOD__.' '. debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 1 )[0]['file'] );
    // Imagenes SVG
    $svg = file_get_contents( $fromRoute );

    // Machaco el tamaño de lienzo
    if( mb_strpos( $svg, 'viewBox' ) !== false ) {
      $svg = preg_replace( '/(\s+)(width|height)="(.*?)"/', '${1}${2}="128px"', $svg );
      //$svg = preg_replace( '/viewBox="(.*?)"/', 'viewBox="0 0 128 128"', $svg );
    }
    else {
      $svg = preg_replace( '/(\s+)(width)="(.*?)"/',  '${1}${2}="32px"', $svg );
      $svg = preg_replace( '/(\s+)(height)="(.*?)"/', '${1}${2}="32px"'."\n".'${1}viewBox="0 0 32 32"', $svg );
    }
    // error_log( 'SVG: '.$svg );

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
      // $imSvg->trimImage( 0 ); // Recorta deixando so a imaxe
      $imSvg->setImagePage( 0, 0, 0, 0 ); // Reset del tamaño de lienzo
      $imSvgSize = $imSvg->getImageGeometry();
      $x = $imSvgSize['width'];
      $y = $imSvgSize['height'];
      $tx = $this->profile['width'];
      $ty = $this->profile['height'];
      // error_log( "SVG iniciales $x $y $tx $ty $density ---" );
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
      // error_log( "SVG finales $x $y $tx $ty $density ---" );
      $imSvg->clear();
      $imSvg->destroy();

      $im->setResolution( $density, $density );
    }


    if( isset( $this->profile['rasterColor'] ) ) {
      // Para dar color en la carga de formatos raster
      $svg = preg_replace( '/(style="fill:)(#[0-9a-f]+)([;"])/', '${1}'.$this->profile['rasterColor'].'${3}', $svg );
    }

    //error_log( 'SVG: '.$svg );
    $im->readImageBlob( $svg );
    // $im->trimImage( 0 ); // Recorta deixando so a imaxe
    $im->setImagePage( 0, 0, 0, 0 ); // Reset del tamaño de lienzo
  }





  public function sendImage( $imgInfo ) {
    // error_log( __METHOD__.' '.print_r( $imgInfo, true ) );
    Cogumelo::debug( __METHOD__.' '.print_r( $imgInfo, true ) );

    $result = false;

    if( file_exists( $imgInfo['route'] ) ) {

      /*
      if( !isset( $imgInfo['type'] ) ) {
        $imgInfo['type'] = mime_content_type( $imgInfo['route'] );
      }
      */
      $imgInfo['type'] = mime_content_type( $imgInfo['route'] );

      if( !isset( $imgInfo['name'] ) ) {
        $imgInfo['name'] = mb_substr( mb_strrchr( $imgInfo['route'], '/' ), 1 );
      }

      // print headers
      header( 'Content-Disposition: inline; filename="' . $imgInfo['name'] . '"' );
      header( 'Content-Type: ' . $imgInfo['type'] );
      header( 'Content-Length: ' . filesize( $imgInfo['route'] ) );

      // header( 'Expires: 0' );
      header( 'Cache-Control: must-revalidate' );
      header( 'Pragma: public' );

      ob_flush();
      flush();

      // print image
      readfile( $imgInfo['route'] );

      // TODO: Revisar
      if( isset( $this->profile['cache'] ) && !$this->profile['cache'] ) {
        // Cogumelo::debug( __METHOD__.' - unlink '.$imgInfo['route'] );

        $info = pathinfo( $imgInfo['route'] );
        $cacheDir = $info['dirname'].'/';

        if( !empty( $this->filesCachePath ) && strpos( $cacheDir, $this->filesCachePath ) !== false ) {
          Cogumelo::debug( __METHOD__.' NO Cache - rmdirRec unlink '.$cacheDir );
          $this->rmdirRec( $cacheDir );
        }
        else {
          Cogumelo::debug( __METHOD__.' ERROR: PELIGRO!!! Intento de borrado de ficheros fuera de "cachePath"' );
          Cogumelo::error( __METHOD__.' ERROR: PELIGRO!!! Intento de borrado de ficheros fuera de "cachePath"' );
          Cogumelo::error( __METHOD__.' filesCachePath '.$this->filesCachePath );
          Cogumelo::error( __METHOD__.' cacheDir '.$cacheDir );
          error_log( __METHOD__.' ERROR: PELIGRO!!! Intento de borrado de ficheros fuera de "cachePath"' );
          error_log( __METHOD__.' filesCachePath '.$this->filesCachePath );
          error_log( __METHOD__.' cacheDir '.$cacheDir );
        }
      }

      $result = true;
    }
    else {
      Cogumelo::debug( __METHOD__.' - Fichero no encontrado: ' . $imgInfo['route'] );
      Cogumelo::error( __METHOD__.' - Fichero no encontrado: ' . $imgInfo['route'] );
    }

    return $result;
  }


  public function clearCache( $fileId ) {
    Cogumelo::debug( __METHOD__.' - ' . $fileId );

    if( is_integer( $fileId ) && $fileId > 0 ) {
      $imgCacheRoute = $this->filesCachePath .'/'. $fileId;
      if( is_link( $imgCacheRoute ) ) {
        $realDir = readlink( $imgCacheRoute );
        if( strpos( $realDir, '/' ) !== 0 ) {
          $realDir = pathinfo( $imgCacheRoute, PATHINFO_DIRNAME ).'/'.$realDir;
        }
        Cogumelo::debug( __METHOD__.' - unlink Dir '. $imgCacheRoute );
        unlink( $imgCacheRoute );
        Cogumelo::debug( __METHOD__.' - rmdirRec-1 unlink '. $realDir );
        $this->rmdirRec( $realDir );
      }
      else {
        Cogumelo::debug( __METHOD__.' - rmdirRec-2 unlink '. $imgCacheRoute );
        $this->rmdirRec( $imgCacheRoute );
      }
    }
  }


  public function rmdirRec( $dir ) {
    Cogumelo::debug( __METHOD__.' - '. $dir );

    $dir = rtrim( $dir, '/' );
    if( is_dir( $dir ) ) {
      $dirElements = scandir( $dir );
      if( !empty( $dirElements ) ) {
        foreach( $dirElements as $object ) {
          if( $object !== '.' && $object !== '..' ) {
            if( is_dir( $dir.'/'.$object ) ) {
              // Cogumelo::debug( __METHOD__.' - rmdirRec '. $dir.'/'.$object );
              $this->rmdirRec( $dir.'/'.$object );
            }
            else {
              // Cogumelo::debug( __METHOD__.' - unlink '. $dir.'/'.$object );
              unlink( $dir.'/'.$object );
            }
          }
        }
      }
      reset( $dirElements );
      if( !is_link( $dir ) ) {
        rmdir( $dir );
      }
      else {
        unlink( $dir );
      }
    }
  }

} // FiledataImagesController
