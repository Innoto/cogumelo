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
  public function val_regex( $value, $param ) {
    $validate = ( preg_match( $param, $value ) === 1 );
    return $validate;
  }

  public function val_required( $value ) {
    $validate = true;
    if( is_array( $value ) ) {
      $validate = ( count( $value ) > 0 );
    }
    else {
      $validate = ( $value !== false && $value !== '' );
    }
    return $validate;
  }

  public function val_email( $value ) {
    $regex = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9]'.
      '(?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';
    return( preg_match( $regex, $value ) === 1 );
  }

  public function val_url( $value ) {
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

  public function val_date( $value ) {
    /*
    */
    return false;
  }

  public function val_dateISO( $value ) {
    return preg_match( '/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/', $value ) === 1;
  }

  public function val_dateMin( $value, $param ) {
    return (strtotime($value) > strtotime($param));
  }

  public function val_dateMax( $value, $param ) {
    return (strtotime($value) < strtotime($param));
  }

  public function val_timeMin( $value, $param ) {
    return (strtotime($value) > strtotime($param));
  }

  public function val_timeMax( $value, $param ) {
    return (strtotime($value) < strtotime($param));
  }

  public function val_dateTime( $value ) {
    return preg_match( '/^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/', $value ) === 1;
  }

  public function val_dateTimeMin( $value, $param ) {
    return (strtotime($value) > strtotime($param));
  }

  public function val_dateTimeMax( $value, $param ) {
    return (strtotime($value) < strtotime($param));
  }

  public function val_number( $value ) {
    return preg_match( '/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/',
      $value ) === 1;
  }

  public function val_numberEU( $value ) {
    return preg_match( '/^-?\d+(,\d+)?$/',
      $value ) === 1;
  }

  public function val_digits( $value ) {
    return preg_match( '/^\d+$/',
      $value ) === 1;
  }


  public function val_creditcard( $value ) {
    /*
    */
    return false;
  }

  public function val_dni( $value ) {
    $result = false;

    if( preg_match('/^([0-9]{8})([A-Z])$/i', $value, $match ) ) {
      $numero   = $match[1];
      $letraDni = strtoupper( $match[2] );

      if( $letraDni === substr( 'TRWAGMYFPDXBNJZSQVHLCKE', $numero%23, 1 ) ) {
        $result = true;
      }
    }

    return $result;
  }

  public function val_nie( $value ) {
    $result = false;

    if( preg_match('/^([XYZ]?)([0-9]{7})([A-Z])$/i', $value, $match ) ) {
      $letraNie = strtoupper( $match[1] );
      $numero   = $match[2];
      $letraDni = strtoupper( $match[3] );

      // Ajustes NIE
      $numero = strtr( $letraNie, 'XYZ', '012' ).$numero;

      if( $letraDni === substr( 'TRWAGMYFPDXBNJZSQVHLCKE', $numero%23, 1 ) ) {
        $result = true;
      }
    }

    return $result;
  }

  public function val_nif( $value ) {
    $result = false;

    if( preg_match('/^([A-HJ-NP-SUVW])([0-9]{7})([A-J0-9])$/i', $value, $match ) ) {
      $letraTipo = strtoupper( $match[1] );
      $numero    = $match[2];
      $letraCtrl = strtoupper( $match[3] );

      $sum = 0;
      // summ all even digits
      for( $i=1; $i<7; $i+=2 ) {
        $sum += substr( $numero, $i, 1 );
      }
      // x2 all odd position digits and sum all of them
      for( $i=0; $i<7; $i+=2 ) {
        $t = substr( $numero, $i, 1 ) * 2;
        $sum += ($t>9) ? 1 + ( $t%10 ) : $t;
      }

      //Rest to 10 the last digit of the sum
      $control = 10 - ( $sum%10 );

      //the control can be a numbber or letter
      if( $letraCtrl == $control || $letraCtrl == substr( 'JABCDEFGHI', $control, 1 ) ) {
        $result = true;
      }
    }

    return $result;
  }



  // http://jqueryvalidation.org/minlength-method/
  public function val_minlength( $value, $param ) {
    return mb_strlen( $value ) >= $param;
  }

  // http://jqueryvalidation.org/maxlength-method/
  public function val_maxlength( $value, $param ) {
    return mb_strlen( $value ) <= $param;
  }

  // http://jqueryvalidation.org/min-method/
  public function val_min( $value, $param ) {
    return $value >= $param;
  }

  // http://jqueryvalidation.org/max-method/
  public function val_max( $value, $param ) {
    return $value <= $param;
  }

  public function val_equalTo( $value, $param ) {
    // equalTo implemented in FormController
    return true;
  }

  public function val_inArray( $value, $param ) {
    return in_array( $value, $param );
  }

  public function val_notInArray( $value, $param ) {
    return !in_array( $value, $param );
  }


  public function val_maxfilesize( $value, $param ) {
    return( $value['validate'][ 'size' ] <= $param );
  }


  public function val_minfilesize( $value, $param ) {
    return( $value['validate'][ 'size' ] >= $param );
  }


  // http://jqueryvalidation.org/accept-method
  public function val_accept( $value, $param ) {
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
  public function val_extension( $value, $param ) {
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
