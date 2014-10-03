<?php


class FormController implements Serializable {

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
  } // function setField

  public function setValidationRule( $fieldName, $ruleName, $ruleParams = true ) {
    $this->rules[$fieldName][$ruleName] = $ruleParams;
  }

  public function setValidationMsg( $fieldName, $msg ) {
    $this->messages[$fieldName] = $msg;
  }


  public function loadPostValues( $formPost ) {
    $this->postValues = $formPost;
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
      $html .= '<div>'.$this->getHtmlField($fieldName)."</div>\n";
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
      
      case 'reserved':
      
        break;
      //case 'file':
      //  break;

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
    return $this->postValues;
  }// fuction getValuesArray


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

  public function getFieldType( $fieldName ) {
    $value = $this->fields[ $fieldName ][ 'type' ];
    return $value;
  }

  public function getFieldValue( $fieldName ) {
    $value = isset( $this->postValues[ $fieldName ] ) ? $this->postValues[ $fieldName ] : null;
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


  /**
   * Metodo de validacion global segun las reglas cargadas
   *
   * @param mixed $param (optinal)
   * @return bool $validate
   **/
  public function validateForm( $formPost = false ) {
    error_log( 'validateForm:' );

    $validate = true;

    if( $formPost ) {
      $this->loadPostValues( $formPost );
    }

    // Tienen que existir los validadores y los valores del form
    if( is_object( $this->validationObj ) && $this->postValues!==false ) {
      foreach( $this->rules as $fieldName => $fieldRules ) {
        $fieldValidate = false;
        $value = $this->getFieldValue( $fieldName );
        error_log( 'validando '.$fieldName.' = '.print_r( $value, true ) );
        if( $value === '' && !$this->isRequiredField( $fieldName ) ) {
          $fieldValidate = true;
        }
        else {
          $fieldValidate = true;
          foreach( $fieldRules as $ruleName => $ruleParams ) {
            error_log( 'evaluateRule( '.$ruleName.', '.print_r( $value, true ).', '.$fieldName.', '.$ruleParams.' )' );

            $fieldRuleValidate = $this->validationObj->evaluateRule( $ruleName, $value, $fieldName, $ruleParams );
            if( $ruleName === 'equalTo' ) {
              $fieldRuleValidate = ( $value === $this->getFieldValue( $ruleParams ) );
             }
            error_log( print_r( $fieldRuleValidate, true ) );

            $this->rulesErrors[ $fieldName ][ $ruleName ] = $fieldRuleValidate;
            if( !$fieldRuleValidate ) {
              $fieldValidate = false;
              break;
            }
          }
        }

        if( !$fieldValidate ) {
          $validate = false;
        }

      } // foreach( $this->rules as $fieldName => $fieldRules )
    } // if( is_object( $this->validationObj ) && $this->postValues!==false )
    else {
      $validate = false;
      error_log( 'FALTA CARGAR EL POST DEL FORM O LOS VALIDADORES' );
    }

    return $validate;
  } // function validateForm( $formPost = false )



  /*
  public function validateForm( $formPost = false ) {
    error_log( 'validateForm:' );

    $validate = true;

    if( $formPost ) {
      $this->loadPostValues( $formPost );
    }

    // Tienen que existir los validadores y los valores del form
    if( is_object( $this->validationObj ) && $this->postValues!==false ) {
      foreach( $this->rules as $fieldName => $fieldRules ) {
        $fieldValidate = false;
        $fieldType = $this->getFieldType( $fieldName );

        $values = $this->getFieldValue( $fieldName );
        if( !is_array( $values ) ) {
          $values = array( $values );
        }

        foreach( $values as $value ) {

          //$value = $this->getFieldValue( $fieldName );
          error_log( 'validando '.$fieldName.' = '.print_r( $value, true ) );

          if( !$this->isRequiredField( $fieldName ) && ( $value === false || $value === '' ) ) {
            $fieldValidate = true;
          }

          if( $this->isRequiredField( $fieldName ) && $value !== false ) {
            $fieldValidate = true;
            foreach( $fieldRules as $ruleName => $ruleParams ) {
              error_log( 'evaluateRule( '.$ruleName.', '.print_r( $value, true ).', '.$fieldName.', '.$ruleParams.' )' );

              if( $ruleName === 'equalTo' ) {
                $fieldRuleValidate = ( $value === $this->getFieldValue( $ruleParams ) );
              }
              else {
                $fieldRuleValidate = $this->validationObj->evaluateRule( $ruleName, $value, $fieldName, $ruleParams );
              }
              error_log( print_r( $fieldRuleValidate, true ) );

              $this->rulesErrors[ $fieldName ][ $ruleName ] = $fieldRuleValidate;
              if( !$fieldRuleValidate ) {
                $fieldValidate = false;
                break;
              }
            }
          }

          if( !$fieldValidate ) {
            $validate = false;
          }

        } // foreach( $values as $value )

      } // foreach( $this->rules as $fieldName => $fieldRules )

    } // if( is_object( $this->validationObj ) && $this->postValues!==false )
    else {
      $validate = false;
      error_log( 'FALTA CARGAR EL POST DEL FORM O LOS VALIDADORES' );
    }

    return $validate;
  } // function validateForm( $formPost = false )
  */



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

    return $errors;
  }

} // class FormController implements Serializable {
