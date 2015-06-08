<?php

error_reporting( -1 );
/*

  CAMBIOS

  jsonFormError - getJsonFormError
  jsonFormOk - getJsonFormOk
  setValidationRule - setFieldRule
  getValuesArray - getFieldsValueArray
  getFieldsNamesArray - getFieldNamesArray
  getHtmlForm - getHtmlAllForm

  evaluateRule - validateRule
  addFieldRuleError: FACHADA como addFieldError=addFieldRuleError( $fieldName, 'cogumelo', $msgRuleError)

  isFieldDefined - isDefinedField
*/


/**
 * Gestión de formularios. Campos, Validaciones, Html, Ficheros, ...
 *
 * @package Module Form
 */
class FormController implements Serializable {

  /**
    Prefijo para marcar las clases CSS creadas automaticamente
  */
  const CSS_PRE = MOD_FORM_CSS_PRE;
  /**
    Ruta a partir de la que se crean los directorios y ficheros subidos
  */
  const FILES_APP_PATH = MOD_FORM_FILES_APP_PATH;
  /**
    Ruta a partir de la que se crean los directorios y ficheros temporales subidos
  */
  const FILES_TMP_PATH = MOD_FORM_FILES_TMP_PATH;

  private $name = false;
  private $id = false;
  private $tokenId = false;
  private $action = false;
  private $success = false;
  private $method = 'post';
  private $enctype = 'multipart/form-data';
  private $fields = array();
  private $rules = array();
  private $groups = array();
  private $messages = array();

  // POST submit
  private $postData = null;
  private $postValues = null;
  private $validationObj = null;
  private $fieldErrors = array();
  private $formErrors = array();

  private $htmlEditor = false;

  private $replaceAcents = array(
    'from' => array( 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ' ),
    'to'   => array( 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'A', 'a', 'O', 'o' )
  );



  /**
    Constructor. Crea el TokenId y, si se envian, establece Name y Action del formulario
    @param string $name Name del formulario
    @param string $action Action del formulario
  */
  public function __construct( $name = false, $action = false ) {
    $this->getTokenId( 'new'.$name.$action );
    if( $name !== false ) {
      $this->setName( $name );
    }
    if( $action !== false ) {
      $this->setAction( $action );
    }
  }


  /**
    Recupera el TokenId único del formulario. Si no existe, se crea. NO es el id del FORM.
    @return string
  */
  public function getTokenId( $saltText = '' ) {
    if( $this->tokenId === false ) {
      $tmp = 'cf-'.uniqid().'-'.session_id().'-'.$saltText.rand(0,999);
      $this->tokenId = sha1( $tmp );
      $this->setField( 'cgIntFrmId', array( 'type' => 'hidden', 'value' => $this->tokenId ) );
    }

    return $this->tokenId;
  }

  /**
    Establece el Name e Id del formulario
    @param string $action Action del formulario
    @return string
  */
  public function setName( $name = false ) {
    $this->name = $name;
    $this->id = $name;
  }

  /**
    Recupera el Name del formulario
    @return string
  */
  public function getName() {

    return $this->name;
  }

  /**
    Recupera el Id del formulario
    @return string
  */
  public function getId() {

    return $this->id;
  }

  /**
    Establece el Action del formulario
    @param string $action Action del formulario
    @return string
  */
  public function setAction( $action ) {
    $this->action = $action;
  }

  /**
    Recupera el Action del formulario
    @return string
  */
  public function getAction() {

    return $this->action;
  }

  /**
    Recupera todos los datos importantes en un array serializado
    @return string
  */
  public function serialize() {
    $data = array();

    $data[ 'name' ] = $this->name;
    $data[ 'id' ] = $this->id;
    $data[ 'tokenId' ] = $this->tokenId;
    $data[ 'action' ] = $this->action;
    $data[ 'success' ] = $this->success;
    $data[ 'method' ] = $this->method;
    $data[ 'enctype' ] = $this->enctype;
    $data[ 'fields' ] = $this->fields;
    $data[ 'rules' ] = $this->rules;
    $data[ 'groups' ] = $this->groups;
    $data[ 'messages' ] = $this->messages;
    // $data[] = $this->postValues;

    return serialize( $data );
  }

  /**
    Carga todos los datos importantes desde el string serializado
    @param string $dataSerialized Datos del form serializados
  */
  public function unserialize( $dataSerialized ) {
    $data = unserialize( $dataSerialized );

    $this->name = $data[ 'name' ];
    $this->id = $data[ 'id' ];
    $this->tokenId = $data[ 'tokenId' ];
    $this->action = $data[ 'action' ];
    $this->success = $data[ 'success' ];
    $this->method = $data[ 'method' ];
    $this->enctype = $data[ 'enctype' ];
    $this->fields = $data[ 'fields' ];
    $this->rules = $data[ 'rules' ];
    $this->groups = $data[ 'groups' ];
    $this->messages = $data[ 'messages' ];
    // $this->postValues = array_shift( $data );
  }

  /**
    Guarda todos los datos importantes (serializados) en sesion
  */
  public function saveToSession() {
    $formSessionId = 'CGFSI_'.$this->getTokenId();
    $_SESSION[ $formSessionId ] = $this->serialize();
    //return $formSessionId;
  }

  /**
    Actualiza los datos de un campo en sesion
  */
  public function updateFieldToSession( $fieldName ) {
    $result = false;

    $formSessionId = 'CGFSI_'.$this->getTokenId();

    if( isset( $_SESSION[ $formSessionId ] ) ) {
      $data = unserialize( $_SESSION[ $formSessionId ] );
      $data[ 'fields' ][ $fieldName ] = $this->fields[ $fieldName ];
      $_SESSION[ $formSessionId ] = serialize( $data );
      $result = true;
    }

    return $result;
  }

  /**
    Actualiza las reglas de validacion de un campo en sesion
  */
  public function updateFieldRulesToSession( $fieldName ) {
    $result = false;

    $formSessionId = 'CGFSI_'.$this->getTokenId();

    if( isset( $_SESSION[ $formSessionId ] ) ) {
      $data = unserialize( $_SESSION[ $formSessionId ] );
      if( isset( $this->rules[ $fieldName ] ) ) {
        $data[ 'rules' ][ $fieldName ] = $this->rules[ $fieldName ];
      }
      else {
        if( isset( $data[ 'rules' ][ $fieldName ] ) ) {
          unset( $data[ 'rules' ][ $fieldName ] );
        }
      }
      $_SESSION[ $formSessionId ] = serialize( $data );
      $result = true;
    }

    return $result;
  }

  /**
    Actualiza un grupo en sesion
  */
  public function updateGroupToSession( $groupName ) {
    $result = false;

    $formSessionId = 'CGFSI_'.$this->getTokenId();

    if( isset( $_SESSION[ $formSessionId ] ) ) {
      $data = unserialize( $_SESSION[ $formSessionId ] );
      $data[ 'groups' ][ $groupName ] = $this->groups[ $groupName ];
      $_SESSION[ $formSessionId ] = serialize( $data );
      $result = true;
    }

    return $result;
  }

  /**
     Recupera de sesion todos los datos importantes
   *
   * @param string $tokenId ID interno del formulario
   *
   * @return boolean
   */
  public function loadFromSession( $tokenId ) {
    $result = false;

    $formSessionId = 'CGFSI_'.$tokenId;
    if( isset( $_SESSION[ $formSessionId ] ) ) {
      $this->unserialize( $_SESSION[ $formSessionId ] );
      $result = ( $this->tokenId === $tokenId );
    }
    else {
      error_log( 'ERROR. El FORM no esta en sesion: '.$formSessionId );
      error_log( print_r( $_SESSION, true ) );
    }

    return $result;
  }

  /**
    Captura los datos enviados por el navegador, recupera parametros del form y le carga los input
    @return boolean
  */
  public function loadPostInput() {
    $result = false;

    $postDataJson = file_get_contents( 'php://input' );
    // error_log( $postDataJson );
    if( $postDataJson !== false && strpos( $postDataJson, '{' )===0 ) {
      $postData = json_decode( $postDataJson, true );
      // error_log( print_r( $postData, true ) );

      // recuperamos FORM de sesion y añadimos los datos enviados
      if( $this->loadPostSession( $postData ) ) {
        $this->loadPostValues( $postData );
        $result = true;
      }
    }

    return $result;
  }

  /**
    Recupera de sesion todos los datos importantes (serializados)
    @param array $formPost Datos enviados por el navegador convertidos a array
    @return boolean
  */
  public function loadPostSession( $formPost ) {
    $result = false;
    if( $formPost !== false && isset( $formPost[ 'cgIntFrmId' ] ) ) {
      $result = $this->loadFromSession( $formPost[ 'cgIntFrmId' ] );
    }

    return $result;
  }

  /**
    Carga los valores de los input del navegador
    @param array $formPost Datos enviados por el navegador convertidos a array
  */
  public function loadPostValues( $formPost ) {
    // $this->postValues = $formPost;

    // Importando los datos del form e integrando los datos de ficheros subidos
    foreach( $this->getFieldsNamesArray() as $fieldName ) {

      if( $this->getFieldType( $fieldName ) !== 'file' ) {
        if( isset( $formPost[ $fieldName ] ) ) {
          $this->setFieldValue( $fieldName, $formPost[ $fieldName ] );
        }
      }
      else {
        if( !$this->isEmptyFieldValue( $fieldName ) ) {
          $fileFieldValue = $this->getFieldValue( $fieldName );
          switch( $fileFieldValue['status'] ) {
            case 'LOAD':
              $fileFieldValue['validate'] = $fileFieldValue['temp'];
              // error_log( 'loadPostValues: LOAD -> temp' );
              // error_log( print_r( $fileFieldValue, true ) );
              break;
            case 'REPLACE':
              $fileFieldValue['validate'] = $fileFieldValue['temp'];
              // error_log( 'loadPostValues: REPLACE -> temp' );
              // error_log( print_r( $fileFieldValue, true ) );
              break;
            case 'EXIST':
              $fileFieldValue['validate'] = $fileFieldValue['prev'];
              // error_log( 'loadPostValues: EXIST -> prev' );
              // error_log( print_r( $fileFieldValue, true ) );
              break;
          }
          $this->setFieldValue( $fieldName, $fileFieldValue );
        }
      }

    }
  }

  /**
    Carga los valores del VO
    @param VO $dataVO Datos cargados por el programa
  */
  public function loadVOValues( $dataVO ) {
    if( gettype( $dataVO ) == 'object' ) {
      $dataArray = array();
      foreach( $dataVO->getKeys() as $keyVO ) {
        $dataArray[ $keyVO ] = $dataVO->getter( $keyVO );
      }
      $this->loadArrayValues( $dataArray );
    }
  }

  /**
    Carga los valores del VO
    @param VO $dataVO Datos cargados por el programa
  */
  public function loadArrayValues( $dataArray ) {
    // error_log( 'loadArrayValues: ' . print_r( $dataArray, true ) );

    foreach( $dataArray as $fieldName => $value ) {
      if( $this->isFieldDefined( $fieldName ) ) {

        if( $this->getFieldType( $fieldName ) !== 'file' ) {
          $this->setFieldValue( $fieldName, $value );
        }
        else {
          error_log( 'FILE value: ' . print_r( $value, true ) );

          if ( isset( $value ) && is_array( $value ) ) {
            $fileFieldValue = array (
              'status' => 'EXIST',
              'prev' => $value
            );
            $this->setFieldValue( $fieldName, $fileFieldValue );
            $this->setFieldParam( $fieldName, 'data-filemodel-id', $value['id'] );
          }
          else {
            $this->setFieldValue( $fieldName, null );
          }
        }
      }
    }
  }

  /**
    Define un campo del formulario y, opcionalmente, con sus parametros
    @param string $fieldName Nombre del campo
    @param array $params Opcional. Parametros: id, type, label, title, options, placeholder, size,
      cols, rows, multiple, readonly, ...
  */
  public function setField( $fieldName, $params = false ) {
    $this->fields[ $fieldName ][ 'name' ] = $fieldName;
    $this->fields[ $fieldName ][ 'cgIntFrmFieldInfo' ] = array();
    if( $params ) {
      foreach( $params as $key => $value ) {
        $this->fields[ $fieldName ][ $key ] = $value;
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
    Establece el valor de un campo
    @param string $fieldName Nombre del campo
    @param mixed $fieldValue Valor del campo
  */
  public function setFieldValue( $fieldName, $fieldValue ) {
    $this->setFieldParam( $fieldName, 'value', $fieldValue );
  }















  /**
    Establece un parametro de un campo
    @param string $fieldName Nombre del campo
    @param string $paramName Nombre del parametro
    @param mixed $value Valor del parametro
  */
  public function setFieldParam( $fieldName, $paramName, $value ) {
    // error_log( 'setFieldParam: ' . $paramName . ': ' . print_r( $value, true ) );

    if( array_key_exists( $fieldName, $this->fields) ) {
      $this->fields[ $fieldName ][ $paramName ] = $value;
    }
    else {
      error_log( 'Intentando almacenar un parámetro ('.$paramName.') en un campo inexistente: '.$fieldName );
    }
  }













  /**
    Establece un parametro interno en un campo
    @param string $fieldName Nombre del campo
    @param string $paramName Nombre del parametro
    @param mixed $value Valor del parametro
  */
  public function setFieldInternal( $fieldName, $paramName, $value ) {
    if( array_key_exists( $fieldName, $this->fields ) ) {
      $this->fields[ $fieldName ][ 'cgIntFrmFieldInfo' ][ $paramName ] = $value;
    }
    else {
      error_log( 'Intentando almacenar un parámetro ('.$paramName.') en un campo inexistente: '.$fieldName );
    }
  }

  /**
    Clona un campo del formulario, sus parametros y reglas.
    @param string $fieldName Nombre del campo origen
    @param string $newFieldName Nombre del campo clonado
  */
  public function cloneField( $fieldName, $newFieldName, $newFieldId = false ) {
    $result = false;

    if( $this->isFieldDefined( $fieldName ) && !$this->isFieldDefined( $newFieldName ) ) {

      $this->fields[ $newFieldName ] = $this->fields[ $fieldName ];

      $this->setFieldInternal( $newFieldName, 'cloneOf', $fieldName );
      $this->setFieldParam( $newFieldName, 'name', $newFieldName );

      if( $newFieldId !== false ) {
        $this->setFieldParam( $newFieldName, 'id', $newFieldId );
      }
      else {
        if( $this->getFieldParam( $fieldName, 'id' ) !== null ) {
          $this->setFieldParam( $newFieldName, 'id', $newFieldName );
        }
      }

      $this->cloneValidationRules( $fieldName, $newFieldName );

      $result = true;
    }

    return $result;
  } // function cloneField

  /**
    Recupera los contenidos no internos de un campo del formulario
    @param string $fieldName Nombre del campo
  */
  public function getField( $fieldName ) {
    $result = null;

    if( $this->isFieldDefined( $fieldName ) ) {
      $result = $this->fields[ $fieldName ];
      unset( $result[ 'cgIntFrmFieldInfo' ] );
    }

    return $result;
  } // function getField

  /**
    Recupera el valor de un campo
    @param string $fieldName Nombre del campo
    @return mixed
  */
  public function getFieldValue( $fieldName ) {

    return $this->getFieldParam( $fieldName, 'value' );
  }

  /**
    Recupera un parametro de un campo
    @param string $fieldName Nombre del campo
    @param string $paramName Nombre del parametro
    @return mixed
  */
  public function getFieldParam( $fieldName, $paramName ) {
    $value = null;

    if( array_key_exists( $fieldName, $this->fields ) &&
      array_key_exists( $paramName, $this->fields[ $fieldName ] ) )
    {
      $value = $this->fields[ $fieldName ][ $paramName ];
    }

    return $value;
  }

  /**
    Recupera un parametro interno de un campo
    @param string $fieldName Nombre del campo
    @param string $paramName Nombre del parametro
    @return mixed
  */
  public function getFieldInternal( $fieldName, $paramName ) {
    $value = null;

    if( array_key_exists( $fieldName, $this->fields ) &&
      array_key_exists( $paramName, $this->fields[ $fieldName ][ 'cgIntFrmFieldInfo' ] ) )
    {
      $value = $this->fields[ $fieldName ][ 'cgIntFrmFieldInfo' ][ $paramName ];
    }

    return $value;
  }

  /**
    Recupera el tipo de un campo
    @param string $fieldName Nombre del campo
    @return string
  */
  public function getFieldType( $fieldName ) {

    return $this->getFieldParam( $fieldName, 'type' );
  }

  /**
    Verifica si se ha definido un campo
    @param string $fieldName Nombre del campo
    @return boolean
  */
  public function isFieldDefined( $fieldName ) {

    return array_key_exists( $fieldName, $this->fields );
  }

  /**
    Recupera los nombres de todos los campos
    @return TYPE
  */
  public function getFieldsNamesArray() {
    $fieldsNamesArray = array();
    foreach( $this->fields as $key => $val ) {
      array_push( $fieldsNamesArray, $key);
    }

    return $fieldsNamesArray;
  }

  /**
    Recupera los valores de todos los campos
    @return array
  */
  public function getValuesArray() {
    $fieldsValuesArray = array();
    $fieldsNamesArray = $this->getFieldsNamesArray();
    foreach( $fieldsNamesArray as $fieldName ) {
      if( $this->getFieldInternal( $fieldName, 'groupCloneRoot' ) !== true &&
        $this->getFieldInternal( $fieldName, 'groupElemRemoved' ) !== true )
      {
        // Procesamos los campos que no son raiz de campos agrupados
        $fieldsValuesArray[ $fieldName ] = $this->getFieldValue( $fieldName );

        // error_log( $fieldName .' === '. print_r( $fieldsValuesArray[ $fieldName ], true ) );
      }
      else {
        error_log( $fieldName .' IGNORADO!!! ' );
      }
    }

    return $fieldsValuesArray;
  } // fuction getValuesArray

  /**
    Recupera los valores de todos los campos
    @return array
  */
  public function getValuesGroupedArray() {
    $loadedGroups = array();
    $valuesArray = array( 'fields' => array(), 'groups' => array() );

    $fieldsNamesArray = $this->getFieldsNamesArray();
    foreach( $fieldsNamesArray as $fieldName ) {
      $groupName = $this->getFieldGroup( $fieldName );

      if( $groupName !== false ) {
        if( !in_array( $groupName, $loadedGroups ) ) {
          // Si el grupo del campo no ha sido procesado cargamos los campos del grupo juntos
          $loadedGroups[] = $groupName;
          $valuesArray[ 'groups' ][ $groupName ] = $this->getValuesGroupArray( $groupName );
        }
      }
      else {
        $valuesArray[ 'fields' ][ $fieldName ] = $this->getFieldValue( $fieldName );
        // error_log( $fieldName .' === '. print_r( $valuesArray[ 'fields' ][ $fieldName ], true ) );
      }
    }

    return $valuesArray;
  } // fuction getValuesArray

  /**
    Recupera los valores de todos los campos
    @return array
  */
  public function getValuesGroupArray( $groupName ) {
    $fieldsValuesArray = array();

    $groupFieldNames = $this->getGroupFields( $groupName );

    foreach( $this->getGroupIdElems( $groupName ) as $idElem ) {
      foreach( $groupFieldNames as $baseFieldName ) {
        $fieldName = $baseFieldName.'_C_'.$idElem;

        if( $this->getFieldInternal( $fieldName, 'groupElemRemoved' ) !== true ) {
          $fieldsValuesArray[ $idElem ][ $fieldName ] = $this->getFieldValue( $fieldName );
          error_log( $groupName.'/'.$idElem.'/'.$fieldName .' === '. print_r( $fieldsValuesArray[ $idElem ][ $fieldName ], true ) );
        }
        else {
          error_log( $groupName.'/'.$idElem.'/'.$fieldName .' IGNORADO!!! ' );
        }

      }
    }

    return $fieldsValuesArray;
  } // fuction getValuesArray

  /**
    Verifica si el valor de un campo es vacio
    @param string $fieldName Nombre del campo
    @return boolean
  */
  public function isEmptyFieldValue( $fieldName ) {
    $empty = true;

    $value = $this->getFieldValue( $fieldName );
    $type = $this->getFieldType( $fieldName );

    if( $type !== 'file' ) {
      if( is_array( $value ) ) {
        $empty = ( count( $value ) <= 0 );
      }
      else {
        $empty = ( $value === false || $value === '' );
      }
    }
    else {
      $empty = !( isset( $value['status'] ) && $value['status'] !== false && $value['status'] !== 'DELETE' );
    }

    return $empty;
  }










  /**
    Inicializa un grupo
    @param string $groupName Nombre del grupo
  */
  private function initGroup( $groupName ) {
    $this->groups[ $groupName ][ 'show' ] = 1;
    $this->groups[ $groupName ][ 'min' ] = 1;
    $this->groups[ $groupName ][ 'max' ] = 1;
    $this->groups[ $groupName ][ 'fields' ] = array();
    $this->groups[ $groupName ][ 'idElments' ] = array();
  }

  /**
    Define el grupo de un campo
    @param string $fieldName Nombre del campo
    @param string $groupName Nombre del grupo
  */
  public function setFieldGroup( $fieldName, $groupName ) {
    if( !$this->inGroup( $fieldName, $groupName ) ) {
      if( !$this->issetGroup( $groupName ) ) {
        $this->initGroup( $groupName );
      }
      $this->groups[ $groupName ][ 'fields' ][] = $fieldName;
      $this->setFieldInternal( $fieldName, 'groupName', $groupName );
    }
  }

  /**
    Recupera el grupo de un campo
    @param string $fieldName Nombre del campo
    @return string Nombre del grupo
  */
  public function getFieldGroup( $fieldName ) {
    $groupNameResult = false;

    $tmp = $this->getFieldInternal( $fieldName, 'groupName' );
    if( $tmp !== null ) {
      $groupNameResult = $tmp;
    }

    return $groupNameResult;
  }

  /**
    Recupera los campos de un grupo
    @param string $groupName Nombre del campo
    @return array
  */
  public function getGroupFields( $groupName ) {
    $fieldNames = false;

    if( $this->issetGroup( $groupName ) ) {
      $fieldNames = $this->groups[ $groupName ][ 'fields' ];
    }

    return $fieldNames;
  }

  /**
    Recupera los nombres de los grupos
    @return array
  */
  public function getGroupNames() {
    $groups = false;

    if( count( $this->groups ) > 0 ) {
      $groups = array_keys( $this->groups );
    }

    return( $groups );
  }

  /**
    Define los limites min y/o max de repeticiones de un grupo
    @param string $groupName Nombre del grupo
    @param string $min Numero min. de copias de grupo
    @param string $max Opcional - Numero max. de copias de grupo
  */
  public function setGroupLimits( $groupName, $show, $min = 'notExist', $max = 'notExist' ) {

    if( !$this->issetGroup( $groupName ) ) {
      $this->initGroup( $groupName );
    }

    // Controles
    if( !( $show >= 0 ) ) {
      $show = 1;
    }
    if( $show > 0 && $min === 'notExist' && $max === 'notExist' ) {
      $min = $show;
      $max = $show;
    }
    else {
      if( !( $min >= 0 ) ) {
        $min = 1;
      }
      if( !( $max > 0 ) ) {
        $max = false;
      }
    }

    // Correciones
    if( $max !== false && $max < $min ) {
      $max = $min;
    }
    if( $max !== false && $show > $max ) {
      $show = $max;
    }
    if( $show < $min ) {
      $show = $min;
    }

    $this->groups[ $groupName ][ 'show' ] = $show;
    $this->groups[ $groupName ][ 'min' ] = $min;
    $this->groups[ $groupName ][ 'max' ] = $max;
  }

  /**
    Recupera los parametros show, min y max de repeticiones de un grupo
    @param string $groupName Nombre del grupo
    @return array
  */
  public function getGroupLimits( $groupName, $key = false ) {
    $limits = false;

    if( $this->issetGroup( $groupName ) ) {
      if( $key && in_array( $key, array( 'show', 'min', 'max' ) ) ) {
        $limits = $this->groups[ $groupName ][ $key ];
      }
      else {
        $limits = array(
          'show' => $this->groups[ $groupName ][ 'show' ],
          'min'  => $this->groups[ $groupName ][ 'min' ],
          'max'  => $this->groups[ $groupName ][ 'max' ],
        );
      }
    }

    return $limits;
  }

  /**
    Recupera la lista de Ids de instancias de grupo
    @param string $groupName Nombre del grupo
  */
  public function getGroupIdElems( $groupName ) {
    $idElems = false;

    if( isset( $this->groups[ $groupName ][ 'idElments' ] ) ) {
      $idElems = array_keys(
        array_filter( $this->groups[ $groupName ][ 'idElments' ],
          function( $valor ) {
            return( $valor === true );
          }
        )
      );
    }

    return $idElems;
  }

  /**
    Recupera la lista de Ids de instancias de grupo eliminadas
    @param string $groupName Nombre del grupo
  */
  public function getGroupIdElemsRemoved( $groupName ) {
    $idElems = false;

    if( isset( $this->groups[ $groupName ][ 'idElments' ] ) ) {
      $idElems = array_keys(
        array_filter( $this->groups[ $groupName ][ 'idElments' ],
          function( $valor ) {
            return( $valor === false );
          }
        )
      );
    }

    return $idElems;
  }

  /**
    Recupera el num. de Ids de instancias de grupo
    @param string $groupName Nombre del grupo
  */
  public function countGroupElems( $groupName ) {
    $numElems = 0;

    if( isset( $this->groups[ $groupName ][ 'idElments' ] ) ) {
      $numElems = count(
        array_filter( $this->groups[ $groupName ][ 'idElments' ],
          function( $valor ) {
            return( $valor === true );
          }
        )
      );
    }

    return $numElems;
  }

  /**
    Verifica si existe un Id de instancia en el grupo
    @param string $groupName Nombre del grupo
    @param string $idElem Identificador de una instancia del grupo
  */
  public function issetGroupIdElem( $groupName, $idElem ) {

    return( isset( $this->groups[ $groupName ][ 'idElments' ][ $idElem ] ) );
  }

  /**
    Verifica si existe un Id de instancia en el grupo
    @param string $groupName Nombre del grupo
    @param string $idElem Identificador de una instancia del grupo
  */
  public function isGroupElemActive( $groupName, $idElem ) {

    return( isset( $this->groups[ $groupName ][ 'idElments' ][ $idElem ] ) &&
      $this->groups[ $groupName ][ 'idElments' ][ $idElem ] === true );
  }

  /**
    Crea una nueva instancia de grupo
    @param string $groupName Nombre del grupo
  */
  public function newGroupElem( $groupName ) {
    $idElem = false;

    if( isset( $this->groups[ $groupName ][ 'idElments' ] ) ) {

      // Conseguimos un ID para el nuevo elemento del grupo
      $idElem = 1 + count( $this->groups[ $groupName ][ 'idElments' ] );
      $this->groups[ $groupName ][ 'idElments' ][ $idElem ] = true;

      // Creamos y etiquetamos los campos para el nuevo elemento del grupo
      foreach( $this->getGroupFields( $groupName ) as $fieldName ) {
        $elemFieldName = $fieldName.'_C_'.$idElem;
        $elemFieldId = false;
        if( $this->getFieldParam( $fieldName, 'id' ) !== null ) {
          $elemFieldId = $this->getFieldParam( $fieldName, 'id' ).'_C_'.$idElem;
        }

        $this->cloneField( $fieldName, $elemFieldName, $elemFieldId );
        $this->setFieldInternal( $fieldName, 'groupCloneRoot', true );
        $this->setFieldInternal( $elemFieldName, 'groupCloneRoot', false );
        $this->setFieldInternal( $elemFieldName, 'groupIdElem', $idElem );
        $this->updateFieldToSession( $elemFieldName );
      }

      $this->updateGroupToSession( $groupName );
    }

    return $idElem;
  }

  /**
    Recupera el html de todos los campos del form (por campos)
    @return array
  */
  public function removeGroupElem( $groupName, $idElem ) {
    $response = false;

    if( $this->isGroupElemActive( $groupName, $idElem ) ) {

      // Marcamos o eliminamos el ID del elemento que retiramos del grupo
      $this->groups[ $groupName ][ 'idElments' ][ $idElem ] = false;
      //$key = array_search( $idElem, $this->groups[ $groupName ][ 'idElments' ] );
      //unset( $this->groups[ $groupName ][ 'idElments' ][ $key ] );

      // Eliminamos las reglas del elemento que retiramos del grupo
      foreach( $this->getGroupFields( $groupName ) as $fieldName ) {
        $elemFieldName = $fieldName.'_C_'.$idElem;
        $this->setFieldInternal( $elemFieldName, 'groupElemRemoved', true );
        $this->updateFieldToSession( $elemFieldName );
        $this->removeValidationRules( $elemFieldName );
      }

      $this->updateGroupToSession( $groupName );
      $response = true;
    }

    return $response;
  }

  /**
    Construye los elementos indicados para mostrar por defecto en un grupo
    @param string $groupName Nombre del grupo
    @return boolean
  */
  public function makeShowGroupElems( $groupName ) {

    $groupLimits = $this->getGroupLimits( $groupName );

    if( !($groupLimits[ 'min' ]===1 && $groupLimits[ 'max' ]===1 )
      && $this->countGroupElems( $groupName ) < $groupLimits[ 'show' ] )
    {
      for( $n = $this->countGroupElems( $groupName ); $n < $groupLimits[ 'show' ]; $n++ ) {
        $this->newGroupElem( $groupName );
      }
    }
  }

  /**
    Verifica si existe un grupo
    @param string $groupName Nombre del grupo
    @return boolean
  */
  public function issetGroup( $groupName ) {

    return(
      isset( $this->groups[ $groupName ] ) &&
      isset( $this->groups[ $groupName ][ 'fields' ] ) &&
      count( $this->groups[ $groupName ][ 'fields' ] ) > 0
    );
  }

  /**
    Verifica si un campo pertenece a un grupo
    @param string $fieldName Nombre del campo
    @param string $groupName Nombre del grupo
    @return boolean
  */
  public function inGroup( $fieldName, $groupName ) {

    return( $this->issetGroup( $groupName ) && in_array( $fieldName, $this->groups[ $groupName ][ 'fields' ] ) );
  }










  /*
  Ficheros
  */

  /**
    Convierte el tamaño de memoria de formato php.ini a bytes
    @param string $size Tamaño de la memoria configurada en php.ini
    @return integer
  */
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
    Procesa los ficheros temporales del form para colocarlos en su lugar definitivo y registrarlos
    @return boolean
  */
  public function processFileFields() {
    $result = true;

    foreach( $this->getFieldsNamesArray() as $fieldName ) {
      if( $result && $this->getFieldType( $fieldName ) === 'file' ) {
        // error_log( 'FILE: Almacenando fileField: '.$fieldName );

        $fileFieldValue = $this->getFieldValue( $fieldName );
        // error_log( print_r( $fileFieldValue, true ) );

        if( isset( $fileFieldValue['status'] ) && $fileFieldValue['status'] !== false ) {
          switch( $fileFieldValue['status'] ) {
            case 'LOAD':
              // error_log( 'processFileFields: LOAD' );

              $fileName = $this->secureFileName( $fileFieldValue['validate']['originalName'] );
              $destDir = $this->getFieldParam( $fieldName, 'destDir' );
              $fullDestPath = self::FILES_APP_PATH . $destDir;
              if( !is_dir( $fullDestPath ) ) {
                // TODO: CAMBIAR PERMISOS 0777
                if( !mkdir( $fullDestPath, 0777, true ) ) {
                  $result = false;
                  $this->addFieldRuleError( $fieldName, 'cogumelo',
                    'La subida del fichero ha fallado. (MD)' );
                  error_log( 'Imposible crear el directorio necesario. ' . $fullDestPath );
                }
              }

              if( !$this->existErrors() ) {
                // TODO: DETECTAR Y SOLUCIONAR COLISIONES!!!
                error_log( 'Movendo ' . $fileFieldValue['validate']['absLocation'] . ' a ' . $fullDestPath.'/'.$fileName );
                if( !rename( $fileFieldValue['validate']['absLocation'], $fullDestPath.'/'.$fileName ) ) {
                  $result = false;
                  $this->addFieldRuleError( $fieldName, 'cogumelo',
                    'La subida del fichero ha fallado. (MF)' );
                  error_log( 'Imposible mover el fichero al directorio adecuado.' . $fileFieldValue['validate']['absLocation'] . ' a ' . $fullDestPath.'/'.$fileName );
                }
              }

              if( !$this->existErrors() ) {
                $fileFieldValue['status'] = 'LOADED';
                $fileFieldValue['values'] = $fileFieldValue['validate'];
                $fileFieldValue['values']['absLocation'] = $destDir.'/'.$fileName;
                $this->setFieldValue( $fieldName, $fileFieldValue );
                $this->updateFieldToSession( $fieldName );
                error_log( 'Info: processFileFields OK. values: ' . print_r( $fileFieldValue, true ) );
              }
              break;
            case 'REPLACE':
              error_log( 'processFileFields: REPLACE' );

              // TODO: EJECUTAR LOS PASOS PARA EL ESTADO REPLACE!!!

              break;
            case 'DELETE':
              error_log( 'processFileFields: DELETE' );

              // TODO: EJECUTAR LOS PASOS PARA EL ESTADO DELETE!!!

              break;
            case 'EXIST':
              error_log( 'processFileFields OK: EXIST - NADA QUE HACER' );
              break;
          }
        } // if( isset( $fileFieldValue['status'] ) && $fileFieldValue['status'] !== false )

        // TODO: FALTA GUARDA LOS DATOS DEFINITIVOS DEL FICHERO!!!
        // En caso de fallo $result = false;
      } // if( $this->getFieldType( $fieldName ) === 'file' )
    } // foreach( $this->getFieldsNamesArray() as $fieldName )

    if( !$result ) {
      // Si algo ha fallado, desacemos los cambios
      $this->revertFileFieldsLoaded();
    }

    return $result;
  } // function processFileFields

  /**
    Procesa los ficheros temporales del form para pasarlos de su lugar definitivo al temporal
    @return boolean
  */
  public function revertFileFieldsLoaded() {
    $result = true;

    foreach( $this->getFieldsNamesArray() as $fieldName ) {
      if( $this->getFieldType( $fieldName ) === 'file' ) {
        error_log( 'FILE: Revertindo LOADED fileField: '.$fieldName );

        $fileFieldValue = $this->getFieldValue( $fieldName );
        error_log( print_r( $fileFieldValue, true ) );

        if( isset( $fileFieldValue['status'] ) && $fileFieldValue['status'] === 'LOADED' ) {
          $absLocationActual = self::FILES_APP_PATH . $fileFieldValue['values']['absLocation'];
          $absLocationAnterior = $fileFieldValue['validate']['absLocation'];

          error_log( 'Devolvendo ' . $absLocationActual .' a '. $absLocationAnterior );
          if( !rename( $absLocationActual, $absLocationAnterior ) ) {
            $result = false;
            $this->addFieldRuleError( $fieldName, 'cogumelo',
              'La subida del fichero ha fallado. (MR)' );
            error_log( 'Imposible devolver el fichero al directorio adecuado.' . $absLocationActual .' a '. $absLocationAnterior );
          }
          else {
            $fileFieldValue['status'] = 'LOAD';
            unset( $fileFieldValue['values'] );
            $this->setFieldValue( $fieldName, $fileFieldValue );
            $this->updateFieldToSession( $fieldName );
            error_log( 'Info: revertFileFieldsLoaded OK. values: ' . print_r( $fileFieldValue, true ) );
          }

        } // if( isset( $fileFieldValue['status'] ) && $fileFieldValue['status'] === 'LOADED' )
      } // if( $this->getFieldType( $fieldName ) === 'file' )
    } // foreach( $this->getFieldsNamesArray() as $fieldName )

    return $result;
  } // function revertFileFieldsLoaded










  /**
    Mover con seguridad un fichero del tmp de PHP al tmp de nuestra aplicacion
    @param string $fileTmpLoc Fichero temporal de PHP
    @param string $fileName Nombre del fichero
    @return string Fichero temporal de la App. En caso de error: false
  */
  public function tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName ) {
    // error_log( 'tmpPhpFile2tmpFormFile: '.$fileTmpLoc.' --- '.$fileName);
    $result = false;
    $error = false;

    $tmpCgmlFormPath = self::FILES_TMP_PATH .'/'. preg_replace( '/[^0-9a-z_\.-]/i', '_', $this->getTokenId() );
    if( !is_dir( $tmpCgmlFormPath ) ) {

      // TODO: CAMBIAR PERMISOS 0777

      if( !mkdir( $tmpCgmlFormPath, 0777, true ) ) {
        $error = 'Imposible crear el dir. necesario: '.$tmpCgmlFormPath;
        error_log($error);
      }
    }

    if( !$error ) {
      $secureName = $this->secureFileName( $fileName );

      $tmpLocationCgml = $tmpCgmlFormPath .'/'. $secureName;

      // TODO: FALTA VER QUE NON SE PISE UN ANTERIOR!!!

      if( !move_uploaded_file( $fileTmpLoc, $tmpLocationCgml ) ) {
        $error = 'Fallo de move_uploaded_file pasando ('.$fileTmpLoc.') a ('.$tmpLocationCgml.')';
        error_log($error);
      }
      else {
        $result = $tmpLocationCgml;
      }
    }

    // error_log( 'tmpPhpFile2tmpFormFile ERROR: '.$error );
    // error_log( 'tmpPhpFile2tmpFormFile RET: '.$result );

    return $result;
  } // function tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName )

  /**
    Crea un nombre de fichero seguro a partir del nombre de fichero deseado
    @param string $fileName Nombre del campo
    @return string
  */
  public function secureFileName( $fileName ) {
    // error_log( 'secureFileName: '.$fileName );
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

    // error_log( 'secureFileName RET: '.$fileName );

    return $fileName;
  }

  /*
  Ficheros (FIN)
  */


  /*
  HTML y JS
  */

  /**
    Recupera el html y js que forman el form
    @return string
  */
  public function getHtmlForm() {
    $html='';

    $html .= $this->getHtmpOpen()."\n";
    $html .= $this->getHtmlFieldsAndGroups()."\n";
    $html .= $this->getHtmlClose()."\n";

    $html .= $this->getScriptCode()."\n";

    return $html;
  }

  /**
    Recupera el html de la apertura del form
    @return string
  */
  public function getHtmpOpen() {
    $html='';

    $html .= '<form name="'.$this->getName().'" id="'.$this->id.'" data-cgmInId="'.$this->getTokenId().'" ';
    $html .= ' class="'.self::CSS_PRE.' '.self::CSS_PRE.'-form-'.$this->getName().'" ';
    if( $this->action ) {
      $html .= ' action="'.$this->action.'"';
    }
    $html .= ' method="'.$this->method.'">';

    // Guardamos en sesion de forma automatica al comenzar a generar el formulario
    $this->saveToSession();

    return $html;
  }

  /**
    Recupera el html de todos los campos del form
    @return string
  */
  public function getHtmlFields() {

    return implode( "\n", $this->getHtmlFieldsArray() );
  }










  /**
    Recupera el html de todos los campos del form
    @return string
  */
  public function getHtmlFieldsAndGroups() {
    $html = '';

    $loadedGroups = array();
    $htmlFields = $this->getHtmlFieldsArray();

    foreach( $htmlFields as $fieldName => $htmlField ) {
      $groupName = $this->getFieldGroup( $fieldName );

      if( $groupName !== false ) {
        if( !in_array( $groupName, $loadedGroups ) ) {
          // Si el grupo del campo no ha sido procesado cargamos los campos del grupo juntos
          $loadedGroups[] = $groupName;
          $html .= $this->getHtmlGroup( $groupName )."\n";
        }
      }
      else {
        $html .= $htmlField."\n";
      }
    }

    return $html;
  }

  /**
    Recupera el html de un grupo del form
    @return string
  */
  public function getHtmlGroup( $groupName ) {
    $html = '';

    if( $this->issetGroup( $groupName ) ) {
      $html = '<div class="'.self::CSS_PRE.'-wrap '.self::CSS_PRE.'-group-wrap '.self::CSS_PRE.'-group-'.$groupName.'">'."\n";
      $html .= '<label>Grupo '.$groupName.'</label>'."\n";

      $groupLimits = $this->getGroupLimits( $groupName );

      if( $groupLimits[ 'min' ]===1 && $groupLimits[ 'max' ]===1 ) {
        $html .= $this->getHtmlGroupElement( $groupName )."\n";
      }
      else {
        if( $this->countGroupElems( $groupName ) < $groupLimits[ 'show' ] ) {
          $this->makeShowGroupElems( $groupName );
        }
        foreach( $this->getGroupIdElems( $groupName ) as $idElem ) {
          $html .= $this->getHtmlGroupElement( $groupName, $idElem )."\n";
        }
        $html .= '<div data-form-id="'.$this->id.'" class="addGroupElement '.self::CSS_PRE.'-group-'.$groupName.'" groupName="'.$groupName.'">MAS</div>'."\n";
        $html .= '<div class="JQVMC-group-'.$groupName.'"></div>'."\n";
      }

      $html .= '</div><!-- /'.self::CSS_PRE.'-group-'.$groupName.' -->';
    }

    return $html;
  }

  /**
    Recupera el html de una instancia de un grupo del form
    @return string
  */
  public function getHtmlGroupElement( $groupName, $idElem = false ) {
    $html = '';

    if( $this->issetGroup( $groupName ) ) {

      if( $idElem === false ) {
        $groupFieldNames = $this->getGroupFields( $groupName );
      }
      else {
        foreach( $this->getGroupFields( $groupName ) as $fieldName ) {
           $groupFieldNames[] = $fieldName.'_C_'.$idElem;
        }
      }

      $html .= '<div class="'.self::CSS_PRE.'-wrap '.self::CSS_PRE.'-groupElem'.
        ( $idElem !== false ? ' '.self::CSS_PRE.'-groupElem_C_'.$idElem : '' ).
        '">'."\n";

      $html .= implode( "\n", $this->getHtmlFieldsArray( $groupFieldNames ) )."\n";

      if( $idElem !== false ) {
        $html .= '<div data-form-id="'.$this->id.'" class="removeGroupElement '.self::CSS_PRE.'-group-'.$groupName.'" '.
          'groupName="'.$groupName.'" groupIdElem="'.$idElem.'">QUITAR</div>'."\n";
      }

      $html .= '</div>';
    }

    return $html;
  }

  /**
    Recupera el html de todos los campos del form (por campos)
    @return array
  */
  public function getHtmlFieldsArray( $fieldNames = false ) {
    $html = array();

    if( $fieldNames === false ) {
      $fieldNames = $this->getFieldsNamesArray();
    }
    foreach( $fieldNames as $fieldName ) {
      if( $this->getFieldInternal( $fieldName, 'groupCloneRoot' ) !== true ) {
        // Procesamos los campos que no son raiz de campos agrupados
        $html[ $fieldName ] = '<div class="'.self::CSS_PRE.'-wrap '.self::CSS_PRE.'-field-'.$fieldName.
          ( $this->getFieldType( $fieldName ) === 'file' ? ' '.self::CSS_PRE.'-fileField ' : '' ).
          '">'.$this->getHtmlField( $fieldName ).'</div>';
      }
    }

    return $html;
  }

  /**
    Recupera el html de un campo del form
    @param string $fieldName Nombre del campo
    @return string
  */
  public function getHtmlField( $fieldName, $idElem = false ) {
    $html = '';

    $fieldName = $idElem!==false ? $fieldName.'_C_'.$idElem : $fieldName;

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
          $html .= '<label>'.$inputAndText['input'].'<span class="labelText">'.$inputAndText['text'].'</span></label>';
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
    Recupera el html de un campo del form en fragmentos
    @param string $fieldName Nombre del campo
    @return array
  */
  public function getHtmlFieldArray( $fieldName ) {
    $html = array();

    $field = $this->getField( $fieldName );

    $cloneOf = $this->getFieldInternal( $fieldName, 'cloneOf' );
    $groupName = $this->getFieldInternal( $fieldName, 'groupName' );

    $myFielId = isset( $field['id'] ) ? $field['id'] : false;

    //$myFielId = ( $myFielId === false && isset( $field['htmlEditor'] ) ) ? $fieldName : false;
    if( isset( $field['htmlEditor'] ) ) {
      $this->htmlEditor = true;
      if( $myFielId === false ) {
        $myFielId = $fieldName;
      }
    }

    $html['fieldType'] = $field['type'];

    if( isset( $field['label'] ) ) {
      $html['label'] = '<label';
      $html['label'] .= ( $myFielId ? ' for="'.$myFielId.'"' : '' );
      $html['label'] .= ' class="'.self::CSS_PRE.( isset( $field['class'] ) ? ' '.$field['class'] : '' ).'"';
      $html['label'] .= isset( $field['style'] ) ? ' style="'.$field['style'].'"' : '';
      $html['label'] .= '>'.$field['label'].'</label>';
    }

    $attribs = 'form="'.$this->id.'"';
    $attribs .= ( $myFielId ? ' id="'.$myFielId.'"' : '' );
    $attribs .= ' class="'.self::CSS_PRE.'-field '.self::CSS_PRE.'-field-'.$fieldName.
      ( ( $field['type'] === 'file' ) ? ' '.self::CSS_PRE.'-fileField' : '' ).
      ( $cloneOf ? ' '.self::CSS_PRE.'-cloneOf-'.$cloneOf : '' ).
      ( $groupName ? ' '.self::CSS_PRE.'-group-'.$groupName : '' ).
      ( isset( $field['htmlEditor'] ) ? ' '.self::CSS_PRE.'-htmlEditor' : '' ).
      ( isset( $field['class'] ) ? ' '.$field['class'] : '' ).
      '"';
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
    $attribs .= isset( $field['htmlEditor'] ) ? ' contenteditable="true"' : '';

    foreach( $field as $dataKey => $dataValue ) {
      if( strpos( $dataKey, 'data-' ) === 0 ) {
        $attribs .= ' '.$dataKey.'="'.$dataValue.'"';
      }
    }

    switch( $field['type'] ) {
      case 'select':
        $html['inputOpen'] = '<select name="'.$fieldName.'"'. $attribs.'>';

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

        $html['inputClose'] = '</select><!-- select '.$fieldName.' -->';
        break;

      case 'checkbox':
      case 'radio':
        $html['options'] = array();
        foreach( $field['options'] as $val => $text ) {
          $html['options'][$val] = array();
          $html['options'][$val]['input'] = '<input name="'.$fieldName.'" value="'.$val.'"'.
            ' type="'.$field['type'].'"'.$attribs.'>';
          $html['options'][$val]['text'] = $text;
          $html['options'][$val]['label'] = $text!='' ? '<label>'.$text.'</label>' : '';
        }
        // Colocamos los checked
        if( isset( $field['value'] ) ) {
          $values = is_array( $field['value'] ) ? $field['value'] : array( $field['value'] );
          foreach( $values as $val ) {
            $html['options'][$val]['input'] = str_replace(
              'name="'.$fieldName.'" value="'.$val.'"',
              'name="'.$fieldName.'" value="'.$val.'" checked="checked"',
              $html['options'][$val]['input'] );
            if( $field['type']=='radio' ) {
              break; // Radio solo puede tener 1 valor
            }
          }
        }
        break;

      case 'textarea':
        $html['inputOpen'] = '<textarea name="'.$fieldName.'"'.$attribs.'>';
        $html['value'] = isset( $field['value'] ) ? $field['value'] : '';
        $html['inputClose'] = '</textarea>';
        break;

      case 'file':
        $html['input'] = '<input name="'.$fieldName.'"';
        // $html['input'] .= isset( $field['value'] ) ? ' value="'.$field['value'].'"' : '';
        $html['input'] .= ' type="'.$field['type'].'"'.$attribs.'>';
        break;

      case 'reserved':
        break;

      default:
        // button, file, hidden, password, range, text, color, date, datetime, datetime-local,
        // email, image, month, number, search, tel, time, url, week
        $html['input'] = '<input name="'.$fieldName.'"';
        $html['input'] .= isset( $field['value'] ) ? ' value="'.$field['value'].'"' : '';
        $html['input'] .= ' type="'.$field['type'].'"'.$attribs.'>';
        break;
    }

    return $html;
  } // function getHtmlFieldArray










  /**
    Recupera el html del cierre del form
    @return string
  */
  public function getHtmlClose() {
    $html = '</form><!-- '.$this->getName().' -->';

    return $html;
  }

  /**
    Añade tareas para ejecutar en el navegador al finalizar bien el submit
    @param string $name Clave del suceso: accept, redirect, ...
    @param string $success Contenido del evento: Msg, url, ...
  */
  public function setSuccess( $name, $success = true ) {
    //error_log( 'setSuccess: name='. $name . ' success=' .  $success );
    // jsEval : Ejecuta el texto indicado con un eval
    // accept : Muestra el texto como un alert
    // redirect : Pasa a la url indicada con un window.location.replace
    // reload : window.location.reload
    // resetForm : Borra el formulario

    $this->success[ $name ] = $success;
  }

  /**
    Recupera las tareas que se han definido para el navegador al finalizar bien el submit
    @return array
  */
  public function getSuccess() {
    // error_log( 'getSuccess: ' . print_r( $this->success, true ) );

    return $this->success;
  }

  /**
    Recupera un JSON con el Ok o los errores que hay que enviar al navegador
    @return string JSON
  */
  public function getJsonResponse( $moreInfo = false ) {
    $json = '';
    if( !$this->existErrors() ) {
      $json = $this->jsonFormOk( $moreInfo );
    }
    else {
      // $this->addFormError( 'NO SE HAN GUARDADO LOS DATOS.', 'formError' );
      $json = $this->jsonFormError( $moreInfo );
    }

    return $json;
  }

  /**
    Envía el JSON con el Ok o los errores al navegador
    @return string JSON
  */
  public function sendJsonResponse( $moreInfo = false ) {
    $json = $this->getJsonResponse( $moreInfo );

    header('Content-Type: application/json; charset=utf-8');
    echo $json;

    return $json;
  }

  /**
    Recupera un JSON de OK con los sucesos que hay que lanzar en el navegador
    @return string JSON
  */
  public function jsonFormOk( $moreInfo = false ) {

    return $this->getJsonOk( $moreInfo );
  }

  /**
    Recupera un JSON de OK con los sucesos que hay que lanzar en el navegador
    @return string JSON
  */
  public function getJsonOk( $moreInfo = false ) {
    $result = array(
      'result' => 'ok',
      // 'valuesDEBUG' => $this->getValuesGroupedArray(),
      'success' => $this->getSuccess()
    );
    if( $moreInfo !== false ) {
      $result['moreInfo'] = $moreInfo;
    }

    return json_encode( $result );
  }

  /**
    Recupera un JSON de ERROR con los errores que hay que mostrar en el navegador
    @return string JSON
  */
  public function jsonFormError( $moreInfo = false ) {

    return $this->getJsonError( $moreInfo );
  }

  /**
    Recupera un JSON de ERROR con los errores que hay que mostrar en el navegador
    @return string JSON
  */
  public function getJsonError( $moreInfo = false ) {
    $jvErrors = array();

    foreach( $this->fieldErrors as $fieldName => $fieldRules ) {
      foreach( $fieldRules as $ruleName => $msgRuleError ) {
        $ruleParams = false;
        if( isset( $this->rules[ $fieldName ][ $ruleName ] ) ) {
          $ruleParams = $this->rules[ $fieldName ][ $ruleName ];
        }
        $jvErrors[] = array( 'fieldName' => $fieldName, 'ruleName' => $ruleName,
          'ruleParams' => $ruleParams, 'JVshowErrors' => array( $fieldName => $msgRuleError ) );
      }
    }

    foreach( $this->formErrors as $formError ) {
      // Errores globales (no referidos a un field determinado)
      $jvErrors[] = array( 'fieldName' => false, 'JVshowErrors' => $formError );
    }

    $result = array(
      'result' => 'error',
      'jvErrors' => $jvErrors
    );
    if( $moreInfo !== false ) {
      $result['moreInfo'] = $moreInfo;
    }

    return json_encode( $result );
  }

  /*
  HTML y JS (FIN)
  */


  /*
  Validaciones y gestion de errores
  */

  /**
    Establece una regla de validacion para un campo
    @param string $fieldName Nombre del campo
    @param string $ruleName Nombre de la regla
    @param mixed $ruleParams
  */
  public function setValidationRule( $fieldName, $ruleName, $ruleParams = true ) {
    $this->rules[ $fieldName ][ $ruleName ] = $ruleParams;
  }

  /**
    Recupera las reglas de validacion para un campo
    @param string $fieldName Nombre del campo
    @param array
  */
  public function getValidationRules( $fieldName ) {
    $fieldRules = false;
    if( isset( $this->rules[ $fieldName ] ) ) {
      $fieldRules = $this->rules[ $fieldName ];
    }

    return $fieldRules;
  }

  /**
    Copia todas las reglas de un campo a otro
    @param string $fieldNameFrom Nombre del campo
    @param string $fieldNameTo Nombre del campo
  */
  public function cloneValidationRules( $fieldNameFrom, $fieldNameTo ) {
    // error_log( 'cloneValidationRules: '.$fieldNameFrom.', '.$fieldNameTo );
    $fieldRules = $this->getValidationRules( $fieldNameFrom );
    if( is_array( $fieldRules ) ) {
      foreach( $fieldRules as $ruleName => $ruleParams ) {
        // error_log( 'cloneValidationRules: '.$ruleName );
        $this->setValidationRule( $fieldNameTo, $ruleName, $ruleParams );
      }
      $this->updateFieldRulesToSession( $fieldNameTo );
    }
  }

  /**
    Elimina todas las reglas de un campo
    @param string $fieldName Nombre del campo
  */
  public function removeValidationRules( $fieldName ) {
    error_log( 'removeValidationRules: '.$fieldName );

    if( isset( $this->rules[ $fieldName ] ) ) {
      unset( $this->rules[ $fieldName ] );
      $this->updateFieldRulesToSession( $fieldName );
    }
  }

  /**
    Establece el mensaje para un campo para el JQueryValidate
    @param string $fieldName Nombre del campo
    @param string $msg
  */
  public function setValidationMsg( $fieldName, $msg ) {
    $this->messages[$fieldName] = $msg;
  }

  /**
    Verifica si el campo es obligatorio
    @param string $fieldName Nombre del campo
    @return boolean
  */
  public function isRequiredField( $fieldName ) {

    return isset( $this->rules[ $fieldName ][ 'required' ] );
  }

  /**
    Regista el objeto que contiene los validadores
    @param object $validationObj
  */
  public function setValidationObj( $validationObj ) {
    $this->validationObj = $validationObj;
  }

  /**
    Verifica si se ha registrado el objeto con los validadores
    @return boolean
  */
  private function issetValidationObj() {

    // Si no hay un validador definido, intentamos cargar los validadores predefinidos
    if( $this->validationObj === null ) {
      $this->setValidationObj( new FormValidators() );
    }

    return( $this->validationObj !== null );
  }

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

    if( $this->issetValidationObj() ) {
      $validate = $this->validationObj->evaluateRule( $fieldName, $fieldValue, $ruleName, $ruleParams );
    }
    else {
      error_log( 'FALTA CARGAR LOS VALIDADORES' );
    }

    return $validate;
  }










  /**
    Verifica que se cumplen todas las reglas establecidas
    @return boolean
  */
  public function validateForm() {
    error_log( 'validateForm:' );

    $formValidated = true;

    // Tienen que existir los validadores y los valores del form
    if( $this->issetValidationObj() ) {
      foreach( $this->rules as $fieldName => $fieldRules ) {
        if( $this->getFieldInternal( $fieldName, 'groupCloneRoot' ) !== true ) {
          // Procesamos los campos que no son raiz de campos agrupados
          // error_log( 'validateForm: campo: '.$fieldName );
          $fieldValidated = $this->validateField( $fieldName );
          $formValidated = $formValidated && $fieldValidated;
        }
      } // foreach( $this->rules as $fieldName => $fieldRules )
    } // if( $this->issetValidationObj() )
    else {
      $formValidated = false;
      error_log( 'FALTA CARGAR LOS VALIDADORES' );
    }

    return $formValidated;
  } // function validateForm()










  /**
    Verifica que se cumplen las reglas establecidas para un campo
  * @param string $fieldName Nombre del campo
  * @return boolean
  */
  public function validateField( $fieldName ) {
    // error_log( 'validateField: '.$fieldName );
    $fieldValidated = true;

    if( $this->isEmptyFieldValue( $fieldName ) ) {
      if( $this->isRequiredField( $fieldName ) ) {
        error_log( 'ERROR: evaluateRule( '.$fieldName.', VACIO, required, ...  )' );
        $this->addFieldRuleError( $fieldName, 'required' );
        //$this->fieldErrors[ $fieldName ][ 'required' ] = false;
        $fieldValidated = false;
      }
      else {
        //error_log( 'evaluateRule: VACIO e non required = ok' );
        $fieldValidated = true;
      }
    } // if( $this->isEmptyFieldValue( $fieldName ) )
    else {

      $fieldRules = $this->getValidationRules( $fieldName );
      $fieldType = $this->getFieldType( $fieldName );
      $fieldValues = $this->getFieldValue( $fieldName );

      // Hay que tener cuidado con ciertos fieldValues con estructura de array pero que son un único elemento
      if( !is_array( $fieldValues ) || ( $fieldType === 'file' && isset( $fieldValues['validate']['name'] ) ) ) {
        $fieldValues = array( $fieldValues );
      }

      foreach( $fieldValues as $value ) {
        $fieldValidateValue = false;

        //error_log( 'validando '.$fieldName.' = '.print_r( $value, true ) );

        //error_log( 'evaluateRule: non VACIO - Evaluar contido coas reglas...' );
        $fieldValidateValue = true;
        foreach( $fieldRules as $ruleName => $ruleParams ) {
          //error_log( 'evaluateRule( '.$fieldName.', '.print_r( $value, true ).', '.$ruleName.', '.print_r( $ruleParams, true ) .' )' );

          if( $ruleName === 'equalTo' ) {
            $fieldRuleValidate = ( $value === $this->getFieldValue( str_replace('#', '', $ruleParams )) );
          }
          else {
            $fieldRuleValidate = $this->evaluateRule( $fieldName, $value, $ruleName, $ruleParams );
          }
          //error_log( 'evaluateRule RET: '.print_r( $fieldRuleValidate, true ) );

          if( !$fieldRuleValidate ) {
            error_log( 'ERROR: evaluateRule( '.$fieldName.', '.print_r( $value, true ).', '.$ruleName.', '.print_r( $ruleParams, true ) .' )' );
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
    Añade un mensaje de error al formulario
    @param string $paramName
    @param string $paramName
    @return TYPE
  */
  public function addFormError( $msgText, $msgClass = false ) {
    error_log( "addFormError: $msgText, $msgClass" );
    $this->formErrors[] = array( 'msgText' => $msgText, 'msgClass' => $msgClass );
  }

  /**
    Añade un mensaje de error a un campo del formulario
    @param string $fieldName Nombre del campo
    @param TYPE $paramName
    @return TYPE
  */
  public function addFieldRuleError( $fieldName, $ruleName, $msgRuleError = false ) {
    error_log( "addFieldRuleError: $fieldName, $ruleName, $msgRuleError " );
    $this->fieldErrors[ $fieldName ][ $ruleName ] = $msgRuleError;
  }

  /**
    Añade un mensaje de error a un grupo del formulario
    @param string $fieldName Nombre del campo
    @param TYPE $paramName
    @return TYPE
  */
  public function addGroupRuleError( $groupName, $ruleName, $msgRuleError = false ) {
    error_log( "addGroupRuleError: $groupName, $ruleName, $msgRuleError " );
    $this->fieldErrors[ $groupName ][ $ruleName ] = $msgRuleError;
  }

  /**
    Verifica si se han añadido errores
    @return boolean
  */
  public function existErrors() {

    return( count( $this->fieldErrors ) > 0 || count( $this->formErrors ) > 0 );
  }

  /**
    Verifica si se han añadido errores a un campo
    @param string $fieldName Nombre del campo
    @return boolean
  */
  public function existFieldErrors( $fieldName ) {

    return( isset( $this->fieldErrors[ $fieldName ] ) || count( $this->formErrors[ $fieldName ] ) > 0 );
  }

  /*
  Validaciones y gestion de errores (FIN)
  */


  /**
    Recupera el html con el JS del form
    @return string
  */
  public function getScriptCode() {
    $html = '';

    $separador = '';

    $html .= '<!-- Cogumelo module form ' . $this->getName() . ' -->' . "\n";


    if( $this->htmlEditor ) {
      form::loadDependence( 'ckeditor' );
    }


    $html .= '<script>' . "\n";

    $html .= '$( document ).ready( function() {'."\n";

    $html .= '  $validateForm_' . $this->id . ' = setValidateForm( "' . $this->id . '", ';
    $html .= ( count( $this->rules ) > 0 ) ? json_encode( $this->rules ) : 'false';
    $html .= ', ';
    $html .= ( count( $this->messages ) > 0 ) ? json_encode( $this->messages ) : 'false';
    $html .= ' );'."\n";

    // if( count( $this->messages ) > 0 ) {
    //   $html .= $separador.'    messages: '.json_encode( $this->messages )."\n";
    //   $separador = '    ,'."\n";
    // }

    $html .= '  console.log( $validateForm_'.$this->id.' );'."\n";



    foreach( $this->getFieldsNamesArray() as $fieldName ) {
      if( $this->getFieldType( $fieldName ) === 'file' ) {
        $fileInfo = $this->getFieldValue( $fieldName );
        if( $fileInfo[ 'status' ] === 'EXIST' ) {
          $html .= '  fileFieldToOk( "'.$this->id.'", "'.$fieldName.'", '.
            '"'.$fileInfo[ 'prev' ][ 'name' ].'", "'.$fileInfo[ 'prev' ][ 'id' ].'" );'."\n";
        }
      }
    }



    if( $this->htmlEditor ) {
      $html .= '  activateHtmlEditor( "'.$this->id.'" );'."\n";
    }

    $html .= '});'."\n";
    $html .= '</script>'."\n";

    $html .= '<!-- Cogumelo module form '.$this->getName().' - END -->'."\n";

    //$html .= '<pre>'. print_r( $this->fields, true ) .'</pre>';

    return $html;
  } // function getScriptCode


} // END FormController class
