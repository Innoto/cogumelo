<?php


Cogumelo::load('c_view/View.php');
common::autoIncludes();
form::autoIncludes();


/**
 * Gestión de ficheros en formularios. Subir o borrar ficheros en campos de formulario.
 *
 * @package Module Form
 **/
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

          // Guardamos los datos previos del campo
          $fileFieldValuePrev = $form->getFieldValue( $fieldName );

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

          error_log( 'FU: Validando ficheiro subido...' );
          error_log( print_r( $form->getFieldValue( $fieldName ), true ) );

          // Validar input del fichero
          $form->validateField( $fieldName );

          if( !$form->existErrors() ) {
            // El fichero ha superado las validaciones. Ajustamos sus valores finales y los almacenamos.
            error_log( 'FU: Validado o ficheiro subido...' );

            $tmpCgmlFileLocation = $form->tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName );
            if( $tmpCgmlFileLocation === false ) {
              error_log( 'FU: Validado pero NON movido.' );

              $form->addFieldRuleError( $fieldName, 'cogumelo', 'Fallo de move_uploaded_file movendo ('.$fileTmpLoc.')' );
            }
            else {
              // El fichero subido ha pasado todos los controles. Vamos a registrarlo según proceda
              error_log( 'FU: Validado e movido...' );

              if( isset( $fileFieldValuePrev['status'] ) && $fileFieldValuePrev['status'] !== false ) {
                if( $fileFieldValuePrev['status'] === 'DELETE' ) {
                  error_log( 'FU: Todo OK e estado REPLACE...' );

                  $fileFieldValuePrev['status'] = 'REPLACE';
                  $fileFieldValuePrev['temp'] = array(
                    'name' => $fileName,
                    'originalName' => $fileName,
                    'absLocation' => $tmpCgmlFileLocation,
                    'type' => $fileType,
                    'size' => $fileSize
                  );
                }
                else {
                  error_log( 'FU: Validado pero status erroneo: ' . $fileFieldValuePrev['status'] );

                  $form->addFieldRuleError( $fieldName, 'cogumelo', 'Intento de sobreescribir un fichero existente' );
                }
              }
              else {
                error_log( 'FU: Todo OK e estado LOAD...' );

                $fileFieldValuePrev = array(
                  'status' => 'LOAD',
                  'temp' => array(
                    'name' => $fileName,
                    'originalName' => $fileName,
                    'absLocation' => $tmpCgmlFileLocation,
                    'type' => $fileType,
                    'size' => $fileSize
                  )
                );
              }


              if( !$form->existErrors() ) {
                error_log( 'FU: Todo OK con el ficheiro subido... Se persiste...' );

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
            error_log( 'FU: NON Valida o ficheiro subido...' );
            // Los errores ya estan cargados en FORM
          }

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
      $moreInfo = array( 'idForm' => $idForm, 'fieldName' => $fieldName, 'fileName' => $fileFieldValuePrev['temp']['name'],
        'fileSize' => $fileFieldValuePrev['temp']['size'], 'fileType' => $fileFieldValuePrev['temp']['type'] );
      header('Content-Type: application/json; charset=utf-8');
      echo $form->jsonFormOk( $moreInfo );
    }
    else {
      $moreInfo = array( 'idForm' => $idForm, 'fieldName' => $fieldName );
      header('Content-Type: application/json; charset=utf-8');
      echo $form->jsonFormError( $moreInfo );
    }

  } // function fileUpload() {



  function fileDelete() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'fileDelete FormFileUpload');
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $form = new FormController();
    $error = false;

    error_log( 'POST:' ); error_log( print_r( $_POST, true ) );

    if( isset( $_POST['idForm'], $_POST['cgIntFrmId'], $_POST['fieldName'] ) ) {

      $idForm     = $_POST['idForm'];
      $cgIntFrmId = $_POST['cgIntFrmId'];
      $fieldName  = $_POST['fieldName'];

      // Recuperamos formObj y validamos el fichero temporal
      if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' ) {

        // Guardamos los datos previos del campo
        $fileFieldValuePrev = $form->getFieldValue( $fieldName );


        if( isset( $fileFieldValuePrev['status'] ) && $fileFieldValuePrev['status'] !== false ) {
          switch( $fileFieldValuePrev['status'] ) {
            case 'EXIST':
              error_log( 'FDelete: EXIST - Marcamos para borrar: '.$fileFieldValuePrev['prev']['absLocation'] );

              $fileFieldValuePrev['status'] = 'DELETE';
              break;
            case 'REPLACE':
              error_log( 'FDelete: REPLACE - Borramos: '.$fileFieldValuePrev['temp']['absLocation'] );

              $fileFieldValuePrev['status'] = 'DELETE';
              unlink( $fileFieldValuePrev['temp']['absLocation'] );
              $fileFieldValuePrev['temp'] = null;
              break;
            default:
              error_log( 'FDelete: Intentando borrar con status erroneo: ' . $fileFieldValuePrev['status'] );

              $form->addFieldRuleError( $fieldName, 'cogumelo', 'Intento de sobreescribir un fichero existente' );
              break;
          }

        }
        else {
          error_log( 'FDelete: Intentando eliminar un fichero sin estado.' );

          $form->addFieldRuleError( $fieldName, 'cogumelo', 'Intento de borrar un fichero inexistente' );
        }


        if( !$form->existErrors() ) {
          error_log( 'FDelete: OK. Guardando el nuevo estado... Se persiste...' . $fileFieldValuePrev['status'] );

          $form->setFieldValue( $fieldName, $fileFieldValuePrev );
          // Persistimos formObj para cuando se envíe el formulario completo
          $form->saveToSession();
        }
        else {
          error_log( 'FDelete: El borrado ha fallado. Se mantiene el estado.' );
        }

      } // if( $form->loadFromSession( $cgIntFrmId ) && $form->getFieldType( $fieldName ) === 'file' )
      else {
        $form->addFieldRuleError( $fieldName, 'cogumelo', 'Los datos del fichero no han llegado bien al servidor. FORM' );
      }

    } // if( isset( ... ) )
    else { // no parece haber fichero
      $form->addFieldRuleError( $fieldName, 'cogumelo', 'No han llegado los datos o lo ha hecho con errores. ISSET' );
    }


    // Notificamos el resultado al UI
    if( !$form->existErrors() ) {
      $moreInfo = array( 'idForm' => $idForm, 'fieldName' => $fieldName );
      header('Content-Type: application/json; charset=utf-8');
      echo $form->jsonFormOk( $moreInfo );
    }
    else {
      $moreInfo = array( 'idForm' => $idForm, 'fieldName' => $fieldName );
      header('Content-Type: application/json; charset=utf-8');
      echo $form->jsonFormError( $moreInfo );
    }

  } // function fileDelete() {





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
