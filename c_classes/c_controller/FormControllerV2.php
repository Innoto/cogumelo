<?php


class FormControllerV2 implements Serializable {

  private $name = false;
  private $id = false;
  private $cgIntFrmId = false;
  private $action = false;
  private $method = 'post';
  private $enctype = 'multipart/form-data';
  private $fields = array();
  //private $validation = array( 'rules' => array(), 'messages' => array() );
  private $rules = array();
  private $messages = array();

  // POST submit
  private $postValues = false;
  //private $evaluateRuleMethod = false;
  private $validationObj = null;
  private $rulesErrors = array();
  private $formErrors = array();



  function __construct( $name = false, $action = false, $cgIntFrmId = false, $formPost = false ) {
    if( $cgIntFrmId ) {
      $this->loadFromSession( $cgIntFrmId );
      if( $formPost ) {
        $this->loadPostValues( $formPost );
      }
    }
    else {
      $this->cgIntFrmId = crypt( uniqid().'---'.session_id(), 'cf' );
      $this->name = $name;
      $this->id = $name;
      if( $action ) {
        $this->action = $action;
      }
      $this->setField( 'cgIntFrmId', array( 'type' => 'text', 'value' => $this->cgIntFrmId ) );
    }
  }

  public function serialize() {
    $data = array();

    $data[] = $this->name;
    $data[] = $this->id;
    $data[] = $this->cgIntFrmId;
    $data[] = $this->action;
    $data[] = $this->method;
    $data[] = $this->enctype;
    $data[] = $this->fields;
    $data[] = $this->rules;
    $data[] = $this->messages;
    $data[] = $this->postValues;

    return serialize( $data );
  }

  public function unserialize( $dataSerialized ) {
    $data = unserialize( $dataSerialized );

    $this->name = array_shift( $data );
    $this->id = array_shift( $data );
    $this->cgIntFrmId = array_shift( $data );
    $this->action = array_shift( $data );
    $this->method = array_shift( $data );
    $this->enctype = array_shift( $data );
    $this->fields = array_shift( $data );
    $this->rules = array_shift( $data );
    $this->messages = array_shift( $data );
    $this->postValues = array_shift( $data );
  }

  public function saveToSession() {
    $formSessionId = 'CGFSI_'.$this->getIntFrmId();
    $_SESSION[ $formSessionId ] = $this->serialize();
    //error_log( $_SESSION[ $formSessionId ] );

    return $formSessionId;
  }

  public function loadFromSession( $cgIntFrmId ) {
    $formSessionId = 'CGFSI_'.$cgIntFrmId;
    $this->unserialize( $_SESSION[ $formSessionId ] );
    //error_log( $_SESSION[ $formSessionId ] );
  }

  public function setField( $fieldName, $params = false ) {
    /* Vamos a dejar de crear "id" de forma automatica
    if( !isset( $this->fields[$fieldName]['id'] ) ||
      ( isset( $this->fields[$fieldName]['name'] ) && $this->fields[$fieldName]['name'] === $this->fields[$fieldName]['id'] ) )
    {
      $this->fields[$fieldName]['id'] = $fieldName;
    }
    */
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

  private function phpIni2Bytes( $size ) {
    if( preg_match( '/([\d\.]+)([KMG])/i', $size, $match ) ) {
      $pos = array_search( $match[2], array("K", "M", "G") );
      if( $pos !== false ) {
        $size = $match[1] * pow(1024, $pos + 1);
      }
    }
    return $size;
  }

  public function setValidationRule( $fieldName, $ruleName, $ruleParams = true ) {
    $this->rules[$fieldName][$ruleName] = $ruleParams;
  }

  public function setValidationMsg( $fieldName, $msg ) {
    $this->messages[$fieldName] = $msg;
  }


  public function loadPostValues( $formPost ) {
    $this->postValues = $formPost;
    foreach($formPost as $key => $val ){
      if(array_key_exists( $key, $this->fields )){
        $this->fields[$key]['value'] = $val;
      }
    }
  }

  public function getIntFrmId() {
    return $this->cgIntFrmId;
  }

  public function getHtmlForm() {
    $html='';

    $html .= $this->getHtmpOpen()."\n";
    $html .= $this->getHtmlFields()."\n";
    $html .= $this->getHtmlClose()."\n";

    $html .= $this->getJqueryValidationJS()."\n";

    return $html;
  }

  public function getHtmpOpen() {
    $html='';

    $html .= '<form name="'.$this->name.'" id="'.$this->id.'" sg="'.$this->cgIntFrmId.'"';
    if( $this->action ) {
      $html .= ' action="'.$this->action.'"';
    }
    $html .= ' method="'.$this->method.'">';

    return $html;
  }

  public function getHtmlFields() {
    $html = '';
    foreach( $this->fields as $fieldName => $fieldParams ) {
      $html .= '<div class="ffn-'.$fieldName.'">'.$this->getHtmlField($fieldName)."</div>\n";
    }
    return $html;
  }

  public function getHtmlFieldsArray() {
    $html = array();
    foreach( $this->fields as $fieldName => $fieldParams ) {
      $html[] = '<div class="ffn-'.$fieldName.'">'.$this->getHtmlField($fieldName)."</div>\n";
    }
    return $html;
  }


  public function getHtmlField( $fieldName ) {
    $html = '';

    $htmlFieldArray = $this->getHtmlFieldArray( $fieldName );

    if( isset( $htmlFieldArray['label'] ) ) {
      $html .= $htmlFieldArray['label']."<br>\n";
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
        $html .= '<span class="JQVMC-'.$fieldName.'-error"></span>';
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


  public function getHtmlClose() {
    $html = '</form><!-- '.$this->name.' -->';
    return $html;
  }


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


  public function getValuesArray(){
    $fieldsValuesArray = array();
    $fieldsNamesArray = $this->getFieldsNamesArray();
    foreach( $fieldsNamesArray as $fieldsName ){
      $fieldsValuesArray[ $fieldsName ] = $this->getFieldValue( $fieldsName );
    }

    return $fieldsValuesArray;
  }// fuction getValuesArray


  public function getFieldsNamesArray(){
    $fieldsNamesArray = array();
    foreach( $this->fields as $key => $val ){
       array_push( $fieldsNamesArray, $key);
    }

    return $fieldsNamesArray;
  }

  public function setValuesVO( $dataVO ){
    if(gettype($dataVO) == "object"){
      foreach( $dataVO->getKeys() as $keyVO){
        $this->setFieldValue( $keyVO, $dataVO->getter($keyVO));
      }
    }
  }



/**
  ***********************************************************
  VALIDATION
  ***********************************************************
**/

  /*
  public function setEvaluateRuleMethod( $evaluateRuleMethod ) {
    error_log( 'setEvaluateRuleMethod' );
    error_log( print_r( $evaluateRuleMethod, true ) );
    $this->evaluateRuleMethod = $evaluateRuleMethod;
  }
  */

  public function setValidationObj( $validationObj ) {
    $this->validationObj = $validationObj;
  }

  public function issetValidationObj() {
    return( $this->validationObj !== null );
  }

  public function getFieldType( $fieldName ) {
    $value = $this->fields[ $fieldName ][ 'type' ];
    return $value;
  }

  public function getFieldValue( $fieldName ) {
    $value = null;
    if(array_key_exists( $fieldName, $this->fields )){
      $value = isset( $this->fields[ $fieldName ]['value'] ) ? $this->fields[ $fieldName ]['value'] : false;
    }

    return $value;
  }

  public function setFieldValue( $fieldName, $fieldValue ){
    if(array_key_exists($fieldName, $this->fields)){
      $this->fields[ $fieldName ]['value'] = $fieldValue;
    }
  }

  public function isRequiredField( $fieldName ) {
    return isset( $this->rules[ $fieldName ][ 'required' ] );
  }

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

  public function evaluateRule( $ruleName, $value, $fieldName, $ruleParams ) {
    return $this->validationObj->evaluateRule( $ruleName, $value, $fieldName, $ruleParams );
  }



  /**
   * Metodo de validacion global segun las reglas cargadas
   *
   * @param mixed $param (optinal)
   * @return bool $validate
   **/
  public function validateForm() {
    error_log( 'validateForm:' );

    $formValidated = true;

    // Tienen que existir los validadores y los valores del form
    if( $this->issetValidationObj() ) {
      foreach( $this->rules as $fieldName => $fieldRules ) {
        $fieldValidated = true;
        $fieldType = $this->getFieldType( $fieldName );

        $fieldValues = $this->getFieldValue( $fieldName );
        if( !is_array( $fieldValues ) ) {
          $fieldValues = array( $fieldValues );
        }

        foreach( $fieldValues as $value ) {
          $fieldValidateValue = false;

          //$value = $this->getFieldValue( $fieldName );
          error_log( 'validando '.$fieldName.' = '.print_r( $value, true ) );

          if( $this->isEmptyFieldValue( $fieldName ) ) {
            if( $this->isRequiredField( $fieldName ) ) {
              error_log( 'evaluateRule: VACIO e required = fallo' );
              $this->rulesErrors[ $fieldName ][ 'required' ] = false;
              $fieldValidateValue = false;
            }
            else {
              error_log( 'evaluateRule: VACIO e non required = ok' );
              $fieldValidateValue = true;
            }
          } // if( $this->isEmptyFieldValue( $fieldName ) )
          else {
            error_log( 'evaluateRule: non VACIO - Evaluar contido coas reglas...' );
            $fieldValidateValue = true;
            foreach( $fieldRules as $ruleName => $ruleParams ) {
              error_log( 'evaluateRule( '.$ruleName.', '.$value.', '.$fieldName.', '. print_r( $ruleParams, true ) .' )' );

              if( $ruleName === 'equalTo' ) {
                $fieldRuleValidate = ( $value === $this->getFieldValue( $ruleParams ) );
              }
              else {
                $fieldRuleValidate = $this->evaluateRule( $ruleName, $value, $fieldName, $ruleParams );
              }
              error_log( 'evaluateRule RET: '.print_r( $fieldRuleValidate, true ) );

              if( !$fieldRuleValidate ) {
                $this->rulesErrors[ $fieldName ][ $ruleName ] = $fieldRuleValidate;
              }

              $fieldValidateValue = $fieldValidateValue && $fieldRuleValidate;
            } // foreach( $fieldRules as $ruleName => $ruleParams )

          } // else if( $this->isEmptyFieldValue( $fieldName ) )

          $fieldValidated = $fieldValidated && $fieldValidateValue;
        } // foreach( $fieldValues as $value )

        $formValidated = $formValidated && $fieldValidated;
      } // foreach( $this->rules as $fieldName => $fieldRules )

    } // if( $this->issetValidationObj() )
    else {
      $formValidated = false;
      error_log( 'FALTA CARGAR LOS VALIDADORES' );
    }

    return $formValidated;
  } // function validateForm( $formPost = false )


  public function addJVError( $msgClass, $msgText ) {
    $this->formErrors[] = array( 'msgClass' => $msgClass, 'msgText' => $msgText );
  }


  public function getJVErrors() {
    $errors = array();

    foreach( $this->rules as $fieldName => $fieldRules ) {
      foreach( $fieldRules as $ruleName => $ruleParams ) {
        $msgRule = '';
        if( isset( $this->rulesErrors[ $fieldName ][ $ruleName ] ) &&
          $this->rulesErrors[ $fieldName ][ $ruleName ] === false )
        {
          $errors[] = array( 'fieldName' => $fieldName, 'msgRule' => $ruleName, 'ruleParams' => $ruleParams, 'JVshowErrors' => array( $fieldName => $msgRule ) );
        }
      }
    }

    foreach( $this->formErrors as $formError ) {
      $errors[] = array( 'fieldName' => false, 'JVshowErrors' => $formError );
    }

    return $errors;
  }

} // class FormControllerV2 implements Serializable {
