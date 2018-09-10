<?php
// common::autoIncludes();
// form::autoIncludes();


/**
 * Gestión de ficheros en formularios. Subir o borrar ficheros en campos de formulario.
 *
 * @package Module Form
 *
 * PHPMD: Suppress all warnings from these rules.
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 **/
class FormConnectorFiles {

  public function uploadFormFile( $post, $phpFiles ) {
    Cogumelo::debug(__METHOD__);
    // error_log(__METHOD__);

    // error_log('FormConnector: FILES:' ); error_log( print_r( $phpFiles, true ) );
    // error_log('FormConnector: POST:' ); error_log( print_r( $post, true ) );

    $form = new FormController();

    $idForm = isset( $post['idForm'] ) ? $post['idForm'] : false;
    $moreInfo = [ 'idForm' => $idForm ];

    if( isset( $post['cgIntFrmId'], $post['fieldName'], $phpFiles['ajaxFileUpload'] ) ) {

      $cgIntFrmId = $post['cgIntFrmId'];
      $fieldName  = $post['fieldName'];
      $moreInfo['cgIntFrmId'] = $cgIntFrmId;
      $moreInfo['fieldName'] = $fieldName;

      $tnProfile  = isset( $post['tnProfile'] ) ? $post['tnProfile'] : false;


      Cogumelo::debug(__METHOD__.': FILES:'.$phpFiles['ajaxFileUpload']['name'] );
      // error_log(__METHOD__.': FILES:'.$phpFiles['ajaxFileUpload']['name'] );
      $fileTmpLoc   = $phpFiles['ajaxFileUpload']['tmp_name']; // File in the PHP tmp folder
      $fileName     = $phpFiles['ajaxFileUpload']['name'];     // The file name
      $fileType     = $phpFiles['ajaxFileUpload']['type'];     // The type of file it is
      $fileSize     = $phpFiles['ajaxFileUpload']['size'];     // File size in bytes
      $fileErrorId  = $phpFiles['ajaxFileUpload']['error'];    // UPLOAD_ERR_OK o errores


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
            error_log('FormConnector: ALERTA: Los MIME_TYPE reportados por el navegador y PHP difieren: '.
              $fileType.' != '.$fileTypePhp );
            error_log('FormConnector: ALERTA: Damos preferencia a PHP. Puede variar la validación JS/PHP' );
            $fileType = $fileTypePhp;
          }
        }
        else {
          error_log('FormConnector: ALERTA: Imposible obtener el MIME_TYPE del fichero. Nos fiamos del navegador: '.$fileType );
        }
      }

      if( !$form->existErrors() ) {

        // Recuperamos formObj y validamos el fichero temporal
        if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' ) {
          // error_log(__METHOD__.' FORM CARGADO');

          $idForm = $form->getId();

          // Guardamos los datos previos del campo
          $fileFieldValuePrev = $form->getFieldValue( $fieldName );
          // error_log('FormConnector: LEEMOS File Field: '.print_r($fileFieldValuePrev,true) );

          // Creamos un objeto temporal para validarlo
          $tmpFileFieldValue = [
            'status' => 'LOAD',
            'validate' => [
              'partial' => true,
              'name' => $fileName,
              'originalName' => $fileName,
              'absLocation' => $fileTmpLoc,
              'type' => $fileType,
              'size' => $fileSize
            ]
          ];

          // Almacenamos los datos temporales en el formObj para validarlos
          $form->setFieldValue( $fieldName, $tmpFileFieldValue );
          // Validar input del fichero
          $form->validateField( $fieldName );

          if( !$form->existErrors() ) {

            // Posible separacion a un metodo
            // $this->uploadFormFileSave( $form, $fileTmpLoc, $fileName, $fieldName, $fileType, $fileSize );

            // El fichero ha superado las validaciones. Ajustamos sus valores finales y los almacenamos.
            Cogumelo::debug('FormConnector: FU: Validado. Vamos a moverlo...' );


            $tmpCgmlFileLocation = $form->tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName, $fieldName );

            if( $tmpCgmlFileLocation === false ) {
              Cogumelo::debug('FormConnector: FU: Fallo de move_uploaded_file movendo '.$fieldName.': ('.$fileTmpLoc.')' );
              $form->addFieldRuleError( $fieldName, 'cogumelo',
                'La subida del fichero ha fallado. (MU)' );
            }
            else {
              // El fichero subido ha pasado todos los controles. Vamos a registrarlo según proceda
              Cogumelo::debug('FormConnector: FU: Validado y movido. Paso final...' );

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
                    Cogumelo::debug('FormConnector: FU: Todo OK. Estado REPLACE...' );

                    $newFileFieldValue['status'] = 'REPLACE';
                    $fileFieldValuePrev = $newFileFieldValue;
                  }
                  else {
                    Cogumelo::debug('FormConnector: FU: Validado pero status erroneo: ' . $fileFieldValuePrev['status'] );
                    $form->addFieldRuleError( $fieldName, 'cogumelo',
                      'La subida del fichero ha fallado. (FE)' );
                  }
                }
                else {
                  Cogumelo::debug('FormConnector: FU: Todo OK. Estado LOAD...' );

                  $fileFieldValuePrev = $newFileFieldValue;
                }
              }
              else {
                // Multiple: add files
                Cogumelo::debug('FormConnector: FU: Todo OK. Multifile LOAD...' );
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
                Cogumelo::debug('FormConnector: FU: OK con el ficheiro subido... Se persiste...' );
                error_log(__METHOD__.' OK con el ficheiro subido... Se persiste...');
                // error_log('FormConnector: GUARDAMOS File Field: '.print_r($fileFieldValuePrev,true) );
                $form->setFieldValue( $fieldName, $fileFieldValuePrev );
                // Persistimos formObj para cuando se envíe el formulario completo
                $form->saveToSession();
              }
              else {
                Cogumelo::debug('FormConnector: FU: Como ha fallado, eliminamos: ' . $tmpCgmlFileLocation );
                error_log(__METHOD__.' Como ha fallado, eliminamos: ' . $tmpCgmlFileLocation );
                unlink( $tmpCgmlFileLocation );
              }
            } // else - if( !$tmpCgmlFileLocation )
          } // if( !$form->existErrors() )
          else {
            // El fichero NO ha superado las validaciones.
            // Los errores ya estan cargados en FORM
            Cogumelo::debug('FormConnector: FU: NON Valida o ficheiro subido...' );
          }

        } // if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' )
        else {
          $form->addFieldRuleError( $fieldName, 'cogumelo',
            'La subida del fichero ha fallado. (FO)' );
        }
      } // if( !$error ) // Recuperamos formObj y validamos el fichero temporal
    } // if( isset( ... ) )
    else { // no parece haber fichero
      if( isset( $post['fieldName'] ) ) {
        $form->addFieldRuleError( $post['fieldName'], 'cogumelo',
          'La subida del fichero ha fallado. (IS)' );
      }
      else {
        $form->addFormError( 'La subida del fichero ha fallado. (IS2)', 'formError' );
      }
    }


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

      if( !empty( $tnProfile ) /*&& mb_strpos( $moreInfo['fileType'], 'image' ) === 0*/ ) {
        Cogumelo::debug('FormConnector: VAMOS A CREAR fileSrcTn' );

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



  public function deleteFormFile( $post ) {
    Cogumelo::debug(__METHOD__);
    // error_log(__METHOD__);

    // error_log('FormConnector: POST:' );
    // error_log('FormConnector: '. print_r( $post, true ) );

    $form = new FormController();

    $idForm = isset( $post['idForm'] ) ? $post['idForm'] : false;
    $moreInfo = [ 'idForm' => $idForm ];


    if( isset( $post['cgIntFrmId'], $post['fieldName'] ) ) {

      $cgIntFrmId = $post['cgIntFrmId'];
      $fieldName = $post['fieldName'];
      $moreInfo['cgIntFrmId'] = $cgIntFrmId;
      $moreInfo['fieldName'] = $fieldName;


      // Recuperamos formObj y validamos el fichero temporal
      if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' ) {

        $idForm = $form->getId();

        // Cargamos los datos previos del campo
        $fieldPrev = $form->getFieldValue( $fieldName );

        $fileGroup = false;
        $multipleFileField = false;
        $multipleIndex = false;
        if( $fieldPrev['status'] === 'GROUP' ) {
          // Necesitamos informacion extra porque es un grupo de ficheros
          $multipleFileField = true;

          if( isset($fieldPrev['idGroup']) ) {
            $fileGroup = $fieldPrev['idGroup'];
          }

          if( isset( $post['fileTempId'] ) ) {
            $multipleIndex = $post['fileTempId'];
          }
          else {
            $multipleIndex = 'FID_'.$post['fileId'];
          }

          if( isset( $fieldPrev['multiple'][ $multipleIndex ] ) ) {
            $fieldPrev = $fieldPrev['multiple'][ $multipleIndex ];
          }
          else {
            $fieldPrev = false;
          }
        }


        Cogumelo::debug('FormConnector: LEEMOS File Field para BORRAR: '.json_encode( $fieldPrev ) );


        if( isset( $fieldPrev['status'] ) && $fieldPrev['status'] !== false ) {
          switch( $fieldPrev['status'] ) {
            case 'LOAD':
              // error_log('FormConnector: FDelete: LOAD - Borramos: '.$fieldPrev['temp']['absLocation'] );

              // Garbage collector
              // unlink( $fieldPrev['temp']['absLocation'] );
              $fieldPrev = null;
              break;
            case 'EXIST':
              // error_log('FormConnector: FDelete: EXIST - Marcamos para borrar: '.$fieldPrev['prev']['absLocation'] );

              $fieldPrev['status'] = 'DELETE';
              break;
            case 'REPLACE':
              // error_log('FormConnector: FDelete: REPLACE - Borramos: '.$fieldPrev['temp']['absLocation'] );

              $fieldPrev['status'] = 'DELETE';
              // Garbage collector
              // unlink( $fieldPrev['temp']['absLocation'] );
              $fieldPrev['temp'] = null;
              break;
            default:
              // error_log('FormConnector: FDelete: Intentando borrar con status erroneo: ' . $fieldPrev['status'] );

              $form->addFieldRuleError( $fieldName, 'cogumelo',
                'Intento de sobreescribir un fichero existente (STB)' );
              break;
          }
        }
        else {
          error_log('FormConnector: FDelete: Error intentando eliminar un fichero sin estado.' );
          $form->addFieldRuleError( $fieldName, 'cogumelo',
            'Intento de borrar un fichero inexistente (STN)' );
        }

        if( !$form->existErrors() ) {
          // error_log('FormConnector: FDelete: OK. Guardando el nuevo estado... Se persiste...' . $fieldPrev['status'] );



          if( $multipleFileField ) {
            $fieldNew = $fieldPrev;
            $fieldPrev = $form->getFieldValue( $fieldName );
            if( $fieldNew !== null ) {
              $fieldPrev['multiple'][ $multipleIndex ] = $fieldNew;
            }
            else {
              unset( $fieldPrev['multiple'][ $multipleIndex ] );
            }
          }



          Cogumelo::debug('FormConnector: GUARDAMOS File Field: '.$fieldName );

          $form->setFieldValue( $fieldName, $fieldPrev );
          // Persistimos formObj para cuando se envíe el formulario completo
          $form->saveToSession();
        }
        else {
          error_log('FormConnector: FDelete: El borrado ha fallado. Se mantiene el estado.' );
        }
      } // if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' )
      else {
        $form->addFieldRuleError( $fieldName, 'cogumelo',
          'Los datos del fichero no han llegado bien al servidor. (FRM)' );
      }
    } // if( isset( ... ) )
    else { // no parece haber fichero
      if( isset( $post['fieldName'] ) ) {
        $form->addFieldRuleError( $post['fieldName'], 'cogumelo',
          'No han llegado los datos o lo ha hecho con errores. (ISE)' );
      }
      else {
        $form->addFormError( 'No han llegado los datos o lo ha hecho con errores. (ISE2)', 'formError' );
      }
    }











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
}
