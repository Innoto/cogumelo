<?php


Cogumelo::load('c_view/View.php');
Cogumelo::load('c_controller/FormControllerV2.php');
Cogumelo::load('c_controller/FormValidators.php');


class FormsTestV2 extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }



  function loadForm() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'FormsTestV2: loadForm');
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $form = new FormControllerV2( 'probaPorto', '/actionformV2' ); //actionform

    $form->setField( 'inputFicheiro', array( 'type' => 'file', 'id' => 'inputFicheiro',
      'placeholder' => 'Escolle un ficheiro', 'label' => 'Colle un ficheiro',
      'destDir' => $_SERVER['DOCUMENT_ROOT'].'test_upload/0---OK/' ) );
    $form->setValidationRule( 'inputFicheiro', 'required' );
    $form->setValidationRule( 'inputFicheiro', 'minfilesize', 1024 );
    //$form->setValidationRule( 'inputFicheiro', 'accept', 'text/plain' );

    /*
    $form->setField( 'select1', array( 'type' => 'select', 'label' => 'Meu Select',
      'value' => array( '1', '2' ),
      'options'=> array( '0' => 'Zero', '1' => 'Opcion 1', '2' => 'Posto 2', 'asdf' => 'asdf' ),
      'multiple' => 'multiple'
      ) );

    $form->setField( 'input1', array( 'placeholder' => 'Mete 1 valor', 'value' => '5' ) );
    $form->setValidationRule( 'input1', 'required' );
    $form->setValidationRule( 'input1', 'numberEU' );
    //$form->setValidationRule( 'input1', 'regex', '^\d+$' );
    */
    $form->setField( 'input2', array( 'id' => 'meu2', 'label' => 'Meu 2', 'value' => 'valor67' ) );
    $form->setValidationRule( 'input2', 'required' );
    $form->setValidationRule( 'input2', 'minlength', '8' );

    $form->setField( 'check1', array( 'type' => 'checkbox', 'label' => 'Meu checkbox',
      'value' => array( '1', 'asdf' ),
      'options'=> array( '0' => 'Zero', '1' => 'Opcion 1', '2' => 'Posto 2', 'asdf' => 'asdf' )
      ) );
    $form->setValidationRule( 'check1', 'required' );

    /*
    $form->setField( 'radio1', array( 'type' => 'radio', 'label' => 'Meu radio', 'value' => '2',
      'options'=> array( '' => 'Vacio', '1' => 'Opcion 1', '2' => 'Posto 2', 'asdf' => 'asdf' )
      ) );
    */

    $form->setField( 'submit', array( 'type' => 'submit', 'label' => 'Pulsa para enviar', 'value' => 'Manda' ) );



    $form->saveToSession();

    print '<!DOCTYPE html>'."\n".
    '<html>'."\n".
    '<head>'."\n".
    '  <title>FORMs con Cogumelo</title>'."\n".
    '  <script src="/js/jquery.min.js"></script>'."\n".
    '  <script src="/js/jquery-cogumelo-forms.js"></script>'."\n".
    '  <script src="/js/jquery.serializeFormToObject.js"></script>'."\n".
    '  <script src="/js/jquery-validation/jquery.validate.js"></script>'."\n".
    '  <script src="/js/jquery-validation/additional-methods.js"></script>'."\n".
    '  <script src="/js/jquery-validation/CFM-additional-methods.js"></script>'."\n".
    '  <style>div { border:1px dashed; margin:5px; padding:5px; } '.
      'label.error, .formError{ color:red; border:2px solid red; } '.
      '.ffn-inputFicheiro { background-color:#FFD; }</style>'."\n".
    '</head>'."\n".
    '<body>'."\n".
    $form->getHtmpOpen()."\n".
    $form->getHtmlFields()."\n".
    "\n".
    '<div id="subidas" style="background-color:#EEE;">'."\n".
    '<div id="list">Info: </div>'."\n".
    //'<span id="drop_zone" style="background-color:blue;">Drop files here</span>'."\n".
    //'<input type="button" name="botonUploadFile" value="subir ficheiro" onclick="uploadFile()"><br>'."\n".
    '<progress id="progressBar" value="0" max="100" style="width:300px;"></progress>'."\n".
    '<h3 id="status">status</h3>'."\n".
    '<p id="loaded_n_total">carga</p>'."\n".
    '</div>'."\n".
    "\n".
    //$form->getHtmlFieldArray( 'check1' )['options']['2']['text'].$form->getHtmlFieldArray( 'check1' )['options']['2']['input']."\n".
    '<div class="JQVMC-formError">errores formError... </div>'."\n".
    '<div id="JQVMC-meu2-error">errores meu2... </div>'."\n".
    '<div id="JQVMC-ungrupo-error">errores ungrupo... </div>'."\n".
    '<div id="JQVMC-manual">errores manuales... </div>'."\n".
    $form->getHtmlClose()."\n".
    $form->getJqueryValidationJS()."\n".
    '</body>'."\n".
    '</html>'."\n";

  } // function loadForm()



  function actionForm() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'FormsTestV2: actionForm');
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $formError = false;
    $postData = null;

    $postDataJson = file_get_contents('php://input');
    error_log( $postDataJson );
    if( $postDataJson !== false && strpos( $postDataJson, '{' )===0 ) {
      $postData = json_decode( $postDataJson, true );
    }

    error_log( print_r( $postData, true ) );

    if( isset( $postData[ 'cgIntFrmId' ] ) ) {
      // Creamos un objeto recuperandolo de session y añadiendo los datos POST
      $form = new FormControllerV2( false, false, $postData[ 'cgIntFrmId' ], $postData );
      // Creamos un objeto con los validadores y lo asociamos
      $form->setValidationObj( new FormValidators() );


      // CAMBIANDO AS REGLAS
      //$form->setValidationRule( 'input1', 'required' );
      //$form->setValidationRule( 'input2', 'required' );
      //$form->setValidationRule( 'input1', 'numberEU' );
      //$form->setValidationRule( 'input1', 'minlength', '3' );
      //$form->setValidationRule( 'input2', 'maxlength', '3' );
      //$form->setValidationRule( 'select1', 'required' );
      //$form->setValidationRule( 'check1', 'required' );

      $form->validateForm();

      //$form->addJVError( 'manual', 'Ola meu... ERROR ;-)' );

      $jvErrors = $form->getJVErrors();


      if( sizeof( $jvErrors ) === 0 ) {
        // Todo OK. Falta procesar File Fields
        foreach( $form->getFieldsNamesArray() as $fieldName ){
          if( $form->getFieldType( $fieldName ) === 'file' ) {
            error_log( 'FILE: Almacenando File Field: '.$fieldName );
            $destDir = $form->getFieldParam( $fieldName, 'destDir' );
            $fileStatus = $form->getFieldParam( $fieldName, 'fileStatus' );
error_log( print_r( $fileStatus, true ) );
            $fileFieldValue = $form->getFieldValue( $fieldName );
            /*
            $fileStatus['tmpFile'] =
              'name'
              'originalName'
              'absLocation'
              'type'
              'size'
            */
            // mv $fileStatus['tmpFile']['absLocation'] $destDir
            $fileName = $form->secureFileName( $fileStatus['tmpFile']['originalName'] );
            error_log( 'FILE: movendo ' . $fileStatus['tmpFile']['absLocation'] . ' a ' . $destDir.$fileName );
            rename( $fileStatus['tmpFile']['absLocation'], $destDir.$fileName );
          }
        }
      }

      if( sizeof( $jvErrors ) > 0 ) {


        // Añado errores a mano
        $form->addJVError( 'formError', 'El servidor no considera válidos los datos. NO SE HAN GUARDADO.' );
        $form->addJVError( 'sinSitioDefinido', 'Error a lo loco :D' );
        // y recargo los errores para tenerlos todos
        $jvErrors = $form->getJVErrors();
        echo json_encode(
          array(
            'success' => 'error',
            'jvErrors' => $jvErrors,
            'formError' => 'El servidor no considera válidos los datos. NO SE HAN GUARDADO.'
          )
        );
      }
      else {
        echo json_encode( array( 'success' => 'success' ) );
      }

    } //if( isset( $postData[ 'cgIntFrmId' ] ) )
    else {
      echo json_encode(
        array(
          'success' => 'error',
          'error' => 'Los datos del formulario no han llegado bien al servidor. NO SE HAN GUARDADO.'
        )
      );
    }

  }





  function ajaxUpload() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'ajaxUpload FormsTestV2');
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $error = false;

    error_log( 'FILES:' ); error_log( print_r( $_FILES, true ) );
    error_log( 'POST:' ); error_log( print_r( $_POST, true ) );

    if( isset( $_FILES['ajaxFileUpload'] ) ) {
      $fileName     = $_FILES['ajaxFileUpload']['name'];     // The file name
      $fileTmpLoc   = $_FILES['ajaxFileUpload']['tmp_name']; // File in the PHP tmp folder
      $fileType     = $_FILES['ajaxFileUpload']['type'];     // The type of file it is
      $fileSize     = $_FILES['ajaxFileUpload']['size'];     // File size in bytes
      $fileErrorMsg = $_FILES['ajaxFileUpload']['error'];    // 0 for false... and 1 for true

      // Aviso de error PHP
      switch ($fileErrorMsg) {
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
        $fileTypePhp = finfo_file($finfo, $fileTmpLoc);
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

      // Recuperamos formObj y validamos el fichero temporal
      if( !$error ) {
        if( isset( $_POST[ 'cgIntFrmId' ] ) ) {
          $cgIntFrmId = $_POST[ 'cgIntFrmId' ];
          $fieldName = $_POST[ 'fieldName' ];

          // Creamos un objeto recuperandolo de session y añadiendo los datos POST
          $form = new FormControllerV2( false, false, $cgIntFrmId, false );
          // Creamos un objeto con los validadores y lo asociamos
          $form->setValidationObj( new FormValidators() );


          // Cargar fichero en formObj
          if( $form->getFieldType( $fieldName ) !== 'file' ) {
            $error = "El campo no es de tipo FILE."; error_log($error);
          }
          else {
            $tmpExt = '';
            $tmpExtPos = strrpos( $fileName, '.' );
            if( $tmpExtPos > 0 ) { // Not FALSE or 0
              $tmpExt = substr( $fileName, 1+$tmpExtPos );
              if( ( mb_strlen( $tmpExt, 'UTF-8' ) > 5 ) || ( preg_match( '/^[-0-9A-Z_\.]+$/i', $tmpExt ) !== 1 ) ) {
                error_log( 'ALERTA: La Extensión del fichero parece anormal: '.$tmpExt );
              }
            }

            $tmpFileFieldValue = array(
              'name' => $fileName,
              'originalName' => $fileName,
              'absLocation' => $fileTmpLoc,
              'type' => $fileType,
              'size' => $fileSize
            );

            // Almacenamos los datos temporales en el fomrObj para validarlos
            $form->setFieldValue( $fieldName, $tmpFileFieldValue );

            // Validar input del fichero
            if( !$form->validateField( $fieldName ) ) {
              $jvErrors = $form->getJVErrors();
              $error = 'El fichero no cumple las reglas de validación establecidas.'; error_log($error);
            }
            else {
              // El fichero ha superado las validaciones. Ajustamos sus valores finales y los almacenamos.

              $tmpCgmlFileLocation = $form->tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName );

              if( !$tmpCgmlFileLocation ) {
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

error_log( print_r( $fileStatus, true ) );

                // Persistimos formObj para cuando se envíe el formulario completo
                $form->saveToSession();
              }
            }
          }
        } //if( isset( $_POST[ 'cgIntFrmId' ] ) )
        else {
          $error = 'Los datos del formulario no han llegado bien al servidor. NO SE HAN GUARDADO.'; error_log($error);
        }
      }
    }
    else { // no parece haber fichero
      $error = 'No ha llegado el fichero o lo ha hecho con errores.'; error_log($error);
    }


    // Notificamos el resultado al UI
    if( $error === false ) {
      // OK: Los datos procesados son $tmpFileFieldValue
      $respond = array( 'success' => 'success', 'fileName' => $tmpFileFieldValue[ 'name' ],
        'fileSize' => $tmpFileFieldValue[ 'size' ], 'fileType' => $tmpFileFieldValue[ 'type' ] );
    }
    else {
      $respond = array( 'success' => 'error', 'error' => 'ajaxUpload: ERROR: '.$error );
    }
    $respond[ 'idForm' ] = $_POST[ 'idForm' ];
    $respond[ 'fieldName' ] = $_POST[ 'fieldName' ];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($respond);
    error_log( print_r( json_encode($respond), true ) );

  } // function ajaxUpload() {

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





  function phpinfo() {
    phpinfo();
  }




} // class FormsTestV2 extends View
