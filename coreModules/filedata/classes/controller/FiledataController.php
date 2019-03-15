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
class FiledataController {

  // Ruta a partir de la que se crean los directorios y ficheros subidos
  var $filesAppPath = false;
  var $filesAppPathPlain = false;

  public $cacheQuery = false; // false, true or time in seconds



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
    // Cogumelo::debug( __METHOD__ );
    $this->filesAppPath = Cogumelo::getSetupValue( 'mod:filedata:filePath' );
    $this->filesAppPathPlain = realpath( $this->filesAppPath );

    $cache = Cogumelo::getSetupValue('cache:Filedata');
    if( $cache !== null ) {
      Cogumelo::log( __METHOD__.' ---- ESTABLECEMOS CACHE A '.$cache, 'cache' );
      $this->cacheQuery = $cache;
    }
  }



  /**
   * Load File info
   */
  public function loadFileInfo( $fileId ) {
    // Cogumelo::debug( __METHOD__.' - ' . $fileId );
    $fileInfo = false;

    if( !empty( $fileId ) ) {
      $fileModel = new FiledataModel();
      $fileList = $fileModel->listItems([ 'filters' => [ 'id' => $fileId ], 'cache' => $this->cacheQuery ]);
      $fileObj = is_object( $fileList ) ? $fileList->fetch() : false;
      $fileInfo = is_object( $fileObj ) ? $fileObj->getAllData('onlydata') : false;
    }

    if( !empty( $fileInfo ) ) {
      geozzy::load('controller/ResourceController.php');
      $resCtrl = new ResourceController();
      $fileInfo = $resCtrl->getTranslatedData( $fileInfo );
      $fileInfo['validatedAccess'] = $this->validateAccess( $fileInfo );
    }
    else {
      $fileInfo = false;
    }

    return $fileInfo;
  } // function loadFileInfo()


  public function validateAccess( $fileInfo ) {
    $validated = false;

    if( isset( $fileInfo['privateMode'] ) && $fileInfo['privateMode'] > 0 && class_exists('UserAccessController') ) {
      Cogumelo::debug( __METHOD__.' - Verificando usuario logueado para acceder a fichero...' );
      $useraccesscontrol = new UserAccessController();
      $user = $useraccesscontrol->getSessiondata();

      if( $user && $user['data']['active'] ) {
        unset( $user['data']['password'] );
        Cogumelo::debug( __METHOD__.' - USER: '.json_encode( $user ) );

        if( !empty( $fileInfo['user'] ) && $user['data']['id'] === $fileInfo['user'] ) {
          // El fichero es del usuario actual
          Cogumelo::debug( __METHOD__.' - Verificado por ID' );
          $validated = true;
        }

        if( !$validated ) {
          $validRoles = [ 'filedata:privateAccess' ];
          if( $useraccesscontrol->checkPermissions( $validRoles, 'admin:full' ) ) {
            // Permiso de acceso a todos los ficheros
            Cogumelo::debug( __METHOD__.' - Verificado por Rol' );
            $validated = true;
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
    // Cogumelo::debug( __METHOD__.' - ' . $originFile );

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
      // Cogumelo::debug( __METHOD__.' - ' . $originFile );

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
          Cogumelo::error( __METHOD__.' - cant copy the file to: '. $this->filesAppPath.$relativeDestPath.'/'.$realDestName);
          $fileDB->delete();
        }

      }
      else {
        Cogumelo::error( __METHOD__.' - cant find the file path to save: '.$absFrom);
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
    Cogumelo::debug( __METHOD__.' - (filedataInfo, idGroup): ' . print_r( $filedataInfo, true ).' - '.$idGroup );

    $filedataObj = $this->createNewFile( $filedataInfo );

    $idGroup = !empty( $idGroup ) ? $idGroup : 0;
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
    Cogumelo::debug( __METHOD__.' - (deleteId, idGroup): '.$deleteId.' - '.$idGroup );
    $result = false;

    if( $this->deleteFile( $deleteId ) ) {
      $objModel = new FilegroupModel();
      $listModel = $objModel->listItems([
        'filters' => [ 'idGroup' => $idGroup, 'filedataId' => $deleteId ],
        'cache' => $this->cacheQuery
      ]);
      $fileGroupObj = ( gettype( $listModel ) === 'object' ) ? $listModel->fetch() : false;
      if( gettype( $fileGroupObj ) === 'object' ) {
        $result = $fileGroupObj->delete();
      }
    }

    return $result;
  } // function createNewFileGroup()









  /**
    Creates a database FiledataModel register and copy file
  */
  public function createNewFile( $filedataInfo ) {
    // Cogumelo::debug( __METHOD__.' - ' . print_r( $filedataInfo, true ) );

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

      if( empty( $modelInfo['aKey'] ) ) {
        $modelInfo['aKey'] = chr(97+rand(0,25)).chr(97+rand(0,25)).chr(97+rand(0,25)).
          chr(97+rand(0,25)).chr(97+rand(0,25)).chr(97+rand(0,25));
      }

      $filedataObj = new FiledataModel( $modelInfo );
      $filedataObj->save();

      $secureFileName = $modelInfo['name'];
      $relativeDestPath = !empty( $filedataInfo['destDir'] ) ? $filedataInfo['destDir'] : '';

      if( file_exists( $this->filesAppPath.$relativeDestPath.'/'.$secureFileName ) ){
        Cogumelo::debug( __METHOD__.' - (Notice) createFile - COLISION: '.$this->filesAppPath.$relativeDestPath.'/'.$secureFileName );
        $filePathInfo = pathinfo( $secureFileName );
        if(!empty($filePathInfo['extension'])){
          $secureFileName = $filePathInfo['filename'] .'_fdmi'. $filedataObj->getter('id') .'.'. $filePathInfo['extension'];
        }
        else{
          $secureFileName = $filePathInfo['filename'] .'_fdmi'. $filedataObj->getter('id');
        }
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
        $msg = ' - Cant copy the file to: '. $this->filesAppPath.$relativeDestPath.'/'.$secureFileName;
        Cogumelo::error( __METHOD__.$msg );
        Cogumelo::debug( __METHOD__.$msg );
        $filedataObj->delete();
        $filedataObj = false;
      }
    }
    else {
      Cogumelo::error( __METHOD__.' - Cant find the file path to save. '.json_encode( $filedataInfo ) );
      Cogumelo::debug( __METHOD__.' - Cant find the file path to save: '.json_encode( $filedataInfo ) );
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
    // Cogumelo::debug( __METHOD__.' - ' . print_r( $filedataInfo, true ) );

    $filedataObj = false;

    $fileModel = new FiledataModel();
    $fileList = $fileModel->listItems([ 'filters' => [ 'id' => $fileId ], 'cache' => $this->cacheQuery ]);
    $filedataObj = ( gettype( $fileList ) === 'object' ) ? $fileList->fetch() : false;

    if( gettype( $filedataObj ) === 'object' ) {
      $ignore = array( 'id', 'name', 'originalName', 'absLocation', 'size' );
      foreach( array_keys( $filedataObj->getCols( true ) ) as $keyVO ) {
        if( isset( $filedataInfo[ $keyVO ] ) && !in_array( $keyVO, $ignore ) ) {
          $filedataObj->setter( $keyVO, $filedataInfo[ $keyVO ] );
        }
      }
      $filedataObj->save();
    }
    else {
      Cogumelo::error( __METHOD__.' - No Filedata ID:'.$fileId );
      Cogumelo::debug( __METHOD__.' - No Filedata ID:'.$fileId );
    }

    return $filedataObj;
  } // function updateInfo()


  /**
    Delete a database FiledataModel register and files
  */
  public function deleteFile( $fileId ) {
    Cogumelo::debug( __METHOD__.' - ' . $fileId );
    $result = false;

    $objModel = new FiledataModel();
    $listModel = $objModel->listItems([ 'filters' => [ 'id' => $fileId ], 'cache' => $this->cacheQuery ]);
    $filedataObj = ( gettype( $listModel ) === 'object' ) ? $listModel->fetch() : false;

    if( gettype( $filedataObj ) === 'object' ) {
      $result = $filedataObj->delete();
    }

    return $result;
  } // function deleteFile( $fileId )


  /**
    Remove server files
  */
  public function removeServerFiles( $voFile ) {
    // Cogumelo::debug( __METHOD__.' - ' . $voFile->getter('id') );

    // Borramos imagenes en cache
    Cogumelo::debug( __METHOD__.' - fileImageCtrl->clearCache '.$voFile->getter('id') );
    filedata::load('controller/FiledataImagesController.php');
    $fileImageCtrl = new FiledataImagesController();
    $fileImageCtrl->clearCache( $voFile->getter('id') );

    // Borramos el fichero real
    $serverFile = $this->filesAppPath.$voFile->getter('absLocation');
    Cogumelo::debug( __METHOD__.' - unlink '.$serverFile );
    $unlinkStatus = 'FAIL: Not filesAppPathPlain';
    if( !empty( $this->filesAppPathPlain ) ) {
      $serverFilePlain = realpath( $serverFile );
      $unlinkStatus = 'FAIL: serverFilePlain='.$serverFilePlain;
      if( !empty( $serverFilePlain ) && strpos ( $serverFilePlain, $this->filesAppPathPlain ) === 0 ) {
        $unlinkStatus = 'FAIL: Not valid file';
        if( file_exists( $serverFile ) && is_file( $serverFile ) ) {
          $unlinkStatus = 'FAIL: unlink';
          if( unlink( $serverFile ) ) {
            $unlinkStatus = 'DONE ';
          }
        }
      }
    }
    Cogumelo::debug( __METHOD__.' - unlink '.$serverFile.' ('.$unlinkStatus.')' );
    error_log( __METHOD__.' - BORRAR '.$serverFilePlain.' ('.$unlinkStatus.')' );
  } // function removeServerFiles( $voFile )


  /**
    Crea un nombre de fichero seguro a partir del nombre de fichero deseado
    @param string $fileName Nombre del campo
    @return string
   */
  public function secureFileName( $fileName ) {
    // Cogumelo::debug( __METHOD__.' - '.$fileName );
    $maxLength = 200;

    // "Aplanamos" caracteres no ASCII7
    $fileName = str_replace( $this->replaceAcents[ 'from' ], $this->replaceAcents[ 'to' ], $fileName );
    // Solo admintimos a-z A-Z 0-9 - / El resto pasan a ser -
    $fileName = preg_replace( '/[^a-z0-9_\-\.]/iu', '_', $fileName );
    // Eliminamos - sobrantes
    $fileName = preg_replace( '/__+/u', '_', $fileName );
    $fileName = trim( $fileName, '_' );

    $sobran = mb_strlen( $fileName, 'UTF-8' ) - $maxLength;
    if( $sobran < 0 ) {
      $sobran = 0;
    }

    $tmpExtPos = mb_strrpos( $fileName, '.' );
    if( $tmpExtPos > 0 && ( $tmpExtPos - $sobran ) >= 8 ) {
      // Si hay extensión y al cortar el nombre quedan 8 o más letras, recorto solo el nombre
      $tmpName = mb_substr( $fileName, 0, $tmpExtPos - $sobran );
      $tmpExt = mb_substr( $fileName, 1 + $tmpExtPos );
      $fileName = $tmpName . '.' . $tmpExt;
    }
    else {
      // Recote por el final
      $fileName = mb_substr( $fileName, 0, $maxLength );
    }

    // Cogumelo::debug( __METHOD__.' - RET: '.$fileName );

    return $fileName;
  } // function secureFileName()





  /**
    Busca elementos abandonados
    @param array $params Parametros
    @return bool
   */
  public function garbageCollection() {
    Cogumelo::debug( __METHOD__ );
    error_log( __METHOD__ );

    $modelName = 'FiledataModel';

    // // Importante: Precargamos los modelos
    // VOUtils::listVOs();

    require_once( ModuleController::getRealFilePath( 'GarbageCollection.php', 'GarbageCollection' ) );
    GarbageCollection::load( 'controller/GarbageCollectionController.php' );
    $garbageCollCtrl = new GarbageCollectionController();

    $idFilesUsed = $garbageCollCtrl->listModelUsedIds( $modelName );

    $diskFiles = $this->garbCollListDiskFiles();
    $dBFiles = $this->garbCollListDBFiles();
    // $idFilesUsed = $this->garbCollListIdFilesUsed();

    $diskFilesOrphan = $diskFiles;
    $dbFilesOrphan = $dBFiles;
    $dbRefersOrphan = [];

    foreach( $dbFilesOrphan as $objId => $objAbsLocation ) {
      if( !isset( $idFilesUsed[ $objId ] ) ) {
        $dbRefersOrphan[] = $objId; // Sobra por no estar referenciado
      }
      else {
        $keyList = array_search( $objAbsLocation, $diskFilesOrphan, true );
        if( $keyList !== false ) {
          unset( $diskFilesOrphan[ $keyList ] ); // Existe en bbdd ...OK
          unset( $dbFilesOrphan[ $objId ] ); // Existe en disco ...OK
        }
      }
    }

    error_log("\n\nSobran no disco por non ter Filedata ou con Filedata sen refer: ".count($diskFilesOrphan)." \n");
    // error_log(implode( "\n", $diskFilesOrphan ));

    foreach( $diskFilesOrphan as $fileOrphan ) {
      error_log('unlink("'.$fileOrphan.'") - non ten FiledataModel');
      // error_log('Borrando '.$this->filesAppPath.'/'.$fileOrphan);
      // unlink( $this->filesAppPath.'/'.$fileOrphan );
    }

    error_log("\n\nSobran na base de datos por non ter ficheiro ou refer: ".count($dbFilesOrphan)." \n");
    error_log(implode( ', ', array_keys( $dbFilesOrphan ) ));

    filedata::load('model/FiledataModel.php');
    foreach( array_keys( $dbFilesOrphan ) as $idFiledata ) {
      error_log('FiledataModel('.$idFiledata.')->delete() - non ten Pai');
      // $objModel = new FiledataModel( [ 'id' => $idFiledata ] );
      // $objModel->delete();
    }

    // error_log("\n\nSobran na base de datos por non ter refer: ".count($dbRefersOrphan)." \n");
    // error_log(implode( ', ', $dbRefersOrphan ));

    error_log("\n\nFALTAN na base de datos por ter refer e NON Filedata:\n");
    error_log(implode( ', ', array_diff( array_keys($idFilesUsed), array_keys($dBFiles) ) ));


    //   RESUMO:
    // Sobran no disco por non ter Filedata: 374
    // Sobran na base de datos por non ter ficheiro ou refer: 356


    error_log("\n\n  RESUMO:\n");
    error_log("Sobran no disco por non ter Filedata: ".count($diskFilesOrphan)." \n");
    error_log("Sobran na base de datos por non ter ficheiro ou refer: ".count($dbFilesOrphan)." \n");
  } // function garbageCollection()

  private function garbCollListDBFiles() {
    // Cogumelo::debug( __METHOD__ );
    $dBFiles = [];

    filedata::load('model/FiledataModel.php');
    $objModel = new FiledataModel();
    $listModel = $objModel->listItems();
    if( is_object( $listModel ) ) {
      while( $filedataObj = $listModel->fetch() ) {
        $dBFiles[ $filedataObj->getter('id') ] = ltrim( $filedataObj->getter('absLocation'), '/' );
      }
    }

    return $dBFiles;
  }

  private function garbCollListDiskFiles() {
    // Cogumelo::debug( __METHOD__ );

    $diskFiles = $this->lsDirRec( $this->filesAppPath );
    foreach( $diskFiles as $key => $fich ) {
      if( strpos( $fich, 'public/' ) === 0 || strpos( $fich, '/' ) === false ) {
        unset( $diskFiles[ $key ] ); // Estos son especiales ...saltamolos
      }
    }

    return $diskFiles;
  }

  private function lsDirRec( $dir ) {
    // Cogumelo::debug( __METHOD__.' - '. $dir );

    $list = [];

    $dir = rtrim( $dir, '/' );
    if( is_dir( $dir ) ) {
      $dirElements = scandir( $dir );

      if( !empty( $dirElements ) ) {
        foreach( $dirElements as $dirElem ) {
          if( $dirElem !== '.' && $dirElem !== '..' ) {
            if( is_dir( $dir.'/'.$dirElem ) ) {
              $listSub = $this->lsDirRec( $dir.'/'.$dirElem );
              if( !empty( $listSub ) ) {
                foreach( $listSub as $listSubElem ) {
                  $list[] = $dirElem.'/'.$listSubElem;
                }
              }
            }
            else {
              $list[] = $dirElem;
            }
          }
        }
      }
      reset( $dirElements );
    }

    return $list;
  }
} // FiledataController
