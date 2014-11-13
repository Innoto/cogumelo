<?php


/**
 * undocumented class
 *
 * @package default
 **/
class FormController implements Serializable {


  private $name = false;
  private $id = false;
  private $cgIntFrmId = false;
  private $action = false;
  private $success = false;
  private $method = 'post';
  private $enctype = 'multipart/form-data';
  private $fields = array();
  private $rules = array();
  private $messages = array();

  // POST submit
  private $postData = null;
  private $postValues = null;
  private $validationObj = null;
  private $fieldErrors = array();
  private $formErrors = array();

  private $replaceAcents = array(
    'from' => array( 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ' ),
    'to'   => array( 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'A', 'a', 'O', 'o' )
  );


  function __construct( $name = false, $action = false ) {
    error_log( 'Instanciando FormController sen datos de POST' );
    if( $name !== false ) {
      $this->name = $name;
      $this->id = $name;
    }
    if( $action !== false ) {
      $this->action = $action;
    }
    $this->cgIntFrmId = crypt( uniqid().'---'.session_id(), 'cf' );
    $this->setField( 'cgIntFrmId', array( 'type' => 'text', 'value' => $this->cgIntFrmId ) );
  }


  /**
   * Recupera todos los datos importantes en un array serializado
   *
   * @return string
   **/
  public function serialize() {
    $data = array();

    $data[] = $this->name;
    $data[] = $this->id;
    $data[] = $this->cgIntFrmId;
    $data[] = $this->action;
    $data[] = $this->success;
    $data[] = $this->method;
    $data[] = $this->enctype;
    $data[] = $this->fields;
    $data[] = $this->rules;
    $data[] = $this->messages;
    $data[] = $this->postValues;

    return serialize( $data );
  }


  /**
   * Carga todos los datos importantes desde el string serializado
   *
   * @param string $dataSerialized Datos del form serializados
   **/
  public function unserialize( $dataSerialized ) {
    $data = unserialize( $dataSerialized );

    $this->name = array_shift( $data );
    $this->id = array_shift( $data );
    $this->cgIntFrmId = array_shift( $data );
    $this->action = array_shift( $data );
    $this->success = array_shift( $data );
    $this->method = array_shift( $data );
    $this->enctype = array_shift( $data );
    $this->fields = array_shift( $data );
    $this->rules = array_shift( $data );
    $this->messages = array_shift( $data );
    $this->postValues = array_shift( $data );
  }


  /**
   * Guarda todos los datos importantes (serializados) en sesion
   **/
  public function saveToSession() {
    $formSessionId = 'CGFSI_'.$this->getIntFrmId();
    $_SESSION[ $formSessionId ] = $this->serialize();
    //return $formSessionId;
  }


  /**
   * Recupera de sesion todos los datos importantes
   * @param string $cgIntFrmId ID interno del formulario
   * @return boolean
   **/
  public function loadFromSession( $cgIntFrmId ) {
    $result = false;
    $formSessionId = 'CGFSI_'.$cgIntFrmId;
    if( isset( $_SESSION[ $formSessionId ] ) ) {
      $this->unserialize( $_SESSION[ $formSessionId ] );
      $result = ( $this->cgIntFrmId === $cgIntFrmId );
    }
    return $result;
  }


  /**
   * Captura los datos enviados por el navegador, recupera parametros del form y le carga los input
   *
   * @return boolean
   **/
  public function loadPostInput() {
    $result = false;

    $postDataJson = file_get_contents( 'php://input' );
    error_log( $postDataJson );
    if( $postDataJson !== false && strpos( $postDataJson, '{' )===0 ) {
      $postData = json_decode( $postDataJson, true );
      error_log( print_r( $postData, true ) );

      // recuperamos FORM de sesion y añadimos los datos enviados
      if( $this->loadPostSession( $postData ) ) {
        $this->loadPostValues( $postData );
        $result = true;
      }
    }

    return $result;
  }


  /**
   * Recupera de sesion todos los datos importantes (serializados)
   *
   * @param array $formPost Datos enviados por el navegador convertidos a array
   * @return boolean
   **/
  public function loadPostSession( $formPost ) {
    $result = false;
    if( $formPost !== false && isset( $formPost[ 'cgIntFrmId' ] ) ) {
      $result = $this->loadFromSession( $formPost[ 'cgIntFrmId' ] );
    }
    return $result;
  }


  /**
   * Carga los valores de los input del navegador
   *
   * @param array $formPost Datos enviados por el navegador convertidos a array
   **/
  public function loadPostValues( $formPost ) {
    $this->postValues = $formPost;
    foreach( $formPost as $key => $val ){
      if( array_key_exists( $key, $this->fields ) ){
        $this->fields[ $key ][ 'value' ] = $val;
      }
    }
  }


  /**
   * Carga los valores del VO
   *
   * @param VO $dataVO Datos cargados por el programa
   **/
  public function loadVOValues( $dataVO ) {
    if( gettype( $dataVO ) == "object" ) {
      foreach( $dataVO->getKeys() as $keyVO ) {
        $this->setFieldValue( $keyVO, $dataVO->getter( $keyVO ) );
      }
    }
  }


  /**
   * Define un campo del formulario y, opcionalmente, con sus parametros
   *
   * @param string $fieldName Nombre del campo
   * @param array $params Opcional. Parametros: id, type, label, title, options, placeholder, size,
   *   cols, rows, multiple, readonly, ...
   **/
  public function setField( $fieldName, $params = false ) {
    $this->fields[$fieldName]['name'] = $fieldName;
    if( $params ) {
      foreach( $params as $key => $value ) {
        $this->fields[$fieldName][$key] = $value;
      }
    }
    if( !isset( $this->fields[$fieldName]['type'] ) ) {
      $this->fields[$fieldName]['type'] = 'text';
    }

    // Creamos ya algunas reglas en funcion del tipo
    switch( $this->fields[$fieldName]['type'] ) {
      case 'select':
        $this->setValidationRule( $this->fields[$fieldName]['name'], 'inArray',
          array_keys( $this->fields[$fieldName]['options'] ) );
        break;
      case 'checkbox':
      case 'radio':
        $this->setValidationRule( $this->fields[$fieldName]['name'], 'inArray',
          array_keys( $this->fields[$fieldName]['options'] ) );
        break;
      case 'file':
        $maxFileSize = $this->phpIni2Bytes( ini_get('upload_max_filesize') );
        if( ini_get('post_max_size') != '0' ) {
          $maxFileSize = min( $maxFileSize, $this->phpIni2Bytes( ini_get('post_max_size') ) );
        }
        if( ini_get('memory_limit') != '-1' ) {
          $maxFileSize = min( $maxFileSize, $this->phpIni2Bytes( ini_get('memory_limit') ) );
        }
        $this->setValidationRule( $this->fields[$fieldName]['name'], 'maxfilesize', $maxFileSize );
        break;
    }
  } // function setField


  /**
   * Convierte el tamaño de memoria de formato php.ini a bytes
   *
   * @param string $size Tamaño de la memoria configurada en php.ini
   * @return integer
   **/
  private function phpIni2Bytes( $size ) {
    if( preg_match( '/([\d\.]+)([KMG])/i', $size, $match ) ) {
      $pos = array_search( $match[2], array( 'K', 'M', 'G' ) );
      if( $pos !== false ) {
        $size = $match[1] * pow( 1024, $pos + 1 );
      }
    }
    return $size;
  }


  /**
   * Añade tareas para ejecutar en el navegador al finalizar bien el submit
   *
   * @param string $name Clave del suceso: accept, redirect, ...
   * @param string $success Contenido del evento: Msg, url, ...
   **/
  public function setSuccess( $name, $success ) {
    error_log( 'setSuccess: name='. $name . ' success=' .  $success );
    $this->success[ $name ] = $success;
  }


  /**
   * Recupera las tareas que se han definido para el navegador al finalizar bien el submit
   *
   * @return array
   **/
  public function getSuccess() {
    error_log( 'getSuccess: ' . print_r( $this->success, true ) );
    return $this->success;
  }


  /**
   * Establece una regla de validacion para un campo
   *
   * @param string $fieldName Nombre del campo
   * @param string $ruleName Nombre de la regla
   * @param mixed $ruleParams
   **/
  public function setValidationRule( $fieldName, $ruleName, $ruleParams = true ) {
    $this->rules[$fieldName][$ruleName] = $ruleParams;
  }


  /**
   * Establece el mensaje para un campo para el JQueryValidate
   *
   * @param string $fieldName Nombre del campo
   * @param string $msg
   **/
  public function setValidationMsg( $fieldName, $msg ) {
    $this->messages[$fieldName] = $msg;
  }


  /**
   * Recupera el ID interno del form
   *
   * @return string
   **/
  public function getIntFrmId() {
    return $this->cgIntFrmId;
  }


  /**
   * Recupera todo el html y js que forman el form
   *
   * @return string
   **/
  public function getHtmlForm() {
    $html='';

    $html .= $this->getHtmpOpen()."\n";
    $html .= $this->getHtmlFields()."\n";
    $html .= $this->getHtmlClose()."\n";

    $html .= $this->getJqueryValidationJS()."\n";

    return $html;
  }


  /**
   * Recupera el html de la apertura del form
   *
   * @return string
   **/
  public function getHtmpOpen() {
    $html='';

    $html .= '<form name="'.$this->name.'" id="'.$this->id.'" sg="'.$this->getIntFrmId().'"';
    if( $this->action ) {
      $html .= ' action="'.$this->action.'"';
    }
    $html .= ' method="'.$this->method.'">';

    $this->saveToSession(); // Guardamos en sesion de forma automatica al comenzar a generar el formulario

    return $html;
  }


  /**
   * Recupera el html de todos los campos del form
   *
   * @return string
   **/
  public function getHtmlFields() {
    $html = '';
    foreach( $this->fields as $fieldName => $fieldParams ) {
      $html .= '<div class="ffn-'.$fieldName.'">'.$this->getHtmlField($fieldName)."</div>\n";
    }
    return $html;
  }


  /**
   * Recupera el html de todos los campos del form (por campos)
   *
   * @return array
   **/
  public function getHtmlFieldsArray() {
    $html = array();
    foreach( $this->fields as $fieldName => $fieldParams ) {
      $html[] = '<div class="ffn-'.$fieldName.'">'.$this->getHtmlField($fieldName)."</div>\n";
    }
    return $html;
  }


  /**
   * Recupera el html de un campo del form
   *
   * @param string $fieldName Nombre del campo
   * @return string
   **/
  public function getHtmlField( $fieldName ) {
    $html = '';

    $htmlFieldArray = $this->getHtmlFieldArray( $fieldName );

    if( isset( $htmlFieldArray['label'] ) ) {
      $html .= $htmlFieldArray['label']."\n";
    }
    switch( $htmlFieldArray['fieldType'] ) {
      case 'select':
        $html .= $htmlFieldArray['inputOpen']."\n";
        foreach( $htmlFieldArray['options'] as $optionAndText ) {
          $html .= $optionAndText['input']."\n";
        }
        $html .= $htmlFieldArray['inputClose'];
        break;
      case 'checkbox':
      case 'radio':
        foreach( $htmlFieldArray['options'] as $inputAndText ) {
          //$html .= $inputAndText['input'].$inputAndText['label'];
          $html .= '<label>'.$inputAndText['input'].$inputAndText['text'].'</label>';
        }
        $html .= '<span class="JQVMC-'.$fieldName.'-error JQVMC-error"></span>';
        break;
      case 'textarea':
        $html .= $htmlFieldArray['inputOpen'] . $htmlFieldArray['value'] . $htmlFieldArray['inputClose'];
        break;
      case 'reserved':
        break;
      default:
        $html .= $htmlFieldArray['input'];
        break;
    }

    return $html;
  } // function getHtmlField


  /**
   * Recupera el html de un campo del form en fragmentos
   *
   * @param string $fieldName Nombre del campo
   * @return array
   **/
  public function getHtmlFieldArray( $fieldName ) {
    $html = array();

    $field = $this->fields[$fieldName];

    $html['fieldType'] = $field['type'];

    if( isset( $field['label'] ) ) {
      $html['label'] = '<label';
      $html['label'] .= isset( $field['id'] ) ? ' for="'.$field['id'].'"' : '';
      $html['label'] .= isset( $field['class'] ) ? ' class="'.$field['class'].'"' : '';
      $html['label'] .= isset( $field['style'] ) ? ' style="'.$field['style'].'"' : '';
      $html['label'] .= '>'.$field['label'].'</label>';
    }

    $attribs = '';
    $attribs .= isset( $field['id'] )    ? ' id="'.$field['id'].'"' : '';
    $attribs .= isset( $field['class'] ) ? ' class="'.$field['class'].'"' : '';
    $attribs .= isset( $field['style'] ) ? ' style="'.$field['style'].'"' : '';
    $attribs .= isset( $field['title'] ) ? ' title="'.$field['title'].'"' : '';
    $attribs .= isset( $field['placeholder'] ) ? ' placeholder="'.$field['placeholder'].'"' : '';
    $attribs .= isset( $field['maxlength'] ) ? ' maxlength="'.$field['maxlength'].'"' : '';
    $attribs .= isset( $field['size'] ) ? ' size="'.$field['size'].'"' : '';
    $attribs .= isset( $field['cols'] ) ? ' cols="'.$field['cols'].'"' : '';
    $attribs .= isset( $field['rows'] ) ? ' rows="'.$field['rows'].'"' : '';
    $attribs .= isset( $field['multiple'] ) ? ' multiple="multiple"' : '';
    $attribs .= isset( $field['readonly'] ) ? ' readonly="readonly"' : '';
    $attribs .= isset( $field['disabled'] ) ? ' disabled="disabled"' : '';
    $attribs .= isset( $field['hidden'] ) ? ' hidden="hidden"' : '';

    switch( $field['type'] ) {
      case 'select':
        $html['inputOpen'] = '<select name="'.$field['name'].'"'. $attribs.'>';

        $html['options'] = array();
        foreach( $field['options'] as $val => $text ) {
          $html['options'][$val] = array(
            'input' => '<option value="'.$val.'">'.$text.'</option>',
            'text' => $text
            );
        }
        // Colocamos los selected
        if( isset( $field['value'] ) ) {
          $values = is_array( $field['value'] ) ? $field['value'] : array( $field['value'] );
          foreach( $values as $val ) {
            $html['options'][$val]['input'] = str_replace( 'option value="'.$val.'"',
              'option value="'.$val.'" selected="selected"', $html['options'][$val]['input'] );
            if( !isset( $field['multiple'] ) ) {
              break; // Si no es multiple, solo puede tener 1 valor
            }
          }
        }

        $html['inputClose'] = '</select><!-- select '.$field['name'].' -->';

        // Creamos ya la regla que controla el contenido
        $this->setValidationRule( $field['name'], 'inArray', array_keys( $field['options'] ) );
        break;

      case 'checkbox':
      case 'radio':
        $html['options'] = array();
        foreach( $field['options'] as $val => $text ) {
          $html['options'][$val] = array();
          $html['options'][$val]['input'] = '<input name="'.$field['name'].'" value="'.$val.'"'.
            ' type="'.$field['type'].'"'.$attribs.'>';
          $html['options'][$val]['text'] = $text;
          $html['options'][$val]['label'] = $text!='' ? '<label>'.$text.'</label>' : '';
        }
        // Colocamos los checked
        if( isset( $field['value'] ) ) {
          $values = is_array( $field['value'] ) ? $field['value'] : array( $field['value'] );
          foreach( $values as $val ) {
            $html['options'][$val]['input'] = str_replace( 'name="'.$field['name'].'" value="'.$val.'"',
              'name="'.$field['name'].'" value="'.$val.'" checked="checked"', $html['options'][$val]['input'] );
            if( $field['type']=='radio' ) {
              break; // Radio solo puede tener 1 valor
            }
          }
        }

        // Creamos ya la regla que controla el contenido
        $this->setValidationRule( $field['name'], 'inArray', array_keys( $field['options'] ) );
        break;

      case 'textarea':
        $html['inputOpen'] = '<textarea name="'.$field['name'].'"'.$attribs.'>';
        $html['value'] = isset( $field['value'] ) ? $field['value'] : '';
        $html['inputClose'] = '</textarea>';
        break;

      case 'file':
        $html['input'] = '<input name="'.$field['name'].'"';
        $html['input'] .= isset( $field['value'] ) ? ' value="'.$field['value'].'"' : '';
        $html['input'] .= ' type="'.$field['type'].'"'.$attribs.'>';
        break;

      case 'reserved':
        break;

      default:
        // button, file, hidden, password, range, text
        // color, date, datetime, datetime-local, email, image, month, number, search, tel, time, url, week
        $html['input'] = '<input name="'.$field['name'].'"';
        $html['input'] .= isset( $field['value'] ) ? ' value="'.$field['value'].'"' : '';
        $html['input'] .= ' type="'.$field['type'].'"'.$attribs.'>';
        break;
    }

    return $html;
  } // function getHtmlFieldArray


  /**
   * Recupera el html del cierre del form
   *
   * @return string
   **/
  public function getHtmlClose() {
    $html = '</form><!-- '.$this->name.' -->';
    return $html;
  }


  /**
   * Recupera el html con el JS del form
   *
   * @return string
   **/
  public function getJqueryValidationJS() {
    $html = '';

    $separador = '';

    $html .= '<!-- Validate form '.$this->name.' -->'."\n";
    $html .= '<script>'."\n";

    $html .= '$().ready(function() {'."\n";

    $html .= '  $validateForm_'.$this->id.' = setValidateForm( "'.$this->id.'", ';
    $html .= ( count( $this->rules ) > 0 ) ? json_encode( $this->rules ) : 'false';
    $html .= ', ';
    $html .= ( count( $this->messages ) > 0 ) ? json_encode( $this->messages ) : 'false';
    $html .= ' );'."\n";

    /*
    if( count( $this->messages ) > 0 ) {
      $html .= $separador.'    messages: '.json_encode( $this->messages )."\n";
      $separador = '    ,'."\n";
    }
    */
    $html .= '  console.log( $validateForm_'.$this->id.' );'."\n";

    $html .= '});'."\n";
    $html .= '</script>'."\n";

    $html .= '<!-- Validate form '.$this->name.' - END -->'."\n";

    return $html;
  } // function getJqueryValidationJS


  /**
   * Recupera los valores de todos los campos
   *
   * @return array
   **/
  public function getValuesArray(){
    $fieldsValuesArray = array();
    $fieldsNamesArray = $this->getFieldsNamesArray();
    foreach( $fieldsNamesArray as $fieldsName ){
      $fieldsValuesArray[ $fieldsName ] = $this->getFieldValue( $fieldsName );
    }

    return $fieldsValuesArray;
  }// fuction getValuesArray


  /**
   * Recupera los nombres de todos los campos
   *
   * @return TYPE
   **/
  public function getFieldsNamesArray(){
    $fieldsNamesArray = array();
    foreach( $this->fields as $key => $val ){
      array_push( $fieldsNamesArray, $key);
    }

    return $fieldsNamesArray;
  }


  /**
   * Recupera un parametro de un campo
   *
   * @param string $fieldName Nombre del campo
   * @param string $paramName Nombre del parametro
   * @return mixed
   **/
  public function getFieldParam( $fieldName, $paramName ) {
    $value = null;

    if( array_key_exists( $fieldName, $this->fields ) &&
      array_key_exists( $paramName, $this->fields[ $fieldName ] ) &&
      isset( $this->fields[ $fieldName ][$paramName] ) )
    {
      $value = $this->fields[ $fieldName ][ $paramName ];
    }

    return $value;
  }


  /**
   * Recupera el tipo de un campo
   *
   * @param string $fieldName Nombre del campo
   * @return string
   **/
  public function getFieldType( $fieldName ) {
    return $this->getFieldParam( $fieldName, 'type' );
  }


  /**
   * Recupera el valor de un campo
   *
   * @param string $fieldName Nombre del campo
   * @return mixed
   **/
  public function getFieldValue( $fieldName ) {
    return $this->getFieldParam( $fieldName, 'value' );
  }


  /**
   * Establece un parametro de un campo
   *
   * @param string $fieldName Nombre del campo
   * @param string $paramName Nombre del parametro
   * @param mixed $value Valor del parametro
   **/
  public function setFieldParam( $fieldName, $paramName, $value ) {
    if(array_key_exists($fieldName, $this->fields)){
      $this->fields[ $fieldName ][ $paramName ] = $value;
    }
    else {
      error_log( 'Intentando almacenar un parámetro ('.$paramName.') en un campo inexistente: '.$fieldName );
    }
  }


  /**
   * Establece el valor de un campo
   *
   * @param string $fieldName Nombre del campo
   * @param mixed $fieldValue Valor del campo
   **/
  public function setFieldValue( $fieldName, $fieldValue ){
    $this->setFieldParam( $fieldName, 'value', $fieldValue );
  }


  /**
   * Verifica si el valor de un campo es vacio
   *
   * @param string $fieldName Nombre del campo
   * @return boolean
   **/
  public function isEmptyFieldValue( $fieldName ) {
    $empty = true;

    $value = $this->getFieldValue( $fieldName );
    $type = $this->getFieldType( $fieldName );

    if( is_array( $value ) ) {
      $empty = ( sizeof( $value ) <= 0 );
    }
    else {
      $empty = ( $value === false || $value === '' );
    }

    return $empty;
  }


  /**
   * Procesa los ficheros temporales del form para colocarlos en su lugar definitivo y registrarlos
   *
   * @return boolean
   **/
  public function processFileFields() {
    $result = true;

    foreach( $this->getFieldsNamesArray() as $fieldName ){
      if( $this->getFieldType( $fieldName ) === 'file' && !$this->isEmptyFieldValue( $fieldName ) ) {
        error_log( 'FILE: Almacenando File Field: '.$fieldName );
        $fileStatus = $this->getFieldParam( $fieldName, 'fileStatus' );
        error_log( print_r( $fileStatus, true ) );
        $fileFieldValue = $this->getFieldValue( $fieldName );
        // $fileStatus['tmpFile'] = 'name'=>'', 'originalName'=>'', 'absLocation'=>'', 'type'=>'', 'size'=>''
        $fileName = $this->secureFileName( $fileStatus['tmpFile']['originalName'] );

        $destDir = $this->getFieldParam( $fieldName, 'destDir' );
        $fullDestPath = MOD_FORM_FILES_APP_PATH . $destDir;
        if( !is_dir( $fullDestPath ) ) {
          /**
          // TODO: CAMBIAR PERMISOS 0777
          **/
          if( !mkdir( $fullDestPath, 0777, true ) ) {
            $error = 'Imposible crear el dir. necesario: '.$fullDestPath; error_log($error);
          }
        }

        error_log( 'FILE: movendo ' . $fileStatus['tmpFile']['absLocation'] . ' a ' . $fullDestPath.'/'.$fileName );

        /**
        // TODO: DETECTAR Y SOLUCIONAR COLISIONES!!!
        */

        rename( $fileStatus['tmpFile']['absLocation'], $fullDestPath.'/'.$fileName );

        /**
        // TODO: FALTA GUARDA LOS DATOS DEFINITIVOS DEL FICHERO!!!
        En caso de fallo $result = false;
        **/
      }
    }

    return $result;
  }


  /**
   * Mover con seguridad un fichero del tmp de PHP al tmp de nuestra aplicacion
   *
   * @param string $fileTmpLoc Fichero temporal de PHP
   * @param string $fileName Nombre del fichero
   * @return string Fichero temporal de la App. En caso de error: false
   **/
  public function tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName ) {
    error_log( 'tmpPhpFile2tmpFormFile: '.$fileTmpLoc.' --- '.$fileName);
    $result = false;
    $error = false;

    $tmpCgmlFormPath = MOD_FORM_FILES_TMP_PATH .'/'. preg_replace( '/[^0-9a-z_\.-]/i', '_', $this->getIntFrmId() );
    if( !is_dir( $tmpCgmlFormPath ) ) {
      /**
      // TODO: CAMBIAR PERMISOS 0777
      **/
      if( !mkdir( $tmpCgmlFormPath, 0777, true ) ) {
        $error = 'Imposible crear el dir. necesario: '.$tmpCgmlFormPath; error_log($error);
      }
    }

    if( !$error ) {
      $secureName = $this->secureFileName( $fileName );

      $tmpLocationCgml = $tmpCgmlFormPath .'/'. $secureName;
      /**
      // TODO: FALTA VER QUE NON SE PISE UN ANTERIOR!!!
      **/

      if( !move_uploaded_file( $fileTmpLoc, $tmpLocationCgml ) ) {
        $error = 'Fallo de move_uploaded_file pasando ('.$fileTmpLoc.') a ('.$tmpLocationCgml.')'; error_log($error);
      }
      else {
        $result = $tmpLocationCgml;
      }
    }

    error_log( 'tmpPhpFile2tmpFormFile ERROR: '.$error );
    error_log( 'tmpPhpFile2tmpFormFile RET: '.$result );
    return $result;
  }


  /**
   * Crea un nombre de fichero seguro a partir del nombre de fichero deseado
   *
   * @param string $fileName Nombre del campo
   * @return string
   **/
  public function secureFileName( $fileName ) {
    error_log( 'secureFileName: '.$fileName );
    $maxLength = 200;

    $fileName = str_replace( $this->replaceAcents[ 'from' ], $this->replaceAcents[ 'to' ], $fileName );
    $fileName = preg_replace( '/[^0-9a-z_\.-]/i', '_', $fileName );

    $sobran = mb_strlen( $fileName, 'UTF-8' ) - $maxLength;
    if( $sobran < 0 ) {
      $sobran = 0;
    }

    $tmpExtPos = strrpos( $fileName, '.' );
    if( $tmpExtPos > 0 && ( $tmpExtPos - $sobran ) >= 8 ) {
      // Si hay extensión y al cortar el nombre quedan 8 o más letras, recorto solo el nombre
      $tmpName = substr( $fileName, 0, $tmpExtPos - $sobran );
      $tmpExt = substr( $fileName, 1 + $tmpExtPos );
      $fileName = $tmpName . '.' . $tmpExt;
    }
    else {
      // Recote por el final
      $fileName = substr( $fileName, 0, $maxLength );
    }

    error_log( 'secureFileName RET: '.$fileName );
    return $fileName;
  }





/**
  ***********************************************************
  VALIDATION
  ***********************************************************
**/


  /**
   * Regista el objeto que contiene los validadores
   *
   * @param object $validationObj
   **/
  public function setValidationObj( $validationObj ) {
    $this->validationObj = $validationObj;
  }


  /**
   * Verifica si se ha registrado el objeto con los validadores
   *
   * @return boolean
   **/
  public function issetValidationObj() {
    return( $this->validationObj !== null );
  }


  /**
   * Verifica si el campo es obligatorio
   *
   * @param string $fieldName Nombre del campo
   * @return boolean
   **/
  public function isRequiredField( $fieldName ) {
    return isset( $this->rules[ $fieldName ][ 'required' ] );
  }


  /**
   * Verifica si el valor de un campo cumple una regla segun los parametros establecidos
   *
   * @param string $fieldName Nombre del campo
   * @param string $fieldValue Valor del campo
   * @param string $ruleName Nombre de la regla
   * @param mixed $ruleParams Parametros de la regla (opcional)
   * @return boolean
   **/
  public function evaluateRule( $fieldName, $fieldValue, $ruleName, $ruleParams ) {
    $validate = false;

    if( $this->issetValidationObj() ) {
      $validate = $this->validationObj->evaluateRule( $fieldName, $fieldValue, $ruleName, $ruleParams );
    }
    else {
      error_log( 'FALTA CARGAR LOS VALIDADORES' );
    }

    return $validate;
  }


  /**
   * Verifica que se cumplen todas las reglas establecidas
   *
   * @return boolean
   **/
  public function validateForm() {
    error_log( 'validateForm:' );

    $formValidated = true;

    // Tienen que existir los validadores y los valores del form
    if( $this->issetValidationObj() ) {
      foreach( $this->rules as $fieldName => $fieldRules ) {
        $fieldValidated = $this->validateField( $fieldName );
        $formValidated = $formValidated && $fieldValidated;
      } // foreach( $this->rules as $fieldName => $fieldRules )

    } // if( $this->issetValidationObj() )
    else {
      $formValidated = false;
      error_log( 'FALTA CARGAR LOS VALIDADORES' );
    }

    return $formValidated;
  } // function validateForm()


  /**
   * Verifica que se cumplen las reglas establecidas para un campo
   *
   * @param string $fieldName Nombre del campo
   * @return boolean
   **/
  public function validateField( $fieldName ) {
    error_log( 'validateField: '.$fieldName );
    $fieldValidated = true;

    if( $this->isEmptyFieldValue( $fieldName ) ) {
      if( $this->isRequiredField( $fieldName ) ) {
        error_log( 'evaluateRule: VACIO e required = fallo' );
        $this->addFieldRuleError( $fieldName, 'required' );
        //$this->fieldErrors[ $fieldName ][ 'required' ] = false;
        $fieldValidated = false;
      }
      else {
        error_log( 'evaluateRule: VACIO e non required = ok' );
        $fieldValidated = true;
      }
    } // if( $this->isEmptyFieldValue( $fieldName ) )
    else {

      $fieldRules = $this->rules[ $fieldName ];
      $fieldType = $this->getFieldType( $fieldName );
      $fieldValues = $this->getFieldValue( $fieldName );

      // Hay que tener cuidado con ciertos fieldValues con estructura de array pero que son un único elemento
      if( !is_array( $fieldValues ) || ( $fieldType === 'file' && isset( $fieldValues[ 'name' ] ) ) ) {
        $fieldValues = array( $fieldValues );
      }

      foreach( $fieldValues as $value ) {
        $fieldValidateValue = false;

        error_log( 'validando '.$fieldName.' = '.print_r( $value, true ) );

        error_log( 'evaluateRule: non VACIO - Evaluar contido coas reglas...' );
        $fieldValidateValue = true;
        foreach( $fieldRules as $ruleName => $ruleParams ) {
          error_log( 'evaluateRule( '.$fieldName.', '.print_r( $value, true ).', '.$ruleName.', '.print_r( $ruleParams, true ) .' )' );

          if( $ruleName === 'equalTo' ) {
            $fieldRuleValidate = ( $value === $this->getFieldValue( str_replace('#', '', $ruleParams )) );
          }
          else {
            $fieldRuleValidate = $this->evaluateRule( $fieldName, $value, $ruleName, $ruleParams );
          }
          error_log( 'evaluateRule RET: '.print_r( $fieldRuleValidate, true ) );

          if( !$fieldRuleValidate ) {
            $this->addFieldRuleError( $fieldName, $ruleName );
            //$this->fieldErrors[ $fieldName ][ $ruleName ] = $fieldRuleValidate;
          }

          $fieldValidateValue = $fieldValidateValue && $fieldRuleValidate;
        } // foreach( $fieldRules as $ruleName => $ruleParams )

        $fieldValidated = $fieldValidated && $fieldValidateValue;
      } // foreach( $fieldValues as $value )

    } // else if( $this->isEmptyFieldValue( $fieldName ) )

    return( $fieldValidated );
  }


  /**
   * Añade un mensaje de error al formulario
   *
   * @param string $paramName
   * @param string $paramName
   * @return TYPE
   **/
  public function addFormError( $msgText, $msgClass = false ) {
    $this->formErrors[] = array( 'msgText' => $msgText, 'msgClass' => $msgClass );
  }

  /**
   * Añade un mensaje de error a un campo del formulario
   *
   * @param string $fieldName Nombre del campo
   * @param TYPE $paramName
   * @return TYPE
   **/
  public function addFieldRuleError( $fieldName, $ruleName, $msgRuleError = false ) {
    error_log( "addFieldRuleError: $fieldName, $ruleName, $msgRuleError " );
    $this->fieldErrors[ $fieldName ][ $ruleName ] = $msgRuleError;
  }


  /**
   * Recupera todos los errores que se han añadido
   *
   * @return array
   **/
  public function getJVErrors() {
    $errors = array();

    foreach( $this->fieldErrors as $fieldName => $fieldRules ) {
      foreach( $fieldRules as $ruleName => $msgRuleError ) {
        $ruleParams = isset( $this->rules[ $fieldName ][ $ruleName ] ) ? $this->rules[ $fieldName ][ $ruleName ] : false;
        $errors[] = array( 'fieldName' => $fieldName, 'ruleName' => $ruleName, 'ruleParams' => $ruleParams, 'JVshowErrors' => array( $fieldName => $msgRuleError ) );
      }
    }

    foreach( $this->formErrors as $formError ) {
      // Errores globales (no referidos a un field determinado)
      $errors[] = array( 'fieldName' => false, 'JVshowErrors' => $formError );
    }

    return $errors;
  }


  /**
   * Recupera un JSON de OK con los sucesos que hay que lanzar en el navegador
   *
   * @return string JSON
   **/
  public function jsonFormOk( $moreInfo = false ) {
    $result = array(
      'result' => 'ok',
      'success' => $this->getSuccess()
    );
    if( $moreInfo !== false ) {
      $result['moreInfo'] = $moreInfo;
    }

    return json_encode( $result );
  }


  /**
   * Recupera un JSON de ERROR con los errores que hay que mostrar en el navegador
   *
   * @return string JSON
   **/
  public function jsonFormError( $moreInfo = false ) {
    $result = array(
      'result' => 'error',
      'jvErrors' => $this->getJVErrors()
    );
    if( $moreInfo !== false ) {
      $result['moreInfo'] = $moreInfo;
    }

    return json_encode( $result );
  }


  /**
   * Verifica si se han añadido errores
   *
   * @return boolean
   **/
  public function existErrors() {
    return( sizeof( $this->fieldErrors ) > 0 || sizeof( $this->formErrors ) > 0 );
  }


  /**
   * Verifica si se han añadido errores a un campo
   *
   * @param string $fieldName Nombre del campo
   * @return boolean
   **/
  public function existFieldErrors( $fieldName ) {
    return( isset( $this->fieldErrors[ $fieldName ] ) || sizeof( $this->formErrors[ $fieldName ] ) > 0 );
  }


/**


class FormController implements Serializable {
  function __construct( $name = false, $action = false, $formPost = false ) {

  private $action = false;
  private $cgIntFrmId = false;
  private $enctype = 'multipart/form-data';
  private $fieldErrors = array();
  private $fields = array();
  private $formErrors = array();
  private $id = false;
  private $messages = array();
  private $method = 'post';
  private $name = false;
  private $postValues = false;
  private $replaceAcents = array(
  private $rules = array();
  private $success = false;
  private $validationObj = null;

  private function phpIni2Bytes( $size ) {
  public function addFieldRuleError( $fieldName, $ruleName, $msgRuleError = false ) {
  public function addFormError( $msgText, $msgClass = false ) {
  public function evaluateRule( $ruleName, $value, $fieldName, $ruleParams ) {
  public function existErrors() {
  public function existFieldErrors( $fieldName ) {
  public function getFieldParam( $fieldName, $paramName ) {
  public function getFieldsNamesArray(){
  public function getFieldType( $fieldName ) {
  public function getFieldValue( $fieldName ) {
  public function getHtmlClose() {
  public function getHtmlField( $fieldName ) {
  public function getHtmlFieldArray( $fieldName ) {
  public function getHtmlFields() {
  public function getHtmlFieldsArray() {
  public function getHtmlForm() {
  public function getHtmpOpen() {
  public function getIntFrmId() {
  public function getJqueryValidationJS() {
  public function getJVErrors() {
  public function getSuccess() {
  public function getValuesArray(){
  public function isEmptyFieldValue( $fieldName ) {
  public function isRequiredField( $fieldName ) {
  public function issetValidationObj() {
  public function jsonFormError() {
  public function jsonFormOk() {
  public function loadFromSession( $cgIntFrmId ) {
  public function loadPostValues( $formPost ) {
  public function loadVOValues( $dataVO ) {
  public function saveToSession() {
  public function secureFileName( $fileName ) {
  public function serialize() {
  public function setField( $fieldName, $params = false ) {
  public function setFieldParam( $fieldName, $paramName, $value ) {
  public function setFieldValue( $fieldName, $fieldValue ){
  public function setSuccess( $success ) {
  public function setValidationMsg( $fieldName, $msg ) {
  public function setValidationObj( $validationObj ) {
  public function setValidationRule( $fieldName, $ruleName, $ruleParams = true ) {
  public function tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName ) {
  public function unserialize( $dataSerialized ) {
  public function validateField( $fieldName ) {
  public function validateForm() {

**/


} // END FormController class
