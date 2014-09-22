<?php


class FormValidators implements Serializable {

  private $methods = array();
  private $messages = array();

  function __construct(){
  } //function __construct



  public function serialize() {
    $data = array();

    $data[] = $this->methods;
    $data[] = $this->messages;

    return serialize( $data );
  }

  public function unserialize( $dataSerialized ) {
    $data = unserialize( $dataSerialized );

    $this->methods = array_shift( $data );
    $this->messages = array_shift( $data );
  }

/*
  // http://jqueryvalidation.org/jQuery.validator.addMethod/
  public function addPhpMethod( $name, $method, $msg = false ) {
    $this->methods[ $name ] = $method;
    if ( method.length < 3 ) {
      $.validator.addClassRules( name, $.validator.normalizeRule( name ) );
    }
  }
*/

  /**
   * Metodo de validacion segun unha regla indicada
   *
   * @param string $ruleName
   * @param mixed $value
   * @param mixed $param (optinal)
   * @return bool $validate
   **/
  public function evaluateRule( $ruleName, $value, $fieldName, $param ) {
    $validate = false;

    $ruleMethod = 'val_'.$ruleName;
    if( method_exists( $this, $ruleMethod ) ) {
      //error_log( 'is_callable $this->'.$ruleMethod );
      $validate = $this->$ruleMethod( $value, $param );
    }
    else {
      error_log( 'NO EXIST $this->'.$ruleMethod );
    }

    return $validate;
  }


/**
 * Metodos de validacion
 *
 * @param mixed $value
 * @param mixed $param (optinal)
 * @return bool $validate
 **/


private function val_required( $value ) {
  $validate = true;
  if( is_array( $value ) ) {
    $validate = ( sizeof( $value ) > 0 );
  }
  else {
    $validate = ( $value !== '' );
  }
  // required implemented in FormController
  return $validate;
}

private function val_regex( $value, $param ) {
  return preg_match( $param, $value ) === 1;
}

private function val_email( $value ) {
  return preg_match( '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9]'.
    '(?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
    $value ) === 1;
}

private function val_url( $value ) {
  $azP = '[a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]';
  $azP2 = $azP.'|\d|-|\.|_|~';
  $rx2 = '%[\da-f]{2})|[!\$&\'\(\)\*\+,;=]|:';
  $rx3 = '\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]';
  return preg_match( '/^(https?|s?ftp):\/\/'.
    '(((('.$azP2.')|('.$rx2.')*@)?((('.$rx3.')\.('.$rx3.')\.('.$rx3.')\.('.$rx3.'))|'.
    '((('.$azP.'|\d)|(('.$azP.'|\d)('.$azP2.')*('.$azP.'|\d)))\.)+'.
    '(('.$azP.')|(('.$azP.')('.$azP2.')*('.$azP.')))\.?)(:\d*)?)'.
    '(\/((('.$azP2.')|('.$rx2.'|@)+(\/(('.$azP2.')|('.$rx2.'|@)*)*)?)?'.
    '(\?((('.$azP2.')|('.$rx2.'|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((('.$azP2.')|('.$rx2.'|@)|\/|\?)*)?$/i',
    $value ) === 1;
}

private function val_date( $value ) {
  return false;
}

private function val_dateISO( $value ) {
  return preg_match( '/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/',
    $value ) === 1;
}
  
private function val_dateMin( $dateMin, $dateCompare ) {  
  return (strtotime($dateMin) < strtotime($dateCompare));
} 
  
private function val_dateMax( $dateMax, $dateCompare ) {
  return (strtotime($dateMax) > strtotime($dateCompare));
} 
  
private function val_timeMin( $timeMin, $timeCompare ) {
  return (strtotime($timeMin) < strtotime($timeCompare));
}
  
private function val_timeMax( $timeMax, $timeCompare ) {
  return (strtotime($timeMax) < strtotime($timeCompare));
}  
  
  
private function val_number( $value ) {
  return preg_match( '/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/',
    $value ) === 1;
}

private function val_numberEU( $value ) {
  return preg_match( '/^-?\d+(,\d+)?$/',
    $value ) === 1;
}

private function val_digits( $value ) {
  return preg_match( '/^\d+$/',
    $value ) === 1;
}

private function val_creditcard( $value ) {
  return false;
}

// http://jqueryvalidation.org/minlength-method/
private function val_minlength( $value, $param ) {
  return strlen( $value ) >= $param;
}

// http://jqueryvalidation.org/maxlength-method/
private function val_maxlength( $value, $param ) {
  return strlen( $value ) <= $param;
}

// http://jqueryvalidation.org/min-method/
private function val_min( $value, $param ) {
  return $value >= $param;
}

// http://jqueryvalidation.org/max-method/
private function val_max( $value, $param ) {
  return $value <= $param;
}

private function val_equalTo( $value, $param ) {
  // equalTo implemented in FormController
  return true;
}

private function val_inArray( $value, $param ) {
  $validate = true;

  $values = $value;
  if( !is_array( $value ) ) {
    $values = array( $value );
  }

  foreach( $values as $val ) {
    if( !in_array( $val, $param ) ) {
      $validate = false;
      break;
    }
  }

  return $validate;
}







} // class FormValidators implements Serializable




























/*
  $errors = array();
  $values = array();
  $rules = array();


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
