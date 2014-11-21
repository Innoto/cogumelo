<?php


class Validation {

  function __construct($inputData, ){

  }


}

/*
  $errors = array();
  $values = array();
  $rules = array();

  function __construct($inputData, ){

  }


  funciton valiedate() {
    return false;
  }


  //  String validation
  //
  function validateString( $validation_rules ) {
    $validation_rules['type_name'] = t("texto");

    return validateField($validation_rules);
  }

  //  Email validation
  //
  function validateEmail( $validation_rules ) {
    $validation_rules['type_name'] = t("número enteiro");
    $validation_rules['regex'] = '/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/';

    return validateField($validation_rules);
  }

  //  IP adress validation
  //
  function validateIp( $validation_rules ) {
    $validation_rules['type_name'] = t("dirección IP");
    $validation_rules['regex'] = '/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';

    return validateField($validation_rules);
  }

  //  URL validation
  //
  function validate( $validation_rules ) {
    $validation_rules['type_name'] = t("dirección web");
    $validation_rules['regex'] = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';

    return validateField($validation_rules);
  }

  //  Credit card validation
  //
  function validateCreditcard( $validation_rules ) {
    $validation_rules['type_name'] = t("tarxeta de crédito");
    $validation_rules['regex'] = '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/';

    return validateField($validation_rules);
  }


  //
  // Generic validation method
  //
  function validateField( $params = array() ){
    $error = false;
    $field_id = false;
    $field_name = false;
    $type_name = false;
    $data = false;

    if( array_key_exists('field_id', $params)) {
      $field_id = $params['field_id'];
    }
    if( array_key_exists('field_name', $params)) {
      $field_name = $params['field_name'];
    }
    if( array_key_exists('type_name', $params)) {
      $type_name = $params['type_name'];
    }
    if( array_key_exists('data', $params)) {
      $data = $params['data'];
    }

    if( array_key_exists('required', $params) && $data != '' && $data) {
      $error['error_type'] = 'required';
      $error['msg'] = sprintf( t("O campo %s é obligatorio"). $field_name );
    }

    if( array_key_exists('regex', $params)) {
      $regex = $params['regex'];

      if( !preg_match( $regex, $data) ){
        $error['error_type'] = 'regex';
        $error['msg'] = sprintf( t("O campo %s debe ser de tipo %s"). $field_name, $type_name );
      }
    }

    if($error != array() && array_key_exists('size_min', $params) &&  str_sizeof($data)<$params['size_min'] ) {
      $error['error_type'] = 'size_min';
      $error['msg'] = sprintf( t("O campo %s non pode ser menor de %s caracteres"). $field_name, $params['size_min'] );
    }
    if($error != array() &&  array_key_exists('size_max', $params)  &&  str_sizeof($data)>$params['size_max'] ) {
      $error['error_type'] = 'size_max';
      $error['msg'] = sprintf( t("O campo %s non pode ser maior de %s caracteres"). $field_name, $params['size_max'] );
    }
    if($error != array() &&  array_key_exists('value_min', $params) &&  $data<$params['value_min'] ) {
      $error['error_type'] = 'value_min';
      $error['msg'] = sprintf( t("O campo %s non pode ser inferior de %s"). $field_name, $params['value_max'] );
    }
    if($error != array() &&  array_key_exists('value_max', $params) &&  $data>$params['value_max']) {
      $error['error_type'] = 'valie_max';
      $error['msg'] = sprintf( t("O campo %s non pode ser maior de %s"). $field_name, $params['value_max'] );
    }



    // return
    if($error != array() ) {
      $error['field_id'] = $field_id;
      return $error;
    }
    else{
      return false;
    }
  }

*/
