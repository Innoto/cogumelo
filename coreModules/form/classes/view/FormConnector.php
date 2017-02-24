<?php


Cogumelo::load('coreView/View.php');
common::autoIncludes();
form::autoIncludes();


/**
 * Gestión de ficheros en formularios. Subir o borrar ficheros en campos de formulario.
 *
 * @package Module Form
 **/
class FormConnector extends View {

  public function __construct( $base_dir ) {
    parent::__construct( $base_dir );
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  public function accessCheck() {

    return true;
  }


  public function execCommand() {
    if( isset( $_POST['execute'] ) ) {
      switch( $_POST['execute'] ) {
        case 'keepAlive':
          $this->keepAlive();
          break;
        case 'removeGroupElement':
          $this->removeGroupElement();
          break;
        case 'getGroupElement':
          $this->getGroupElement();
          break;
        default:
          error_log( 'ERROR - FormConnector::execCommand - Comando no soportado: '.$_POST['execute'] );
          break;
      }
    }
    else {
      error_log( 'ERROR - FormConnector::execCommand - Datos erroneos' );
    }
  }


  public function keepAlive() {
    error_log( '(Notice) FormConnector::keepAlive' );

    $form = new FormController();
    $error = false;

    if( isset( $_POST['cgIntFrmId'] ) && $form->loadFromSession( $_POST['cgIntFrmId'] ) ) {
      $form->saveToSession();
    }
    else { // no parece haber fichero
      $form->addFormError( 'No existe el form' );
    }

    $moreInfo = array(
      'cgIntFrmId' => $_POST['cgIntFrmId']
    );

    // Notificamos el resultado al UI
    $form->sendJsonResponse( $moreInfo );
  }




  // addUrlPatterns( '#^cgml-form-file-upload$#', 'view:FormConnector::fileUpload' );
  public function fileUpload() {
    if( isset( $_POST['execute'] ) && $_POST['execute'] === 'delete' ) {
      $this->deleteFormFile();
    }
    else {
      $this->uploadFormFile();
    }
  }
















  private function uploadFormFile() {
    error_log( '--------------------------------' );
    error_log( ' FormConnector - uploadFormFile ' );
    error_log( '--------------------------------' );

    $form = new FormController();
    $error = false;

    $idForm = isset( $_POST['idForm'] ) ? $_POST['idForm'] : false;

    error_log( 'FILES:'.$_FILES['ajaxFileUpload']['name'] );
    // error_log( 'FILES:' ); error_log( print_r( $_FILES, true ) );
    // error_log( 'POST:' ); error_log( print_r( $_POST, true ) );

    if( isset( $_POST['cgIntFrmId'], $_POST['fieldName'], $_FILES['ajaxFileUpload'] ) ) {

      $cgIntFrmId = $_POST['cgIntFrmId'];
      $fieldName  = $_POST['fieldName'];
      $fieldName  = $_POST['fieldName'];

      $tnProfile  = isset( $_POST['tnProfile'] ) ? $_POST['tnProfile'] : false;


      $fileTmpLoc   = $_FILES['ajaxFileUpload']['tmp_name']; // File in the PHP tmp folder
      $fileName     = $_FILES['ajaxFileUpload']['name'];     // The file name
      $fileType     = $_FILES['ajaxFileUpload']['type'];     // The type of file it is
      $fileSize     = $_FILES['ajaxFileUpload']['size'];     // File size in bytes
      $fileErrorId  = $_FILES['ajaxFileUpload']['error'];    // UPLOAD_ERR_OK o errores


      // Aviso de error PHP
      if( $fileErrorId !== UPLOAD_ERR_OK ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo',
          'La subida del fichero ha fallado. (SF-'.$fileErrorId.')' );
        // $form->addFieldRuleError( $fieldName, 'cogumelo', $this->getFileErrorMsg( $fileErrorId ) );
      }

      // Datos enviados fuera de rango
      if( !$form->existErrors() && $fileSize < 1 ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo',
          'La subida del fichero ha fallado. (T0)' );
      }

      // Verificando la existencia y tamaño del fichero intermedio
      if( !$form->existErrors() && ( !is_uploaded_file( $fileTmpLoc ) || filesize( $fileTmpLoc ) !== $fileSize ) ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo',
          'La subida del fichero ha fallado. (T1)' );
      }

      // Verificando el MIME_TYPE del fichero intermedio
      if( !$form->existErrors() ) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $fileTypePhp = finfo_file( $finfo, $fileTmpLoc );
        if( $fileTypePhp !== false ) {
          if( $fileType !== $fileTypePhp ) {
            error_log( 'ALERTA: Los MIME_TYPE reportados por el navegador y PHP difieren: '.
              $fileType.' != '.$fileTypePhp );
            error_log( 'ALERTA: Damos preferencia a PHP. Puede variar la validación JS/PHP' );
            $fileType = $fileTypePhp;
          }
        }
        else {
          error_log( 'ALERTA: Imposible obtener el MIME_TYPE del fichero. Nos fiamos del navegador: '.$fileType );
        }
      }

      if( !$form->existErrors() ) {

        // Recuperamos formObj y validamos el fichero temporal
        if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' ) {

          $idForm = $form->getId();

          // Guardamos los datos previos del campo
          $fileFieldValuePrev = $form->getFieldValue( $fieldName );




          error_log( 'LEEMOS File Field: '.print_r($fileFieldValuePrev,true) );


          // Creamos un objeto temporal para validarlo
          $tmpFileFieldValue = array(
            'status' => 'LOAD',
            'validate' => array(
              'name' => $fileName,
              'originalName' => $fileName,
              'absLocation' => $fileTmpLoc,
              'type' => $fileType,
              'size' => $fileSize
            )
          );





          // Almacenamos los datos temporales en el formObj para validarlos
          $form->setFieldValue( $fieldName, $tmpFileFieldValue );
          // Validar input del fichero
          $form->validateField( $fieldName );





          if( !$form->existErrors() ) {
            // El fichero ha superado las validaciones. Ajustamos sus valores finales y los almacenamos.
            error_log( 'FU: Validado. Vamos a moverlo...' );


            $tmpCgmlFileLocation = $form->tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName, $fieldName );

            if( $tmpCgmlFileLocation === false ) {
              error_log( 'FU: Fallo de move_uploaded_file movendo '.$fieldName.': ('.$fileTmpLoc.')' );
              $form->addFieldRuleError( $fieldName, 'cogumelo',
                'La subida del fichero ha fallado. (MU)' );
            }
            else {
              // El fichero subido ha pasado todos los controles. Vamos a registrarlo según proceda
              error_log( 'FU: Validado y movido. Paso final...' );

              $newFileFieldValue = [
                'status' => 'LOAD',
                'temp' => [
                  'name' => $fileName,
                  'originalName' => $fileName,
                  'absLocation' => $tmpCgmlFileLocation,
                  'type' => $fileType,
                  'size' => $fileSize
                ]
              ];

              if( !$form->getFieldParam( $fieldName, 'multiple' ) ) {
                // Basic: only one file
                if( isset( $fileFieldValuePrev['status'] ) && $fileFieldValuePrev['status'] !== false ) {
                  if( $fileFieldValuePrev['status'] === 'DELETE' ) {
                    error_log( 'FU: Todo OK. Estado REPLACE...' );

                    $newFileFieldValue['status'] = 'REPLACE';
                    $fileFieldValuePrev = $newFileFieldValue;
                  }
                  else {
                    error_log( 'FU: Validado pero status erroneo: ' . $fileFieldValuePrev['status'] );
                    $form->addFieldRuleError( $fieldName, 'cogumelo',
                      'La subida del fichero ha fallado. (FE)' );
                  }
                }
                else {
                  error_log( 'FU: Todo OK. Estado LOAD...' );

                  $fileFieldValuePrev = $newFileFieldValue;
                }
              }
              else {
                // Multiple: add files
                error_log( 'FU: Todo OK. Multifile LOAD...' );
                if( !isset( $fileFieldValuePrev['multiple'] ) ) {
                  $fileFieldValuePrev['multiple'] = [];
                  if( isset( $fileFieldValuePrev['status'] ) ) {
                    $fileFieldValuePrev['multiple'] = [ $fileFieldValuePrev ];
                  }
                }
                $preKeys = array_keys( $fileFieldValuePrev['multiple'] );
                $fileFieldValuePrev['multiple'][] = $newFileFieldValue;
                $newKeys = array_diff( array_keys( $fileFieldValuePrev['multiple'] ), $preKeys );
                $newKey = array_shift( $newKeys );
                $newFileFieldValue['temp']['tempId'] = $newKey;
                $fileFieldValuePrev['multiple'][ $newKey ]['temp']['tempId'] = $newKey;
              }

              if( !$form->existErrors() ) {
                error_log( 'FU: OK con el ficheiro subido... Se persiste...' );




                error_log( 'GUARDAMOS File Field: '.print_r($fileFieldValuePrev,true) );


                $form->setFieldValue( $fieldName, $fileFieldValuePrev );
                // Persistimos formObj para cuando se envíe el formulario completo
                $form->saveToSession();
              }
              else {
                error_log( 'FU: Como ha fallado, eliminamos: ' . $tmpCgmlFileLocation );
                unlink( $tmpCgmlFileLocation );
              }


            } // else - if( !$tmpCgmlFileLocation )
          } // if( !$form->existErrors() )
          else {
            // El fichero NO ha superado las validaciones.
            // Los errores ya estan cargados en FORM
            error_log( 'FU: NON Valida o ficheiro subido...' );
          }

        } // if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' )
        else {
          $form->addFieldRuleError( $fieldName, 'cogumelo',
            'La subida del fichero ha fallado. (FO)' );
        }
      } // if( !$error ) // Recuperamos formObj y validamos el fichero temporal
    } // if( isset( ... ) )
    else { // no parece haber fichero
      $form->addFieldRuleError( $_POST['fieldName'], 'cogumelo',
        'La subida del fichero ha fallado. (IS)' );
    }

    $moreInfo = array(
      'idForm' => $idForm,
      'cgIntFrmId' => $_POST['cgIntFrmId'],
      'fieldName' => $_POST['fieldName']
    );
    if( !$form->existErrors() ) {
      $moreInfo['fileName'] = $newFileFieldValue['temp']['name'];
      $moreInfo['fileSize'] = $newFileFieldValue['temp']['size'];
      $moreInfo['fileType'] = $newFileFieldValue['temp']['type'];
      if( isset( $newFileFieldValue['temp']['tempId'] ) ) {
        $moreInfo['tempId'] = $newFileFieldValue['temp']['tempId'];
      }
      else {
        $moreInfo['tempId'] = false;
      }

      if( $tnProfile && strpos( $moreInfo['fileType'], 'image' ) === 0 ) {
        error_log( 'image='.strpos( $moreInfo['fileType'], 'image' ) );
        error_log( 'VAMOS A CREAR fileSrcTn' );
        filedata::load('controller/FiledataImagesController.php');
        $filedataImagesCtrl = new FiledataImagesController();
        $filedataImagesCtrl->setProfile( $tnProfile );
        $moreInfo['fileSrcTn'] = $filedataImagesCtrl->createImageProfile(
          $newFileFieldValue['temp']['absLocation'], false, true );
      }
    }

    // Notificamos el resultado al UI
    $form->sendJsonResponse( $moreInfo );

  } // function uploadFormFile() {


















  private function deleteFormFile() {
    error_log( '--------------------------------' );
    error_log( ' FormConnector - deleteFormFile ' );
    error_log( '--------------------------------' );

    $form = new FormController();
    $error = false;

    // error_log( 'POST:' );
    // error_log( print_r( $_POST, true ) );

    $idForm = isset( $_POST['idForm'] ) ? $_POST['idForm'] : false;

    if( isset( $_POST['cgIntFrmId'], $_POST['fieldName'] ) ) {

      $cgIntFrmId = $_POST['cgIntFrmId'];
      $fieldName = $_POST['fieldName'];


      // Recuperamos formObj y validamos el fichero temporal
      if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' ) {

        $idForm = $form->getId();

        // Cargamos los datos previos del campo
        $fieldPrev = $form->getFieldValue( $fieldName );

        $fileGroup = false;
        if( $fieldPrev['status'] === 'GROUP' ) {
          // Necesitamos informacion extra porque es un grupo de ficheros
          $fileGroup = $fieldPrev['idGroup'];

          if( isset( $_POST['fileTempId'] ) ) {
            $multipleIndex = $_POST['fileTempId'];
          }
          else {
            $multipleIndex = 'FID_'.$_POST['fileId'];
          }

          if( isset( $fieldPrev['multiple'][ $multipleIndex ] ) ) {
            $fieldPrev = $fieldPrev['multiple'][ $multipleIndex ];
          }
          else {
            $fieldPrev = false;
          }
        }



        error_log( 'LEEMOS File Field para BORRAR: '.print_r( $fieldPrev, true ) );



        if( isset( $fieldPrev['status'] ) && $fieldPrev['status'] !== false ) {
          switch( $fieldPrev['status'] ) {
            case 'LOAD':
              // error_log( 'FDelete: LOAD - Borramos: '.$fieldPrev['temp']['absLocation'] );

              // Garbage collector
              // unlink( $fieldPrev['temp']['absLocation'] );
              $fieldPrev = null;
              break;
            case 'EXIST':
              // error_log( 'FDelete: EXIST - Marcamos para borrar: '.$fieldPrev['prev']['absLocation'] );

              $fieldPrev['status'] = 'DELETE';
              break;
            case 'REPLACE':
              // error_log( 'FDelete: REPLACE - Borramos: '.$fieldPrev['temp']['absLocation'] );

              $fieldPrev['status'] = 'DELETE';
              // Garbage collector
              // unlink( $fieldPrev['temp']['absLocation'] );
              $fieldPrev['temp'] = null;
              break;
            default:
              // error_log( 'FDelete: Intentando borrar con status erroneo: ' . $fieldPrev['status'] );

              $form->addFieldRuleError( $fieldName, 'cogumelo',
                'Intento de sobreescribir un fichero existente' );
              break;
          }
        }
        else {
          error_log( 'FDelete: Error intentando eliminar un fichero sin estado.' );

          $form->addFieldRuleError( $fieldName, 'cogumelo',
            'Intento de borrar un fichero inexistente' );
        }

        if( !$form->existErrors() ) {
          // error_log( 'FDelete: OK. Guardando el nuevo estado... Se persiste...' . $fieldPrev['status'] );



          if( $fileGroup ) {
            $fieldNew = $fieldPrev;
            $fieldPrev = $form->getFieldValue( $fieldName );
            $fieldPrev['multiple'][ $multipleIndex ] = $fieldNew;
          }



          error_log( 'GUARDAMOS File Field: '.print_r($fieldPrev,true) );

          $form->setFieldValue( $fieldName, $fieldPrev );
          // Persistimos formObj para cuando se envíe el formulario completo
          $form->saveToSession();
        }
        else {
          error_log( 'FDelete: El borrado ha fallado. Se mantiene el estado.' );
        }

      } // if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' )
      else {
        $form->addFieldRuleError( $fieldName, 'cogumelo',
          'Los datos del fichero no han llegado bien al servidor. FORM' );
      }
    } // if( isset( ... ) )
    else { // no parece haber fichero
      $form->addFieldRuleError( $_POST['fieldName'], 'cogumelo',
        'No han llegado los datos o lo ha hecho con errores. ISSET' );
    }


    $moreInfo = array(
      'idForm' => $idForm,
      'cgIntFrmId' => $_POST['cgIntFrmId'],
      'fieldName' => $_POST['fieldName']
    );

    // Notificamos el resultado al UI
    $form->sendJsonResponse( $moreInfo );

  } // function deleteFormFile() {

  /**
   * Obtiene el texto de error en funcion del codigo
   * @param integer $fileErrorId
   * @return string $msgError
   **/
  private function getFileErrorMsg( $fileErrorId ) {
    $msgError = '';

    // Aviso de error PHP
    switch( $fileErrorId ) {
      case UPLOAD_ERR_OK:
        // OK, no hay error
        break;
      case UPLOAD_ERR_INI_SIZE:
        $msgError = 'El tamaño del fichero ha superado el límite establecido en el servidor.';
        break;
      case UPLOAD_ERR_FORM_SIZE:
        $msgError = 'El tamaño del fichero ha superado el límite establecido para este campo.';
        break;
      case UPLOAD_ERR_PARTIAL:
        $msgError = 'La subida del fichero no se ha completado.';
        break;
      case UPLOAD_ERR_NO_FILE:
        $msgError = 'No se ha subido el fichero.';
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $msgError = 'La subida del fichero ha fallado. (6)';
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $msgError = 'La subida del fichero ha fallado. (7)';
        break;
      case UPLOAD_ERR_EXTENSION:
        $msgError = 'La subida del fichero ha fallado. (8)';
        break;
      default:
        $msgError = 'La subida del fichero ha fallado.';
        break;
    }

    return $msgError;
  }






  private function getGroupElement() {
    // error_log( '---------------------------------' );
    // error_log( ' FormConnector - getGroupElement ' );
    // error_log( '---------------------------------' );

    $groupIdElem = false; // Id de la nueva instancia del grupo
    $htmlGroupElement = false; // HTML de la nueva instancia del grupo
    $validationRules = false; // Reglas de validacion de la nueva instancia del grupo

    $form = new FormController();

    if( isset( $_POST['cgIntFrmId'], $_POST['idForm'], $_POST['groupName'] ) ) {

      $cgIntFrmId = $_POST['cgIntFrmId'];
      $idForm     = $_POST['idForm'];
      $groupName  = $_POST['groupName'];

      // Recuperamos formObj y validamos el grupo
      if( $form->loadFromSession( $cgIntFrmId ) && $form->issetGroup( $groupName ) ) {

        $groupMax = $form->getGroupLimits( $groupName, 'max' );
        if( $groupMax > $form->countGroupElems( $groupName ) ) {

          $groupIdElem = $form->newGroupElem( $groupName );

          if( $groupIdElem !== false ) {
            $htmlGroupElement = $form->getHtmlGroupElement( $groupName, $groupIdElem );

            foreach( $form->getGroupFields( $groupName ) as $fieldName ) {
              $fieldRules = $form->getValidationRules( $fieldName );
              if( $fieldRules !== false ) {
                $validationRules[ $fieldName.'_C_'.$groupIdElem ] = $fieldRules;
              }
            }
          }
          else {
            $form->addGroupRuleError( false, 'cogumelo', 'Error creando un nuevo elemento.' );
          }
        }
        else {
          $form->addGroupRuleError( false, 'cogumelo',
            'Se ha alcanzado el número máximo de elementos permitidos: '.$groupMax );
        }

      }
      else {
        $form->addGroupRuleError( false, 'cogumelo', 'Los datos no son válidos.' );
      }

    } // if( isset( ... ) )
    else { // los datos no estan bien
      $form->addGroupRuleError( false, 'cogumelo', 'No han llegado los datos necesarios. (IS)' );
    }

    // Notificamos el resultado al UI
    $moreInfo = array( 'idForm' => $_POST['idForm'], 'groupName' => $_POST['groupName'] );
    if( !$form->existErrors() ) {
      $moreInfo[ 'groupIdElem' ] = $groupIdElem;
      $moreInfo[ 'htmlGroupElement' ] = $htmlGroupElement;
      $moreInfo[ 'validationRules' ] = $validationRules;
    }
    $form->sendJsonResponse( $moreInfo );
  }


  private function removeGroupElement() {
    // error_log( '------------------------------------' );
    // error_log( ' FormConnector - removeGroupElement ' );
    // error_log( '------------------------------------' );

    $form = new FormController();

    if( isset( $_POST['idForm'], $_POST['cgIntFrmId'], $_POST['groupName'], $_POST['groupIdElem'] ) ) {

      $idForm     = $_POST['idForm'];
      $cgIntFrmId = $_POST['cgIntFrmId'];
      $groupName  = $_POST['groupName'];
      $groupIdElem  = $_POST['groupIdElem'];

      // Recuperamos formObj y validamos el grupo
      if( $form->loadFromSession( $cgIntFrmId ) && $form->issetGroup( $groupName ) ) {

        $groupMin = $form->getGroupLimits( $groupName,  'min' );
        if( $groupMin < $form->countGroupElems( $groupName ) ) {

          if( !$form->removeGroupElem( $groupName, $groupIdElem ) ) {
            $form->addGroupRuleError( false, 'cogumelo',
              'Imposible eliminar el elemento. (' . $groupIdElem . ')' );
          }

        }
        else {
          $form->addGroupRuleError( false, 'cogumelo',
            'Se ha alcanzado el número mínimo de elementos permitidos: '.$groupMin );
        }

      }
      else {
        $form->addGroupRuleError( false, 'cogumelo', 'Los datos no son válidos.' );
      }

    } // if( isset( ... ) )
    else { // los datos no estan bien
      $form->addGroupRuleError( false, 'cogumelo', 'No han llegado los datos necesarios. (IS)' );
    }

    // Notificamos el resultado al UI
    $moreInfo = array( 'idForm' => $_POST['idForm'], 'groupName' => $_POST['groupName'], 'groupIdElem' => $_POST['groupIdElem'] );
    $form->sendJsonResponse( $moreInfo );
  }





  public function customCkeditorConfig() {
    $fileInfo = ModuleController::getRealFilePath( 'classes/view/templates/js/ckeditor-config.js', 'form' );

    header( 'Content-Type: application/javascript; charset=utf-8' );
    header( 'Content-Length: ' . filesize( $fileInfo ) );
    readfile( $fileInfo );
    exit;
  } // function customCkeditorConfig() {

} // class FormConnector extends View


/*

  pasos

  1.- Sube o ficheiro + ver que existe en tmp e ten tamaño
  http://php.net/manual/function.is-uploaded-file.php
  http://es1.php.net/manual/en/function.filesize.php
  Controlar upload_max_filesize e post_max_size
  To upload large files, this value must be larger than upload_max_filesize.
  If memory limit is enabled by your configure script, memory_limit also affects file uploading.
  Generally speaking, memory_limit should be larger than post_max_size.

  2.- Validadores - Se non valida, eliminar en form e en srv.
  http://php.net/manual/function.finfo-file.php
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $finfo->file($_FILES['upfile']['tmp_name'])

  3.- Establecer o seu destino temporal e definitivo: ruta e nome (evitando colisions)
  make sure that the file name not bigger than 250 characters.
  mb_strlen($filename,"UTF-8") > 225
  make sure the file name in English characters, numbers and (_-.) symbols.
  preg_match("`^[-0-9A-Z_\.]+$`i",$filename)
  http://php.net/manual/ini.core.php#ini.open-basedir
  http://php.net/pathinfo
  http://php.net/manual/function.chmod.php
  http://php.net/manual/function.move-uploaded-file.php
  http://php.net/manual/function.sha1-file.php

  4.- Gardar no obj FORM e voltalo a meter na sesion



  SEGURIDADE EXTERNA

  You can use .htaccess to stop working some scripts as in example php file in your upload path.
  use :
  AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
  Options -ExecCGI
*/

