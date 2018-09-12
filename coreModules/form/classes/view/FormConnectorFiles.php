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
    $fieldName = isset( $post['fieldName'] ) ? $post['fieldName'] : false;
    $tnProfile = isset( $post['tnProfile'] ) ? $post['tnProfile'] : false;
    $moreInfo = [ 'idForm' => $idForm ];

    if( isset( $post['cgIntFrmId'], $post['fieldName'], $phpFiles['ajaxFileUpload'] ) ) {
      $cgIntFrmId = $post['cgIntFrmId'];
      $moreInfo['cgIntFrmId'] = $cgIntFrmId;
      $moreInfo['fieldName'] = $fieldName;

      Cogumelo::debug(__METHOD__.': FILES:'.$phpFiles['ajaxFileUpload']['name'] );
      // error_log(__METHOD__.': FILES:'.$phpFiles['ajaxFileUpload']['name'] );
      $fich = [
        'tmpLoc'  => $phpFiles['ajaxFileUpload']['tmp_name'], // File in the PHP tmp folder
        'name'    => $phpFiles['ajaxFileUpload']['name'],     // The file name
        'type'    => $phpFiles['ajaxFileUpload']['type'],     // The type of file it is
        'size'    => $phpFiles['ajaxFileUpload']['size'],     // File size in bytes
        'errorId' => $phpFiles['ajaxFileUpload']['error'],    // UPLOAD_ERR_OK o errores
      ];

      $this->uploadFormFilePhpValidate( $form, $fieldName, $fich );

      if( !$form->existErrors() ) {
        // Recuperamos formObj, validamos y guardamos el fichero
        $newFileFieldValue = $this->uploadFormFileProcess( $form, $fieldName, $fich, $cgIntFrmId );
      }
    }
    else { // no parece haber fichero
      if( !empty( $fieldName ) ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (IS)' );
      }
      else {
        $form->addFormError( 'La subida del fichero ha fallado. (IS2)', 'formError' );
      }
    }


    if( !$form->existErrors() ) {
      $newVal = $newFileFieldValue['temp'];
      $moreInfo['fileName'] = $newVal['name'];
      $moreInfo['fileSize'] = $newVal['size'];
      $moreInfo['fileType'] = $newVal['type'];
      $moreInfo['tempId'] = isset( $newVal['tempId'] ) ? $newVal['tempId'] : false;

      if( !empty( $tnProfile ) ) {
        $moreInfo['fileSrcTn'] = $this->uploadFormFileThumbnail( $newVal['absLocation'], $tnProfile );
      }
    }


    // Notificamos el resultado al UI
    $form->sendJsonResponse( $moreInfo );
  } // function uploadFormFile() {

  public function uploadFormFilePhpValidate( $form, $fieldName, $fich ) {
    Cogumelo::debug(__METHOD__);
    // error_log(__METHOD__);

    // Aviso de error PHP
    if( $fich['errorId'] !== UPLOAD_ERR_OK ) {
      $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (SF-'.$fich['errorId'].')' );
    }

    // Verificando la existencia y tamaño del fichero intermedio
    if( !$form->existErrors() ) {
      if( $fich['size'] < 1 ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (T0)' );
      }
      elseif( !is_uploaded_file( $fich['tmpLoc'] ) ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (T1)' );
      }
      elseif( filesize( $fich['tmpLoc'] ) !== $fich['size'] ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (T2)' );
      }
    }

    // Verificando el MIME_TYPE del fichero intermedio
    if( !$form->existErrors() ) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
      $fileTypePhp = finfo_file( $finfo, $fich['tmpLoc'] );
      if( $fileTypePhp !== false ) {
        if( $fich['type'] !== $fileTypePhp ) {
          error_log('FormConnector: ALERTA: Los MIME_TYPE reportados por el navegador y PHP difieren: '.
            $fich['type'].' != '.$fileTypePhp );
          error_log('FormConnector: ALERTA: Damos preferencia a PHP. Puede variar la validación JS/PHP' );
          $fich['type'] = $fileTypePhp;
        }
      }
      else {
        error_log('FormConnector: ALERTA: MIME_TYPE PHP del fichero desconocido. Navegador: '.$fich['type'] );
      }
    }
  } // uploadFormFilePhpValidate( $form, $fieldName, $fich )

  public function uploadFormFileProcess( $form, $fieldName, $fich, $cgIntFrmId ) {
    Cogumelo::debug(__METHOD__);
    // error_log(__METHOD__);

    $newFileFieldValue = false;

    if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' ) {
      // error_log(__METHOD__.' FORM CARGADO');

      // Guardamos los datos previos del campo
      $fileFieldValuePrev = $form->getFieldValue( $fieldName );
      // error_log('FormConnector: LEEMOS File Field: '.print_r($fileFieldValuePrev,true) );

      // Almacenamos datos temporales en el formObj para validarlos
      $form->setFieldValue( $fieldName, [
        'status' => 'LOAD',
        'validate' => [ 'partial' => true, 'name' => $fich['name'], 'originalName' => $fich['name'],
          'absLocation' => $fich['tmpLoc'], 'type' => $fich['type'], 'size' => $fich['size'] ]
      ] );
      $form->validateField( $fieldName );

      if( !$form->existErrors() ) {
        // El fichero ha superado las validaciones. Ajustamos sus valores finales y los almacenamos.
        $newFileFieldValue = $this->uploadFormFileSave( $form, $fieldName, $fich, $fileFieldValuePrev );
      }
      else {
        Cogumelo::debug('FormConnector: FU: NON Valida o ficheiro subido...' );
      }
    }
    else {
      $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (FO)' );
    }

    return $newFileFieldValue;
  } // uploadFormFileProcess( $form, $fieldName, $fich, $cgIntFrmId )

  public function uploadFormFileSave( $form, $fieldName, $fich, $fileFieldValuePrev ) {
    Cogumelo::debug(__METHOD__);
    // error_log(__METHOD__);

    $newFileFieldValue = false;

    Cogumelo::debug('FormConnector: FU: Validado. Vamos a moverlo...' );
    $tmpCgmlFileLocation = $form->tmpPhpFile2tmpFormFile( $fich['tmpLoc'], $fich['name'], $fieldName );

    if( $tmpCgmlFileLocation === false ) {
      Cogumelo::debug('FormConnector: FU: Fallo de move_uploaded_file movendo '.$fieldName.': ('.$fich['tmpLoc'].')' );
      $form->addFieldRuleError( $fieldName, 'cogumelo',
        'La subida del fichero ha fallado. (MU)' );
    }
    else {
      // El fichero subido ha pasado todos los controles. Vamos a registrarlo según proceda
      Cogumelo::debug('FormConnector: FU: Validado y movido. Paso final...' );

      $newFileFieldValue = [
        'status' => 'LOAD',
        'temp' => [
          'name' => $fich['name'],
          'originalName' => $fich['name'],
          'absLocation' => $tmpCgmlFileLocation,
          'type' => $fich['type'],
          'size' => $fich['size']
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
            $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (FE)' );
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

    return $newFileFieldValue;
  } // uploadFormFileSave( $form, $fieldName, $fich )

  public function uploadFormFileThumbnail( $fileLocation, $tnProfile ) {
    filedata::load('controller/FiledataImagesController.php');
    $iCtrl = new FiledataImagesController();
    $iCtrl->setProfile( $tnProfile );

    $fileSrcTn = $iCtrl->createImageProfile( $fileLocation, false, true );

    return $fileSrcTn;
  } // uploadFormFileThumbnail( $fileLocation, $tnProfile )




  public function deleteFormFile( $post ) {
    Cogumelo::debug(__METHOD__);
    // error_log(__METHOD__);

    // error_log('FormConnector: POST:' );
    // error_log('FormConnector: '. print_r( $post, true ) );

    $form = new FormController();

    $idForm = isset( $post['idForm'] ) ? $post['idForm'] : false;
    $fieldName = isset( $post['fieldName'] ) ? $post['fieldName'] : false;
    $moreInfo = [ 'idForm' => $idForm ];

    if( isset( $post['cgIntFrmId'], $post['fieldName'] ) ) {
      $cgIntFrmId = $post['cgIntFrmId'];
      $moreInfo['cgIntFrmId'] = $cgIntFrmId;
      $moreInfo['fieldName'] = $fieldName;

      $fich['fileTempId'] = isset( $post['fileTempId'] ) ? $post['fileTempId'] : false;
      $fich['fileId'] = isset( $post['fileId'] ) ? $post['fileId'] : false;

      $this->deleteFormFileProcess( $form, $fieldName, $fich, $cgIntFrmId );
    }
    else { // no parece haber fichero
      if( !empty( $fieldName ) ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'No han llegado los datos o lo ha hecho con errores. (ISE)' );
      }
      else {
        $form->addFormError( 'No han llegado los datos o lo ha hecho con errores. (ISE2)', 'formError' );
      }
    }

    // Notificamos el resultado al UI
    $form->sendJsonResponse( $moreInfo );
  } // function deleteFormFile() {

  public function deleteFormFileProcess( $form, $fieldName, $fich, $cgIntFrmId ) {
    Cogumelo::debug(__METHOD__);
    // error_log(__METHOD__);

    // Recuperamos formObj y validamos el fichero temporal
    if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' ) {
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

        if( !empty( $fich['fileTempId'] ) ) {
          $multipleIndex = $fich['fileTempId'];
        }
        else {
          $multipleIndex = 'FID_'.$fich['fileId'];
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
        error_log('FormConnector: FDelete: STATUS: ' . $fieldPrev['status'] );

        switch( $fieldPrev['status'] ) {
          case 'LOAD':
            // error_log('FormConnector: FDelete: LOAD - Borramos: '.$fieldPrev['temp']['absLocation'] );
            // unlink( $fieldPrev['temp']['absLocation'] ); // Garbage collector
            $fieldPrev = null;
            break;
          case 'EXIST':
            // error_log('FormConnector: FDelete: EXIST - Marcamos para borrar: '.$fieldPrev['prev']['absLocation'] );
            $fieldPrev['status'] = 'DELETE';
            break;
          case 'REPLACE':
            // error_log('FormConnector: FDelete: REPLACE - Borramos: '.$fieldPrev['temp']['absLocation'] );
            $fieldPrev['status'] = 'DELETE';
            // unlink( $fieldPrev['temp']['absLocation'] ); // Garbage collector
            $fieldPrev['temp'] = null;
            break;
          default:
            error_log('FormConnector: FDelete: Intentando borrar con status erroneo: ' . $fieldPrev['status'] );
            $form->addFieldRuleError( $fieldName, 'cogumelo', 'Intento de sobreescribir un fichero existente (STB)' );
            break;
        }
      }
      else {
        error_log('FormConnector: FDelete: Error intentando eliminar un fichero sin estado.' );
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'Intento de borrar un fichero inexistente (STN)' );
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
      $form->addFieldRuleError( $fieldName, 'cogumelo', 'Intento de borrado incorrecto. (FRM)' );
    }
  }
}
