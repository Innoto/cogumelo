<?php


form::load('controller/FormValidatorsExtender.php');


class FormValidators extends FormValidatorsExtender {

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
  // Base: http://jqueryvalidation.org/
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
      $validate = ( $value !== false && $value !== '' );
    }
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
    /*
     *
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
     *
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
    return( $value[ 'size' ] <= $param );
  }


  private function val_minfilesize( $value, $param ) {
    return( $value[ 'size' ] >= $param );
  }


  private function val_accept( $value, $param ) {
    if( !is_array( $param ) ) {
      // Split param on commas in case we have multiple types we can accept
      $param = str_replace( ' ', '', $param );
      $param = explode( ',', $param );
    }

    return in_array( $value[ 'type' ], $param );
  }


  private function val_extension( $value, $param ) {
    // Older "accept" file extension method. Old docs: http://docs.jquery.com/Plugins/Validation/Methods/accept

    if( !is_array( $param ) ) {
      // Split param on commas in case we have multiple extensions we can accept
      $param = str_replace( ' ', '', $param );
      $param = explode( ',', $param );
    }

    $tmpExt = '';
    $tmpExtPos = strrpos( $value[ 'name' ], '.' );
    if( $tmpExtPos > 0 ) { // Not FALSE or 0
      $tmpExt = substr( $value[ 'name' ], 1+$tmpExtPos );
      if( ( mb_strlen( $tmpExt, 'UTF-8' ) > 5 ) || ( preg_match( '/^[-0-9A-Z_\.]+$/i', $tmpExt ) !== 1 ) ) {
        error_log( 'ALERTA: La Extensión del fichero parece anormal: '.$tmpExt );
      }
    }

    return in_array( $tmpExt, $param );
  }








} // class FormValidators implements Serializable
