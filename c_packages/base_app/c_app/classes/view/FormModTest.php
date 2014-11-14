<?php


Cogumelo::load('c_view/View.php');
common::autoIncludes();
form::autoIncludes();


class FormModTest extends View
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



  /**
  * Defino y muestro un formulario
  *
  */
  function loadForm() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'FormModTest: loadForm');
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $form = new FormController( 'probaPorto', '/form-mod-action' );

    $form->setSuccess( 'accept', 'Gracias por participar' );
    $form->setSuccess( 'redirect', '/' );

    $form->setField( 'inputFicheiro', array( 'type' => 'file', 'id' => 'inputFicheiro',
      'placeholder' => 'Escolle un ficheiro', 'label' => 'Colle un ficheiro',
      'destDir' => '/porto' ) );

    $form->setValidationRule( 'inputFicheiro', 'minfilesize', 1024 );
    $form->setValidationRule( 'inputFicheiro', 'accept', 'image/gif' );
    //$form->setValidationRule( 'inputFicheiro', 'required' );

    /*
    $form->setField( 'select1', array( 'type' => 'select', 'label' => 'Meu Select',
      'value' => array( '1', '2' ),
      'options'=> array( '0' => 'Zero', '1' => 'Opcion 1', '2' => 'Posto 2', 'asdf' => 'asdf' ),
      'multiple' => 'multiple'
      ) );
    */
    $form->setField( 'input2', array( 'id' => 'meu2', 'label' => 'Meu 2', 'value' => 'valor678' ) );
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

    // Una vez que hemos definido todo, guardamos el form en sesion
    $form->saveToSession();

    $this->template->assign("formOpen", $form->getHtmpOpen());
    $this->template->assign("formFields", $form->getHtmlFieldsArray());
    $this->template->assign("formClose", $form->getHtmlClose());
    $this->template->assign("formValidations", $form->getJqueryValidationJS());

    $this->template->setTpl('formModTest.tpl');
    $this->template->exec();

  } // function loadForm()



  /**
  * Evalua el envio del formulario y reporta posibles errores
  *
  */
  function actionForm() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'FormModTest: actionForm');
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    // Creamos un objeto FORM sin datos
    $form = new FormController();

    // Leemos el input del navegador y recuperamos FORM de sesion añadiendole los datos enviados
    if( $form->loadPostInput() ) {
      // Creamos un objeto con los validadores y lo asociamos
      $form->setValidationObj( new FormValidators() );

      // $form->setValidationRule( 'input2', 'maxlength', '10' ); // CAMBIANDO AS REGLAS
      $form->validateForm();

      //$form->addFieldRuleError( 'check1', 'cogumelo', 'Un mensaxe de error de campo' );
      //$form->addFormError( 'Ola meu... ERROR porque SI ;-)' );
    }
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recividos.', 'formError' );
    }

    if( !$form->existErrors() ) {
      // Validacion OK. Falta procesar File Fields
      if( !$form->processFileFields() ) {
        $form->addFormError( 'Ha sucedido un problema con los ficheros adjuntos. Puede que sea necesario subirlos otra vez.', 'formError' );
      }
    }

    if( !$form->existErrors() ) {
      echo $form->jsonFormOk();
    }
    else {
      // Añado errores a mano
      $form->addFormError( 'Han aparecido errores. NO SE HAN GUARDADO LOS DATOS.','formError' );
      $form->addFormError( 'Error a lo loco :D','sitioNonDefinido' );
      echo $form->jsonFormError();
    }

  }


} // class FormModTest extends View
