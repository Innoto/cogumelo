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
      //error_log('FormValidators: is_callable $this->'.$ruleMethod );
      $validate = $this->$ruleMethod( $fieldValue, $ruleParams );
    }
    else {
      error_log('FormValidators: NO EXIST $this->'.$ruleMethod );
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
      '(?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/u';
    return( preg_match( $regex, $value ) === 1 );
  }

  public function val_passwordStrength( $value ) {

    $response = (preg_match('/[A-Z]/', $value) &&
    preg_match('/[a-z]/', $value) &&
    preg_match('/[0-9]/', $value) &&
    preg_match('/\W/', $value) &&
    preg_match('/^.{8,16}$/', $value));

    return $response;
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

  public function val_notUrl( $value ) {
    return( !$this->val_url($value) );
  }

  public function val_urlYoutube( $value ) {
    $regex = '/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/';
    return( ( preg_match( $regex, $value ) === 1 ) || empty($value) );
  }

  public function val_date( $value ) {
    /*
    */
    return false;
  }

  public function val_dateUE( $value ) {
    return preg_match( '/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-](\d{4})$/', $value ) === 1;
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
    return preg_match( '/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/', $value ) === 1;
  }

  public function val_numberEU( $value ) {
    return preg_match( '/^-?\d+(,\d+)?$/', $value ) === 1;
  }

  public function val_numberEUDec( $value, $param ) {
    return preg_match( '/^-?\d+(,\d{0,'.$param.'})?$/', $value ) === 1;
  }

  public function val_minEU( $value, $param ) {
    $locInfo = localeconv();
    $value = strtr( $value, '.,', $locInfo['decimal_point'] );
    $param = strtr( $param, '.,', $locInfo['decimal_point'] );
    return $value >= $param;
  }

  public function val_maxEU( $value, $param ) {
    $locInfo = localeconv();
    $value = strtr( $value, '.,', $locInfo['decimal_point'] );
    $param = strtr( $param, '.,', $locInfo['decimal_point'] );
    return $value <= $param;
  }





  public function val_digits( $value ) {
    return preg_match( '/^\d+$/', $value ) === 1;
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
      $letraDni = mb_strtoupper( $match[2] );

      if( $letraDni === mb_substr( 'TRWAGMYFPDXBNJZSQVHLCKE', $numero%23, 1 ) ) {
        $result = true;
      }
    }

    return $result;
  }

  public function val_nie( $value ) {
    $result = false;

    if( preg_match('/^([XYZ]?)([0-9]{7})([A-Z])$/i', $value, $match ) ) {
      $letraNie = mb_strtoupper( $match[1] );
      $numero   = $match[2];
      $letraDni = mb_strtoupper( $match[3] );

      // Ajustes NIE
      $numero = strtr( $letraNie, 'XYZ', '012' ).$numero;

      if( $letraDni === mb_substr( 'TRWAGMYFPDXBNJZSQVHLCKE', $numero%23, 1 ) ) {
        $result = true;
      }
    }

    return $result;
  }

  public function val_nif( $value ) {
    $result = false;

    if( preg_match('/^([A-HJ-NP-SUVW])([0-9]{7})([A-J0-9])$/i', $value, $match ) ) {
      $letraTipo = mb_strtoupper( $match[1] );
      $numero    = $match[2];
      $letraCtrl = mb_strtoupper( $match[3] );

      $sum = 0;
      // summ all even digits
      for( $i=1; $i<7; $i+=2 ) {
        $sum += mb_substr( $numero, $i, 1 );
      }
      // x2 all odd position digits and sum all of them
      for( $i=0; $i<7; $i+=2 ) {
        $t = mb_substr( $numero, $i, 1 ) * 2;
        $sum += ($t>9) ? 1 + ( $t%10 ) : $t;
      }

      //Rest to 10 the last digit of the sum
      $control = 10 - ( $sum%10 );

      //the control can be a numbber or letter
      if( $letraCtrl == $control || $letraCtrl == mb_substr( 'JABCDEFGHI', $control, 1 ) ) {
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

  public function val_passwordStrength( $value ) {

    $response = (preg_match('/[A-Z]/', $value) &&
    preg_match('/[a-z]/', $value) &&
    preg_match('/[0-9]/', $value) &&
    preg_match('/\W/', $value) &&
    preg_match('/^.{8,16}$/', $value));

    return $response;
  }













  public function val_maxfilesize( $value, $param ) {
    // error_log('FormValidators: val_maxfilesize '. json_encode( $value ). '    PARAM:' .$param );
    $result = true;

    if( !isset( $value['multiple'] ) ) {
      cogumelo::debug('FormValidators: val_maxfilesize '.$value['validate']['size'].'<='.$param );
      $result = ( isset( $value['validate']['size'] ) && $value['validate']['size'] <= $param );
    }
    else {
      foreach( $value['multiple'] as $multiId => $fileInfo ) {
        cogumelo::debug('FormValidators: val_maxfilesize multi '.$multiId.' status: '.$fileInfo['status'] );

        if( $fileInfo['status'] !== 'DELETE' ) {
          cogumelo::debug('FormValidators: val_maxfilesize multi '.$multiId.': '.$fileInfo['validate']['size'].'<='.$param );
          if( !isset( $fileInfo['validate']['size'] ) || $fileInfo['validate']['size'] > $param ) {
            $result = false;
            break;
          }
        }
      }
    }

    return $result;
  }


  public function val_minfilesize( $value, $param ) {
    // error_log('FormValidators: val_minfilesize '. json_encode( $value ). '    PARAM:' .$param );

    $result = true;

    if( !isset( $value['multiple'] ) ) {
      cogumelo::debug('FormValidators: val_minfilesize '.$value['validate']['size'].'>='.$param );
      $result = ( isset( $value['validate']['size'] ) && $value['validate']['size'] >= $param );
    }
    else {
      foreach( $value['multiple'] as $multiId => $fileInfo ) {
        cogumelo::debug('FormValidators: val_minfilesize multi '.$multiId.' status: '.$fileInfo['status'] );

        if( $fileInfo['status'] !== 'DELETE' ) {
          cogumelo::debug('FormValidators: val_minfilesize multi '.$multiId.': '.$fileInfo['validate']['size'].'<='.$param );
          if( !isset( $fileInfo['validate']['size'] ) || $fileInfo['validate']['size'] < $param ) {
            $result = false;
            break;
          }
        }
      }
    }

    return $result;
  }


  public function val_multipleMax( $value, $param ) {
    // error_log('FormValidators: val_multipleMax '. json_encode( $value ). '    PARAM:' .$param );

    $numFiles = 0;
    if( isset( $value['multiple'] ) && is_array( $value['multiple'] ) && count( $value['multiple'] ) ) {
      foreach( $value['multiple'] as $multiId => $fileInfo ) {
        cogumelo::debug('FormValidators: val_multipleMax multi '.$multiId.' status: '.$fileInfo['status'] );
        $numFiles += ( $fileInfo['status'] !== 'DELETE' ) ? 1 : 0;
      }
    }

    return( $numFiles <= $param );
  }


  public function val_multipleMin( $value, $param ) {
    // error_log('FormValidators: val_multipleMin '. json_encode( $value ). '    PARAM:' .$param );

    $result = false;

    if( !empty( $value['multiple']['0']['validate']['partial'] ) ) {
      // Contenido parcial. No puede aplicarse este validador
      $result = true;
    }
    else {
      $numFiles = 0;
      if( isset( $value['multiple'] ) && is_array( $value['multiple'] ) && count( $value['multiple'] ) ) {
        foreach( $value['multiple'] as $multiId => $fileInfo ) {
          cogumelo::debug('FormValidators: val_multipleMin multi '.$multiId.' status: '.$fileInfo['status'] );
          $numFiles += ( $fileInfo['status'] !== 'DELETE' ) ? 1 : 0;
        }
      }
      $result = ( $numFiles >= $param );
    }

    return $result;
  }


  public function val_fileRequired( $value, $param ) {
    cogumelo::debug('FormValidators: val_fileRequired '. json_encode( $value ). '    PARAM:' .$param );

    $result = false;

    if( !isset( $value['multiple'] ) ) {
      cogumelo::debug('FormValidators: val_fileRequired (size) '.$value['validate']['size'] );
      $result = isset( $value['validate']['size'] );
    }
    else {
      if( !empty( $value['multiple']['0']['validate']['partial'] ) ) {
        // Contenido parcial. No puede aplicarse este validador
        cogumelo::debug('FormValidators: val_fileRequired (Contenido parcial)' );
        $result = true;
      }
      else {
        $numFiles = 0;
        if( isset( $value['multiple'] ) && is_array( $value['multiple'] ) && count( $value['multiple'] ) ) {
          foreach( $value['multiple'] as $multiId => $fileInfo ) {
            cogumelo::debug('FormValidators: val_fileRequired multi '.$multiId.' status: '.$fileInfo['status'] );
            $numFiles += ( $fileInfo['status'] !== 'DELETE' ) ? 1 : 0;
          }
        }
        cogumelo::debug('FormValidators: val_fileRequired (numFiles) '.$numFiles );
        $result = ( $numFiles > 0 );
      }
    }

    return $result;
  }





  // http://jqueryvalidation.org/accept-method
  public function val_accept( $value, $param ) {
    // error_log('FormValidators: val_accept' );
    $result = true;

    if( !is_array( $param ) ) {
      // Split param on commas in case we have multiple types we can accept
      $param = str_replace( ' ', '', $param );
      $param = explode( ',', $param );
    }


    $filesInfo = isset( $value['multiple'] ) ? $value['multiple'] : [ $value ];
    foreach( $filesInfo as $fileInfo ) {
      $fileResult = false;

      if( $fileInfo['status'] !== 'DELETE' ) {
        cogumelo::debug('FormValidators: val_accept '.json_encode($fileInfo['validate']).':'.json_encode($param) );

        foreach( $param as $test ) {
          if( $test === $fileInfo['validate']['type'] ) {
            $fileResult = true;
            break;
          }
          else {
            $testRegex = '#^'.str_replace( '*', '.*', $test ).'$#';
            if( preg_match( $testRegex, $fileInfo['validate']['type'] ) ) {
              $fileResult = true;
              break;
            }
          }
        }

        if( !$fileResult ) {
          $result = false;
          break;
        }
      } // if( $fileInfo['status'] !== 'DELETE' )
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
    $tmpExtPos = mb_strrpos( $value['validate'][ 'name' ], '.' );
    if( $tmpExtPos > 0 ) { // Not FALSE or 0
      $tmpExt = mb_substr( $value['validate']['name'], 1+$tmpExtPos );
    }

    // TODO: Cambiar in_array por regex

    return in_array( $tmpExt, $param );
  }



} // class FormValidators implements Serializable
