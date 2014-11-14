<?php


Cogumelo::load('c_view/View.php');
common::autoIncludes();
form::autoIncludes();


class FormFileUpload extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }



  function fileUpload() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'fileUpload FormFileUpload');
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $form = new FormController();
    $error = false;

    error_log( 'FILES:' ); error_log( print_r( $_FILES, true ) );
    error_log( 'POST:' ); error_log( print_r( $_POST, true ) );

    if( isset( $_POST['idForm'], $_POST['cgIntFrmId'], $_POST['fieldName'], $_FILES['ajaxFileUpload'] ) ) {

      $idForm     = $_POST['idForm'];
      $cgIntFrmId = $_POST['cgIntFrmId'];
      $fieldName  = $_POST['fieldName'];

      $fileName     = $_FILES['ajaxFileUpload']['name'];     // The file name
      $fileTmpLoc   = $_FILES['ajaxFileUpload']['tmp_name']; // File in the PHP tmp folder
      $fileType     = $_FILES['ajaxFileUpload']['type'];     // The type of file it is
      $fileSize     = $_FILES['ajaxFileUpload']['size'];     // File size in bytes
      $fileErrorMsg = $_FILES['ajaxFileUpload']['error'];    // 0 for false... and 1 for true

      // Aviso de error PHP
      switch( $fileErrorMsg ) {
        case UPLOAD_ERR_OK:
          // Todo OK, no hay error
          break;
        case UPLOAD_ERR_INI_SIZE:
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'El tamaño del fichero ha superado el límite establecido en el servidor.' );
          break;
        case UPLOAD_ERR_FORM_SIZE:
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'El tamaño del fichero ha superado el límite establecido para este campo.' );
          break;
        case UPLOAD_ERR_PARTIAL:
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero no se ha completado.' );
          break;
        case UPLOAD_ERR_NO_FILE:
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'No se ha subido el fichero.' );
          break;
        case UPLOAD_ERR_NO_TMP_DIR:
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (6)' );
          break;
        case UPLOAD_ERR_CANT_WRITE:
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (7)' );
          break;
        case UPLOAD_ERR_EXTENSION:
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado. (8)' );
          break;
        default:
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'La subida del fichero ha fallado.' );
          break;
      }

      // Datos enviados fuera de rango
      if( !$form->existErrors() && $fileSize < 1 ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'El tamaño del fichero parece ser cero (0).' );
      }

      // Verificando la existencia y tamaño del fichero intermedio
      if( !$form->existErrors() && ( !is_uploaded_file( $fileTmpLoc ) || filesize( $fileTmpLoc ) !== $fileSize ) ) {
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'El fichero temporal parece incorrecto o sin datos.' );
      }

      // Verificando el MIME_TYPE del fichero intermedio
      if( !$form->existErrors() ) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $fileTypePhp = finfo_file( $finfo, $fileTmpLoc );
        if( $fileTypePhp !== false ) {
          if( $fileType !== $fileTypePhp ) {
            error_log( 'ALERTA: Los MIME_TYPE reportados por el navegador y PHP difieren: '.$fileType.' != '.$fileTypePhp );
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
          // Creamos un objeto con los validadores y lo asociamos
          $form->setValidationObj( new FormValidators() );
          $tmpFileFieldValue = array(
            'name' => $fileName,
            'originalName' => $fileName,
            'absLocation' => $fileTmpLoc,
            'type' => $fileType,
            'size' => $fileSize
          );

          // Almacenamos los datos temporales en el formObj para validarlos
          $form->setFieldValue( $fieldName, $tmpFileFieldValue );

          // Validar input del fichero
          $form->validateField( $fieldName );

          if( !$form->existErrors() ) {
            // El fichero ha superado las validaciones. Ajustamos sus valores finales y los almacenamos.
            $tmpCgmlFileLocation = $form->tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName );
            if( $tmpCgmlFileLocation === false ) {
              $form->addFieldRuleError( $fieldName, 'cogumelo', 'Fallo de move_uploaded_file movendo ('.$fileTmpLoc.')' );
            }
            else {
              $tmpFileFieldValue[ 'absLocation' ] = $tmpCgmlFileLocation;

              $fileFieldValue = $form->getFieldValue( $fieldName );
              $fileStatus = $form->getFieldParam( $fieldName, 'fileStatus' );

              /*
              if( $fileFieldValue === false ) {
                // No existe fichero previo
              */
                $fileFieldValue = $tmpFileFieldValue;
                $fileStatus[ 'tmpFile' ] = $tmpFileFieldValue;
              /*
              }
              else {
                // Existe valor previo. Previsión de actualizar
              }
              */

              $form->setFieldValue( $fieldName, $fileFieldValue );
              $form->setFieldParam( $fieldName, 'fileStatus', $fileStatus );

              // Persistimos formObj para cuando se envíe el formulario completo
              $form->saveToSession();
            } // else - if( !$tmpCgmlFileLocation )
          } // if( !$form->existErrors() )
        } // if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' )
        else {
          $form->addFieldRuleError( $fieldName, 'cogumelo', 'Los datos del fichero no han llegado bien al servidor. FORM' );
        }


      } // if( !$error ) // Recuperamos formObj y validamos el fichero temporal



    } // if( isset( ... ) )
    else { // no parece haber fichero
      $form->addFieldRuleError( $fieldName, 'cogumelo', 'No han llegado los datos o lo ha hecho con errores. ISSET' );
    }


    // Notificamos el resultado al UI
    if( !$form->existErrors() ) {
      $moreInfo = array( 'idForm' => $idForm, 'fieldName' => $fieldName, 'fileName' => $tmpFileFieldValue['name'],
        'fileSize' => $tmpFileFieldValue['size'], 'fileType' => $tmpFileFieldValue['type'] );
      header('Content-Type: application/json; charset=utf-8');
      echo $form->jsonFormOk( $moreInfo );
    }
    else {
      $moreInfo = array( 'idForm' => $idForm, 'fieldName' => $fieldName );
      header('Content-Type: application/json; charset=utf-8');
      echo $form->jsonFormError( $moreInfo );
    }
    /*
    if( !$form->existErrors() ) {
      // OK: Los datos procesados son $tmpFileFieldValue
      $respond = array( 'success' => 'success', 'fileName' => $tmpFileFieldValue['name'],
        'fileSize' => $tmpFileFieldValue['size'], 'fileType' => $tmpFileFieldValue['type'] );
    }
    else {
      $respond = array( 'success' => 'error', 'error' => 'fileUpload: ERROR: '.$error );
    }
    $respond['idForm'] = $_POST['idForm'];
    $respond['fieldName'] = $_POST['fieldName'];

    echo json_encode($respond);
    error_log( print_r( json_encode($respond), true ) );
    */

  } // function fileUpload() {


  /**

  pasos

  1.- Sube o ficheiro + ver que existe en tmp e ten tamaño
  http://php.net/manual/es/function.is-uploaded-file.php
  http://es1.php.net/manual/en/function.filesize.php
  Controlar upload_max_filesize e post_max_size
  To upload large files, this value must be larger than upload_max_filesize.
  If memory limit is enabled by your configure script, memory_limit also affects file uploading.
  Generally speaking, memory_limit should be larger than post_max_size.

  2.- Validadores - Se non valida, eliminar en form e en srv.
  http://es1.php.net/manual/en/function.finfo-file.php
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $finfo->file($_FILES['upfile']['tmp_name'])

  3.- Establecer o seu destino temporal e definitivo: ruta e nome (evitando colisions)
  make sure that the file name not bigger than 250 characters.
  mb_strlen($filename,"UTF-8") > 225
  make sure the file name in English characters, numbers and (_-.) symbols.
  preg_match("`^[-0-9A-Z_\.]+$`i",$filename)
  http://php.net/manual/es/ini.core.php#ini.open-basedir
  http://php.net/pathinfo
  http://es1.php.net/manual/en/function.chmod.php
  http://es1.php.net/manual/en/function.move-uploaded-file.php
  http://php.net/manual/en/function.sha1-file.php

  4.- Gardar todo no obj FORM e voltalo a meter na sesion



  SEGURIDADE EXTERNA

  You can use .htaccess to stop working some scripts as in example php file in your upload path.
  use :
  AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
  Options -ExecCGI


  **/





} // class FormFileUpload extends View