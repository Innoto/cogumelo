<?php


class FiledataController {

  // Ruta a partir de la que se crean los directorios y ficheros subidos
  var $filesAppPath = false;

  // var $fileId = false;
  // var $fileInfo = false;

  private $replaceAcents = array(
    'from' => array( 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï',
      'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä',
      'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù',
      'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č',
      'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ',
      'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ',
      'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň',
      'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ',
      'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů',
      'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ',
      'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ',
      'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ' ),
    'to'   => array( 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I',
      'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a',
      'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u',
      'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c',
      'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G',
      'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i',
      'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N',
      'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S',
      's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u',
      'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o',
      'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A',
      'a', 'A', 'a', 'O', 'o' )
  );


  public function __construct() {
    // error_log( 'FiledataController __construct' );
    $this->filesAppPath = Cogumelo::getSetupValue( 'mod:filedata:filePath' );
  }



  /**
   * Load File info
   */
  public function loadFileInfo( $fileId ) {
    // error_log( 'FiledataController: loadFileInfo(): ' . $fileId );
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
    Creates a database FiledataModel register and save
  */
  public function saveFile( $originFile, $relativeDestPath, $fileName, $fileInApp = true ) {
    // error_log( 'FiledataController: saveFile(): ' . $originFile );

    $absFrom = ( $fileInApp ) ? APP_BASE_PATH. $originFile : $originFile;

    $filedataInfo = array(
      'name' => $fileName,
      'absLocation' => $absFrom,
      'destDir' => $relativeDestPath
    );

    $filedataObj = $this->createNewFile( $filedataInfo );

    return $filedataObj;
  } // function saveFile()

  /*
  public function saveFile( $originFile, $relativeDestPath, $fileName, $originFileIsRelative = true ) {
    // error_log( 'FiledataController: saveFile(): ' . $originFile );

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

      if( !is_dir($this->filesAppPath.$relativeDestPath) ) {
        mkdir($this->filesAppPath.$relativeDestPath, 0755, true);
      }

      if( copy ( $absFrom, $this->filesAppPath.$relativeDestPath.'/'.$realDestName ) ) {
        //$finfo = new finfo(FILEINFO_MIME, "/usr/share/misc/magic");
        //$fileDB->setter('type', $finfo->file($absFrom) );
        //finfo_close($finfo);

        // mime type
        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        $fileDB->setter('type', finfo_file( $finfo, $absFrom ) );


        $fileDB->setter('size', filesize( $absFrom ) );
        $fileDB->setter('name', $realDestName );
        $fileDB->setter('absLocation', $relativeDestPath.'/'.$realDestName );

        $fileDB->save();
      }
      else {
        cogumelo::error( 'FiledataController cant copy the file to: '. $this->filesAppPath.$relativeDestPath.'/'.$realDestName);
        $fileDB->delete();
      }

    }
    else {
      cogumelo::error( 'FiledataController cant find the file path to save: '.$absFrom);
    }

    if( $fileDB && $fileDB->getter('id') ) {
      filedata::load('controller/FiledataImagesController.php');
      $fileImageCtrl = new FiledataImagesController();
      $fileImageCtrl->clearCache( $fileDB->getter('id') );
    }

    return $fileDB;
  } // function saveFile()
  */








  /**
   * Creates FileModel and register FilegroupModel
   */
  public function saveToFileGroup( $filedataInfo, $idGroup = false ) {
    error_log( 'FiledataController: saveToFileGroup(filedataInfo, idGroup): ' . print_r( $filedataInfo, true ).' - '.$idGroup );

    $filedataObj = $this->createNewFile( $filedataInfo );

    $idGroup = ( $idGroup ) ? $idGroup : 0;
    $fileGroupData = [ 'idGroup' => $idGroup, 'filedataId' => $filedataObj->getter('id') ];

    $fileGroupObj = new FilegroupModel( $fileGroupData );
    $fileGroupObj->save();
    if( !$idGroup ) {
      $fileGroupObj->setter( 'idGroup', $fileGroupObj->getter('id') );
      $fileGroupObj->save();
    }

    return $fileGroupObj;
  } // function createNewFileGroup()

  /**
   * Remove a FileModel and unregister from FilegroupModel
   */
  public function deleteFromFileGroup( $deleteId, $idGroup ) {
    error_log( 'FiledataController: deleteFromFileGroup(deleteId, idGroup): '.
      $deleteId.' - '.$idGroup );
    $result = false;

    if( $this->deleteFile( $deleteId ) ) {
      $objModel = new FilegroupModel();
      $listModel = $objModel->listItems( ['filters' => [ 'idGroup' => $idGroup,
        'filedataId' => $deleteId ] ] );
      if( $listModel && $fileGroupObj = $listModel->fetch() ) {
        $result = $fileGroupObj->delete();
      }
    }

    return $result;
  } // function createNewFileGroup()









  /**
    Creates a database FiledataModel register and copy file
  */
  public function createNewFile( $filedataInfo ) {
    // error_log( 'FiledataController: createFile(): ' . print_r( $filedataInfo, true ) );

    $filedataObj = false;

    if( isset( $filedataInfo['absLocation'] ) && file_exists( $filedataInfo['absLocation'] ) ) {
      $absFrom = $filedataInfo['absLocation'];
      $modelInfo = $filedataInfo;

      if( !isset( $modelInfo['type'] ) ) {
        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        $modelInfo['type'] = finfo_file( $finfo, $absFrom );
      }
      if( !isset( $modelInfo['size'] ) ) {
        $modelInfo['size'] = filesize( $absFrom );
      }
      if( !isset( $modelInfo['originalName'] ) ) {
        $filePathInfo = pathinfo( $absFrom );
        $modelInfo['originalName'] = $filePathInfo['basename'];
      }
      if( !isset( $modelInfo['name'] ) ) {
        $modelInfo['name'] = $modelInfo['originalName'];
      }
      $modelInfo['name'] = $this->secureFileName( $modelInfo['name'] );

      if( !isset( $modelInfo['user'] ) && class_exists('UserAccessController') ) {
        $useraccesscontrol = new UserAccessController();
        $user = $useraccesscontrol->getSessiondata();
        if( $user && $user['data']['active'] ) {
          $modelInfo['user'] = $user['data']['id'];
        }
      }

      $filedataObj = new FiledataModel( $modelInfo );
      $filedataObj->save();

      $secureFileName = $modelInfo['name'];
      $relativeDestPath = ( isset( $filedataInfo['destDir'] ) ) ? $filedataInfo['destDir'] : '';

      if( file_exists( $this->filesAppPath.$relativeDestPath.'/'.$secureFileName ) ){
        // error_log( 'FiledataController: (Notice) createFile - COLISION: '.$this->filesAppPath.$relativeDestPath.'/'.$secureFileName );
        $filePathInfo = pathinfo( $secureFileName );
        $secureFileName = $filePathInfo['filename'] .'_fdmi'. $filedataObj->getter('id') .'.'. $filePathInfo['extension'];
      }

      if( !is_dir( $this->filesAppPath.$relativeDestPath ) ) {
        mkdir( $this->filesAppPath.$relativeDestPath, 0755, true );
      }

      if( copy( $absFrom, $this->filesAppPath.$relativeDestPath.'/'.$secureFileName ) ) {

        // TODO: Falta ver se eliminamos o ficheiro $absFrom do disco

        $filedataObj->setter( 'name', $secureFileName );
        $filedataObj->setter( 'absLocation', str_replace( '//', '/', $relativeDestPath.'/'.$secureFileName ) );
        $filedataObj->save();
      }
      else {
        cogumelo::error( 'FiledataController cant copy the file to: '. $this->filesAppPath.$relativeDestPath.'/'.$secureFileName);
        // error_log( 'FiledataController cant copy the file to: '. $this->filesAppPath.$relativeDestPath.'/'.$secureFileName);
        $filedataObj->delete();
        $filedataObj = false;
      }

    }
    else {
      cogumelo::error( 'FiledataController cant find the file path to save. ');
      // error_log( 'FiledataController cant find the file path to save: '.$absFrom);
    }

    if( $filedataObj && $filedataObj->getter('id') ) {
      filedata::load('controller/FiledataImagesController.php');
      $fileImageCtrl = new FiledataImagesController();
      $fileImageCtrl->clearCache( $filedataObj->getter('id') );
    }

    return $filedataObj;
  } // function createNewFile()


  /**
    Update a database FiledataModel register
  */
  public function updateInfo( $fileId, $filedataInfo ) {
    // error_log( 'FiledataController: updateInfo(): ' . print_r( $filedataInfo, true ) );

    $filedataObj = false;

    $fileModel = new filedataModel();
    if( $fileList = $fileModel->listItems( array( 'filters' => array( 'id' => $fileId ) ) ) ) {
      $filedataObj = $fileList->fetch();
    }

    if( $filedataObj ) {
      $ignore = array( 'id', 'name', 'originalName', 'absLocation', 'size' );
      foreach( array_keys( $filedataObj->getCols( true ) ) as $keyVO ) {
        if( isset( $filedataInfo[ $keyVO ] ) && !in_array( $keyVO, $ignore ) ) {
          $filedataObj->setter( $keyVO, $filedataInfo[ $keyVO ] );
        }
      }
      $filedataObj->save();
    }
    else {
      cogumelo::error( 'FiledataController: updateInfo - No Filedata ID:'.$fileId );
      // error_log( 'ERROR: FiledataController: updateInfo - No Filedata ID:'.$fileId );
    }

    return $filedataObj;
  } // function updateInfo()


  /**
    Delete a database FiledataModel register and files
  */
  public function deleteFile( $fileId ) {
    error_log( 'FiledataController: deleteFile(): ' . $fileId );
    $result = false;

    $objModel = new FiledataModel();
    $listModel = $objModel->listItems( array( 'filters' => array( 'id' => $fileId ) ) );

    if( $listModel && $filedataObj = $listModel->fetch() ) {
      $result = $filedataObj->delete();
    }

    return $result;
  } // function deleteFile( $fileId )


  /**
    Remove server files
  */
  public function removeServerFiles( $voFile ) {
    // error_log( 'FiledataController: removeServerFiles(): ' . $voFile->getter('id') );

    // error_log( 'FiledataController: removeServerFiles(): clearCache '.$voFile->getter('id') );
    filedata::load('controller/FiledataImagesController.php');
    $fileImageCtrl = new FiledataImagesController();
    $fileImageCtrl->clearCache( $voFile->getter('id') );

    $serverFile = Cogumelo::getSetupValue( 'mod:filedata:filePath' ).$voFile->getter('absLocation');
    // error_log( 'FiledataController: removeServerFiles(): unlink '.$serverFile );
    if( file_exists( $serverFile ) ) {
      unlink( $serverFile );
    }
  } // function removeServerFiles( $voFile )


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
