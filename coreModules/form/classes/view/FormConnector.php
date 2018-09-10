<?php
Cogumelo::load('coreView/View.php');
common::autoIncludes();
form::autoIncludes();


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
class FormConnector extends View {

  // public function __construct( $base_dir ) {
  //   parent::__construct( $base_dir );
  // }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  public function accessCheck() {

    return true;
  }


  // addUrlPatterns( '#^cgml-form-command$#', 'view:FormConnector::execCommand' );
  public function execCommand() {
    // error_log(__METHOD__);

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
          error_log('FormConnector: ERROR - FormConnector::execCommand - Comando no soportado: '.$_POST['execute'] );
          break;
      }
    }
    else {
      error_log('FormConnector: ERROR - FormConnector::execCommand - Datos erroneos' );
    }
  }



  // addUrlPatterns( '#^cgml-form-file-upload$#', 'view:FormConnector::fileUpload' );
  public function fileUpload() {
    // error_log(__METHOD__);

    $formCnxFiles = new FormConnectorFiles();
    if( isset( $_POST['execute'] ) && $_POST['execute'] === 'delete' ) {
      $formCnxFiles->deleteFormFile( $_POST );
    }
    else {
      $formCnxFiles->uploadFormFile( $_POST, $_FILES );
    }
  }



  public function keepAlive() {
    cogumelo::debug('FormConnector: (Notice) FormConnector::keepAlive' );

    $form = new FormController();

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




  /**
   * Agrupaciones de campos
   */


  private function getGroupElement() {
    // error_log('FormConnector: getGroupElement ' );

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
    // error_log('FormConnector: removeGroupElement ' );

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



  // addUrlPatterns( '#^cgml-form-htmleditor-config.js#', 'view:FormConnector::customCkeditorConfig' );
  /**
   * Configuracion propia de CKEditor
   */
  public function customCkeditorConfig() {
    // error_log(__METHOD__);

    $fileInfo = ModuleController::getRealFilePath( 'classes/view/templates/js/ckeditor-config.js', 'form' );

    header( 'Content-Type: application/javascript; charset=utf-8' );
    header( 'Content-Length: ' . filesize( $fileInfo ) );
    readfile( $fileInfo );
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
