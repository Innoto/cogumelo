<?php
Cogumelo::load('coreView/View.php');

common::autoIncludes();
form::autoIncludes();
filedata::autoIncludes();
user::autoIncludes();


class RoleView extends View
{


  function __construct($base_dir = false){
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
  *
  * Update role form
  * @param request(id)
  * @return Form Html
  *
  **/
  function roleUpdateFormDefine( $request ){

    $roleModel = new RoleModel();
    $role = $roleModel->listItems( array('filters' => array('id' => $request[1] )))->fetch();
    if(!$role){
      Cogumelo::redirect( SITE_URL.'404' );
    }

    $form = $this->roleFormDefine( $role );
    return $form;
  }



  /**
   *
   * Create form fields and validations
   * @return object
   *
   **/

  function roleFormDefine( $dataVO = '' ) {

    $form = new FormController( 'roleForm', '/user/sendroleform' ); //actionform

    $form->setSuccess( 'redirect', '/' );

    $form->setField( 'id', array( 'type' => 'reserved', 'value' => null ) );
    $form->setField( 'name', array( 'placeholder' => 'Name' ) );
    $form->setField( 'description', array( 'type' => 'textarea', 'placeholder' => 'Descripción' ) );
    $form->setField( 'submit', array( 'type' => 'submit', 'value' => 'Save' ) );

    /******************************************************************************************** VALIDATIONS */
    $form->setValidationRule( 'name', 'required' );
    $form->loadVOValues( $dataVO );

    return $form;
  }

   /**
   *
   * Returns necessary html form
   * @param $form
   * @return string
   *
   **/
  function roleFormGet($form) {
    $form->saveToSession();

    $this->template->assign("roleFormOpen", $form->getHtmpOpen());
    $this->template->assign("roleFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("roleFormClose", $form->getHtmlClose());
    $this->template->assign("roleFormValidations", $form->getScriptCode());

    $this->template->setTpl('roleForm.tpl', 'user');

    return $this->template->execToString();
  }


  /**
   *
   * Example of an external action register
   *
   * @return void
   *
   **/
  /*function sendRoleForm() {

    $form = $this->actionRoleForm();
    $this->roleFormOk($form);

    if( $form->existErrors() ) {
      echo $form->jsonFormError();
    }
    else {
      echo $form->jsonFormOk();
    }
  }*/

  /**
   *
   * Assigns the forms validations
   * @return $form
   *
   **/
  function actionRoleForm() {
    $form = new FormController();
    if( $form->loadPostInput() ) {
      $form->validateForm();
    }
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recibidos.', 'formError' );
    }
    return $form;
  }

  /**
   *
   * Save Roles
   * @return $role
   *
   **/

  function roleFormOk( $form ) {
    //Si todo esta OK!
    $roleModel = false;

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();

      $roleModel = new RoleModel( $valuesArray );
      $roleModel->save();
    }
    return $roleModel;
  }


}

