<?php


form::load('controller/FormValidatorsExtender.php');


/**
  Evaluadores de las reglas de validación de campos de formulario.
  @package Module Form
*/
class FormValidators extends FormValidatorsExtender {

  private $methods = array();
  private $messages = array();

  public function __construct(){
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
    Base: http://jqueryvalidation.org/
  */

  /**
    Verifica si el valor de un campo cumple una regla segun los parametros establecidos
    @param string $fieldName Nombre del campo
    @param string $fieldValue Valor del campo
    @param string $ruleName Nombre de la regla
    @param mixed $ruleParams Parametros de la regla (opcional)
    @return boolean
  */
  public function evaluateRule( $fieldName, $fieldValue, $ruleName, $ruleParams ) {
    $validate = false;

    $ruleMethod = 'val_'.$ruleName;
    if( method_exists( $this, $ruleMethod ) ) {
      //error_log( 'is_callable $this->'.$ruleMethod );
      $validate = $this->$ruleMethod( $fieldValue, $ruleParams );
    }
    else {
      error_log( 'NO EXIST $this->'.$ruleMethod );
    }

    return $validate;
  }


  /**
    Metodos de validacion
    @param mixed $value
    @param mixed $param (optinal)
    @return bool $validate
  */
  private function val_regex( $value, $param ) {
    $validate = ( preg_match( $param, $value ) === 1 );
    return $validate;
  }

  private function val_required( $value ) {
    $validate = true;
    if( is_array( $value ) ) {
      $validate = ( count( $value ) > 0 );
    }
    else {
      $validate = ( $value !== false && $value !== '' );
    }
    return $validate;
  }

  private function val_email( $value ) {
    $regex = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9]'.
      '(?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';
    return( preg_match( $regex, $value ) === 1 );
  }

  private function val_url( $value ) {
    $azP = '[a-z]|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}]';
    $azP2 = $azP.'|\d|-|\.|_|~';
    $rx2 = '%[\da-f]{2})|[!\$&\'\(\)\*\+,;=]|:';
    $rx3 = '\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]';
    $regex = '/^(https?|s?ftp):\/\/'.
      '(((('.$azP2.')|('.$rx2.')*@)?((('.$rx3.')\.('.$rx3.')\.('.$rx3.')\.('.$rx3.'))|'.
      '((('.$azP.'|\d)|(('.$azP.'|\d)('.$azP2.')*('.$azP.'|\d)))\.)+'.
      '(('.$azP.')|(('.$azP.')('.$azP2.')*('.$azP.')))\.?)(:\d*)?)'.
      '(\/((('.$azP2.')|('.$rx2.'|@)+(\/(('.$azP2.')|('.$rx2.'|@)*)*)?)?'.
      '(\?((('.$azP2.')|('.$rx2.'|@)|[\x{E000}-\x{F8FF}]|\/|\?)*)?(#((('.$azP2.')|('.$rx2.'|@)|\/|\?)*)?$/iu';
    return( preg_match( $regex, $value ) === 1 );
  }

  private function val_date( $value ) {
    /*
    */
    return false;
  }

  private function val_dateISO( $value ) {
    return preg_match( '/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/',
      $value ) === 1;
  }

  private function val_dateMin( $value, $param ) {
    return (strtotime($value) > strtotime($param));
  }

  private function val_dateMax( $value, $param ) {
    return (strtotime($value) < strtotime($param));
  }

  private function val_timeMin( $value, $param ) {
    return (strtotime($value) > strtotime($param));
  }

  private function val_timeMax( $value, $param ) {
    return (strtotime($value) < strtotime($param));
  }

  private function val_dateTimeMin( $value, $param ) {
    return (strtotime($value) > strtotime($param));
  }

  private function val_dateTimeMax( $value, $param ) {
    return (strtotime($value) < strtotime($param));
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
    /*
    */
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
    return in_array( $value, $param );
  }

  private function val_notInArray( $value, $param ) {
    return !in_array( $value, $param );
  }


  private function val_maxfilesize( $value, $param ) {
    return( $value['validate'][ 'size' ] <= $param );
  }


  private function val_minfilesize( $value, $param ) {
    return( $value['validate'][ 'size' ] >= $param );
  }


  // http://jqueryvalidation.org/accept-method
  private function val_accept( $value, $param ) {
    $result = false;

    if( !is_array( $param ) ) {
      // Split param on commas in case we have multiple types we can accept
      $param = str_replace( ' ', '', $param );
      $param = explode( ',', $param );
    }

    foreach( $param as $test ) {
      if( $test === $value['validate'][ 'type' ] ) {
        $result = true;
        break;
      }
      else {
        $test = str_replace( '*', '.*', $test );
        if( preg_match( '#^'.$test.'$#', $value['validate'][ 'type' ] ) ) {
          $result = true;
          break;
        }
      }
    }

    return $result;
  }


  // http://jqueryvalidation.org/extension-method
  private function val_extension( $value, $param ) {
    if( !is_array( $param ) ) {
      // Split param on commas in case we have multiple extensions we can accept
      $param = str_replace( ' ', '', $param );
      $param = explode( ',', $param );
    }

    $tmpExt = '';
    $tmpExtPos = strrpos( $value['validate'][ 'name' ], '.' );
    if( $tmpExtPos > 0 ) { // Not FALSE or 0
      $tmpExt = substr( $value['validate'][ 'name' ], 1+$tmpExtPos );
    }

    // TODO: Cambiar in_array por regex

    return in_array( $tmpExt, $param );
  }



} // class FormValidators implements Serializable
