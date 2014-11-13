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

    if( isset( $_FILES['ajaxFileUpload'], $_POST['idForm'], $_POST['cgIntFrmId'], $_POST['fieldName'] ) ) {
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
          $error = "El tamaño del fichero ha superado el límite establecido en el servidor.";
          break;
        case UPLOAD_ERR_FORM_SIZE:
          $error = "El tamaño del fichero ha superado el límite establecido para este campo.";
          break;
        case UPLOAD_ERR_PARTIAL:
          $error = "La subida del fichero no se ha completado.";
          break;
        case UPLOAD_ERR_NO_FILE:
          $error = "No se ha subido el fichero.";
          break;
        case UPLOAD_ERR_NO_TMP_DIR:
          $error = "La subida del fichero ha fallado. (6)";
          break;
        case UPLOAD_ERR_CANT_WRITE:
          $error = "La subida del fichero ha fallado. (7)";
          break;
        case UPLOAD_ERR_EXTENSION:
          $error = "La subida del fichero ha fallado. (8)";
          break;
        default:
          $error = "La subida del fichero ha fallado.";
          break;
      }

      // Datos enviados fuera de rango
      if( !$error && $fileSize < 1 ) {
        $error = "El tamaño del fichero parece ser cero (0)."; error_log($error);
      }

      // Verificando la existencia y tamaño del fichero intermedio
      if( !$error && ( !is_uploaded_file( $fileTmpLoc ) || filesize( $fileTmpLoc ) !== $fileSize ) ) {
        $error = "El fichero temporal parece incorrecto o sin datos."; error_log($error);
      }

      // Verificando el MIME_TYPE del fichero intermedio
      if( !$error ) {
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

      if( !$error ) {

        // Recuperamos formObj y validamos el fichero temporal
        if( $form->loadFromSession( $_POST[ 'cgIntFrmId' ] ) &&
          $form->getFieldType( $_POST[ 'fieldName' ] ) === 'file' )
        {

          // Creamos un objeto con los validadores y lo asociamos
          $form->setValidationObj( new FormValidators() );
          $fieldName = $_POST[ 'fieldName' ];
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
          if( !$form->validateField( $fieldName ) ) {
            $jvErrors = $form->getJVErrors();
            $error = 'El fichero no cumple las reglas de validación establecidas.'; error_log($error);
          }
          else {
            // El fichero ha superado las validaciones. Ajustamos sus valores finales y los almacenamos.
            $tmpCgmlFileLocation = $form->tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName );
            if( $tmpCgmlFileLocation === false ) {
              $error = 'Fallo de move_uploaded_file movendo ('.$fileTmpLoc.')'; error_log($error);
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
          } // else - if( !$form->validateField( $fieldName ) )
        } // if( $form->loadFromSession( $_POST[ 'cgIntFrmId' ] ) ) {
        else {
          $error = 'Los datos del formulario no han llegado bien al servidor. FORM'; error_log($error);
        }


      } // if( !$error ) // Recuperamos formObj y validamos el fichero temporal



    } // if( isset( ... ) )
    else { // no parece haber fichero
      $error = 'No han llegado los datos o lo ha hecho con errores. ISSET'; error_log($error);
    }


    // Notificamos el resultado al UI
    if( $error === false ) {
      // OK: Los datos procesados son $tmpFileFieldValue
      $respond = array( 'success' => 'success', 'fileName' => $tmpFileFieldValue[ 'name' ],
        'fileSize' => $tmpFileFieldValue[ 'size' ], 'fileType' => $tmpFileFieldValue[ 'type' ] );
    }
    else {
      $respond = array( 'success' => 'error', 'error' => 'fileUpload: ERROR: '.$error );
    }
    $respond[ 'idForm' ] = $_POST[ 'idForm' ];
    $respond[ 'fieldName' ] = $_POST[ 'fieldName' ];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($respond);
    error_log( print_r( json_encode($respond), true ) );

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
