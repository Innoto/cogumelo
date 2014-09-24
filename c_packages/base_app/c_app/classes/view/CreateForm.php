<?php


Cogumelo::load('c_view/View');
Cogumelo::load('c_controller/FormController');
Cogumelo::load('c_controller/FormValidators');
Cogumelo::load('controller/LostController');
Cogumelo::load('model/LostVO');

class CreateForm extends View
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


  
  function lostForm() {

    $form = new FormController( 'lostForm', '/sendLostForm' ); //actionform

    $form->setField( 'lostName', array( 'placeholder' => 'Nombre') );
    $form->setField( 'lostSurname', array( 'placeholder' => 'Apellidos') );
    $form->setField( 'lostMail', array( 'placeholder' => 'Email') );    
    
    $form->setField( 'lostBornDate', array( 'label' => 'Fecha de nacimiento Min', 'placeholder' => 'Fecha', 'value' => '15/11/1987', 'format' => 'datedma'));
    $form->setField( 'lostBornDate2', array( 'label' => 'Fecha de nacimiento Max', 'placeholder' => 'Fecha', 'value' => '15/11/2000', 'format' => 'datedma'));    
    
    $form->setField( 'lostDate', array( 'label' => 'Fecha Min', 'placeholder' => 'Fecha', 'value' => '2014-1-9', 'format' => 'dateamd') );
    $form->setField( 'lostDate2', array( 'label' => 'Fecha Max', 'placeholder' => 'Fecha', 'value' => '2012-11-8', 'format' => 'dateamd')) ;
    
    $form->setField( 'lostMail', array( 'placeholder' => 'Email') );
    $form->setField( 'lostPhone', array( 'placeholder' => 'Phone') );
    $form->setField( 'lostProvince', array( 'type' => 'select', 'label' => 'Province',
      'options'=> array( '' => 'Selecciona', '1' => 'A coruña', '2' => 'Lugo', '3' => 'Ourense', '4' => 'Pontevedra' )
    ) );        
    
    $form->setField( 'lostPassword', array( 'type' => 'password', 'placeholder' => 'Password' ) );
    $form->setField( 'lostPassword2', array( 'type' => 'password', 'placeholder' => 'Repeat password' ) );      
    //$form->setField( 'lostConditions', array( 'type' => 'checkbox', 'label' => 'He leído y acepto los Términos y Condiciones de uso') );    
    $form->setField( 'lostSubmit', array( 'type' => 'submit', 'value' => 'OK' ) );
    $form->setField( 'lostSubmit2', array( 'type' => 'submit', 'value' => 'OK2' ) );

    
    $form->setValidationRule( 'lostName', 'required' );
    //$form->setValidationRule( 'lostConditions', 'required' );
    $form->setValidationRule( 'lostMail', 'required' );
    $form->setValidationRule( 'lostPhone', 'required' );
    $form->setValidationRule( 'lostPassword', 'equalTo', '#lostPassword2' );
    
    $form->setValidationRule( 'lostBornDate', 'dateMin', '2014-9-9' );
    $form->setValidationRule( 'lostBornDate2', 'dateMax', '2014-9-9' );
    $form->setValidationRule( 'lostDate', 'dateMin', '2014-9-9' );
    $form->setValidationRule( 'lostDate2', 'dateMax', '2014-9-9' );
    
    
    $form->saveToSession();
    
    $this->template->assign("lostFormOpen", $form->getHtmpOpen());
    $this->template->assign("lostFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("lostFormClose", $form->getHtmlClose());
    $this->template->assign("lostFormValidations", $form->getJqueryValidationJS());
   
    
    $lostControl = new LostController();
    $res = $lostControl->listItems();
    $this->template->assign("lostList", $res->fetchAll());    
    
    $this->template->setTpl('lostForm.tpl');
    $this->template->exec();
    
  } // function loadForm()


  function sendLostForm() {
        
    $formError = false;
    $postData = null;

    $postDataJson = file_get_contents('php://input');
    //error_log( $postDataJson );
    if( $postDataJson !== false && strpos( $postDataJson, '{' )===0 ) {
      $postData = json_decode( $postDataJson, true );
    }
    //error_log( print_r( $postData, true ) );
    if( isset( $postData[ 'cgIntFrmId' ] ) ) {
      // Creamos un objeto recuperandolo de session y añadiendo los datos POST
      $form = new FormController( false, false, $postData[ 'cgIntFrmId' ], $postData );
      // Creamos un objeto con los validadores
      $validator = new FormValidators();
      // y lo asociamos
      $form->setValidationObj( $validator );
      $form->validateForm();
      $jvErrors = $form->getJVErrors();

      //Si todo esta OK!
      if( sizeof( $jvErrors ) == 0 ){
        $lostControl = new LostController();
        $valuesArray = $form->getValuesArray();
        
        unset($valuesArray['cgIntFrmId']);
        unset($valuesArray['lostPassword2']);
        unset($valuesArray['lostSubmit']);
        unset($valuesArray['lostSubmit2']);
        $res = $lostControl->create($valuesArray);
      }
      
      if( sizeof( $jvErrors ) > 0 ) {
        echo json_encode(
          array(
            'success' => 'error',
            'jvErrors' => $jvErrors,
            'formError' => 'El servidor no considera válidos los datos. NO SE HAN GUARDADO.'
          )
        );
      }
      else {
        echo json_encode( array( 'success' => 'success') );
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
  
}

