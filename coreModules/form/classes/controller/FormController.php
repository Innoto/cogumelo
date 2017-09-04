<?php

error_reporting( -1 );
/*
  CAMBIOS

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

  public $cssPrefix = false; // Prefijo para marcar las clases CSS creadas automaticamente
  public $tmpFilesPath = false; // Ruta a partir de la que se crean los directorios y ficheros temporales subidos

  public $langDefault = false;
  public $langAvailable = false;

  private $tokenId = false; // Identificador interno del formulario
  private $name = false;
  private $id = false;
  private $action = false;
  private $success = false;
  private $method = 'post';
  private $enctype = 'multipart/form-data';
  private $captchaUse = false;
  private $captchaResponse = false;
  private $keepAlive = false;
  private $enterSubmit = null;
  private $formMarginTop = 0;
  private $fields = [];
  private $rules = [];
  private $groups = [];
  private $messages = [];

  // POST submit
  private $postData = null;
  private $postValues = null;
  private $validationObj = null;
  private $fieldErrors = [];
  private $formErrors = [];

  private $htmlEditor = false;

  private $replaceAcents = array(
    'from' => array( 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï',
      'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä',
      'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù',
      'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č',
      'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ',
      'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ',
      'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň',
      'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ',
      'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů',
      'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ',
      'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ',
      'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ' ),
    'to'   => array( 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I',
      'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a',
      'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u',
      'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c',
      'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G',
      'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i',
      'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N',
      'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S',
      's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u',
      'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o',
      'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A',
      'a', 'A', 'a', 'O', 'o' )
  );



  /**
   * Constructor. Crea el TokenId y, si se envian, establece Name y Action del formulario
   *
   * @param string $name Name del formulario
   * @param string $action Action del formulario
   */
  public function __construct( $name = false, $action = false ) {
    $this->setTokenId( 'new'.$name.$action );
    if( $name !== false ) {
      $this->setName( $name );
    }
    if( $action !== false ) {
      $this->setAction( $action );
    }

    $this->cssPrefix = Cogumelo::getSetupValue( 'mod:form:cssPrefix' );
    $this->tmpPath = Cogumelo::getSetupValue( 'mod:form:tmpPath' );

    $this->langDefault = Cogumelo::getSetupValue( 'lang:default' );
    $langsConf = Cogumelo::getSetupValue( 'lang:available' );
    if( is_array( $langsConf ) ) {
      $this->langAvailable = array_keys( $langsConf );
    }
  }

  /**
   * Clone. Actualiza el TokenId si se clona el objeto
   */
  public function __clone() {
    $this->setTokenId( 'clone'.$this->getName().$this->getAction() );
  }

  public function reset( $name = false, $action = false ) {
    Cogumelo::debug('FormController: NOTICE: form->reset()' );
    $formSessionId = 'CGFSI_'.$this->getTokenId();
    unset( $_SESSION[ $formSessionId ] );

    $this->name = false;
    $this->id = false;
    $this->tokenId = false;
    $this->action = false;
    $this->success = false;
    $this->method = 'post';
    $this->enctype = 'multipart/form-data';
    $this->captchaUse = false;
    $this->keepAlive = false;
    $this->enterSubmit = null;
    $this->formMarginTop = 0;
    $this->fields = [];
    $this->rules = [];
    $this->groups = [];
    $this->messages = [];

    $this->setTokenId( 'new'.$name.$action );
    if( $name !== false ) {
      $this->setName( $name );
    }
    if( $action !== false ) {
      $this->setAction( $action );
    }
  }

  /**
    Crea el TokenId único del formulario. NO es el id del FORM.
    @return string
   */
  public function setTokenId( $saltText = '' ) {
    $tmp = 'cf-'.uniqid().'-'.session_id().'-'.$saltText.rand(0,999);
    $this->tokenId = sha1( $tmp );
    $this->setField( 'cgIntFrmId', array( 'type' => 'hidden', 'value' => $this->tokenId ) );

    return $this->tokenId;
  }

  /**
    Recupera el TokenId único del formulario. Si no existe, se crea. NO es el id del FORM.
    @return string
   */
  public function getTokenId() {
    if( $this->tokenId === false ) {
      $this->setTokenId( 'new'.$this->getName().$this->getAction() );
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
    $this->setTokenId( 'new'.$name );
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
   * Establece el uso o no del proceso de keep alive y el tiempo en minutos
   *
   * @param bool $status Action del formulario
   */
  public function setKeepAlive( $status = true ) {
    if( is_int( $status ) ) {
      $this->keepAlive = ( $status > 0 ) ? $status : false;
    }
    else {
      $this->keepAlive = ( $status === true ) ? true : false;
    }
  }

  /**
   * Obtiene el uso o no del proceso de keep alive y el tiempo en minutos
   */
  public function getKeepAlive() {
    return $this->keepAlive;
  }

  /**
   * Establece un margen superior para el posicionamiento en caso de error
   *
   * @param integer $formMarginTop Desplazamiento en pixels
   */
  public function setMarginTop( $formMarginTop = 0 ) {
    $this->formMarginTop = $formMarginTop;
  }

  public function getMarginTop() {
    return $this->formMarginTop;
  }

  /**
   * Establece un margen superior para el posicionamiento en caso de error
   *
   * @param integer $formMarginTop Desplazamiento en pixels
   */
  public function setEnterSubmit( $enterSubmit = true ) {
    $this->enterSubmit = $enterSubmit;
  }

  public function getEnterSubmit() {
    return $this->enterSubmit;
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
    $data[ 'captchaUse' ] = $this->captchaUse;
    $data[ 'keepAlive' ] = $this->keepAlive;
    $data[ 'enterSubmit' ] = $this->enterSubmit;
    $data[ 'formMarginTop' ] = $this->formMarginTop;
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
    $this->captchaUse = $data[ 'captchaUse' ];
    $this->keepAlive = $data[ 'keepAlive' ];
    $this->enterSubmit = $data[ 'enterSubmit' ];
    $this->formMarginTop = $data[ 'formMarginTop' ];
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
      if( isset( $this->fields[ $fieldName ] ) ) {
        $data[ 'fields' ][ $fieldName ] = $this->fields[ $fieldName ];
      }
      else {
        if( isset( $data[ 'fields' ][ $fieldName ] ) ) {
          unset( $data[ 'fields' ][ $fieldName ] );
        }
      }
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
      $result = ( $this->getTokenId() === $tokenId );
    }
    else {
      error_log('FormController: ERROR. El FORM no esta en sesion: '.$formSessionId );
      // error_log('FormController: '. print_r( $_SESSION, true ) );
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
    // error_log('FormController: '. $postDataJson );
    if( $postDataJson !== false && mb_strpos( $postDataJson, '{' )===0 ) {
      $postData = json_decode( $postDataJson, true );
      // error_log('FormController: $postDataJson: '.print_r( $postData, true ) );

      // recuperamos FORM de sesion y añadimos los datos enviados
      if( isset( $postData[ 'cgIntFrmId' ][ 'value' ] ) && $this->loadPostSession( $postData[ 'cgIntFrmId' ][ 'value' ] ) ) {
        $this->loadPostValues( $postData );
        $result = true;
      }
    }

    if( $result === false ) {
      $this->addFormError( 'El servidor no ha podido recuperar los datos recibidos.', 'formError' );
    }

    return $result;
  }

  /**
    Recupera de sesion todos los datos importantes (serializados)
    @param array $formPost Datos enviados por el navegador convertidos a array
    @return boolean
   */
  public function loadPostSession( $tokenId ) {
    $result = false;

    if( $tokenId !== false ) {
      $result = $this->loadFromSession( $tokenId );
    }

    return $result;
  }

  /**
    Carga los valores de los input del navegador
    @param array $formPost Datos enviados por el navegador convertidos a array
   */
  public function loadPostValues( $formPost ) {
    // $this->postValues = $formPost;

    if( $this->captchaEnable() ) {
      $fieldName = 'g-recaptcha-response';
      if( isset( $formPost[ $fieldName ][ 'value' ] ) ) {
        $this->captchaResponse = $formPost[ $fieldName ][ 'value' ];
      }
    }

    // Importando los datos del form e integrando los datos de ficheros subidos
    foreach( $this->getFieldsNamesArray() as $fieldName ) {
      // error_log('FormController: Load field '.$fieldName.' value '.print_r( $formPost[ $fieldName ][ 'value' ], true ) );

      if( isset( $formPost[ $fieldName ][ 'dataInfo' ] ) ) {
        // error_log('FormController: DATA-VALUES: '.$fieldName.' '.print_r( $formPost[ $fieldName ][ 'dataInfo' ], true ) );
        // $this->setFieldParam( $fieldName, 'dataInfo', $formPost[ $fieldName ][ 'dataInfo' ] );
        $this->setFieldParam( $fieldName, 'dataInfo', $formPost[ $fieldName ][ 'dataInfo' ] );
        foreach( $formPost[ $fieldName ][ 'dataInfo' ] as $key => $value ) {
          // error_log('FormController: '. "setFieldParam: $fieldName, data-$key, $value )" );
          // $this->setFieldParam( $fieldName, 'data-'.$key, $value );
          $this->setFieldParam( $fieldName, $key, $value );
        }
      }
      if( isset( $formPost[ $fieldName ][ 'dataMultiInfo' ] ) ) {
        $this->setFieldParam( $fieldName, 'dataMultiInfo', $formPost[ $fieldName ][ 'dataMultiInfo' ] );
      }

      /*
        {
          "value":["67","69","71"],
          "dataMultiInfo":{
            "67":{"data-order":"1","data-term-icon":"","data-term-idname":"eduTIC"},
            "69":{"data-order":"2","data-term-icon":"","data-term-idname":"flipped","data-term-parent":"68"},
            "71":{"data-order":"3","data-term-icon":"","data-term-idname":"gamificacion","data-term-parent":"68"}
          }
        },
        {
          "value":false,
          "dataInfo":{"data-switchery":"true"}
        }
      */

      if( $this->getFieldType( $fieldName ) !== 'file' ) {
        if( isset( $formPost[ $fieldName ][ 'value' ] ) ) {
          $this->setFieldValue( $fieldName, $formPost[ $fieldName ][ 'value' ] );
        }
      }
      else {
        if( !$this->isEmptyFieldValue( $fieldName ) ) {
          $fileFieldValue = $this->getFieldValue( $fieldName );

          // error_log( 'FormController::loadPostValues FILE: '.print_r( $fileFieldValue, true ) );

          switch( $fileFieldValue['status'] ) {
            case 'LOAD':
            case 'REPLACE':
              $fileFieldValue['validate'] = $fileFieldValue['temp'];
              break;
            case 'EXIST':
              $fileFieldValue['validate'] = $fileFieldValue['prev'];
              break;
            case 'GROUP':
              if( count( $fileFieldValue['multiple'] ) ) {
                foreach( $fileFieldValue['multiple'] as $fileKey => $fileData ) {
                  switch( $fileData['status'] ) {
                    case 'LOAD':
                    case 'REPLACE':
                      $fileFieldValue['multiple'][ $fileKey ]['validate'] = $fileData['temp'];
                      break;
                    case 'EXIST':
                      $fileFieldValue['multiple'][ $fileKey ]['validate'] = $fileData['prev'];
                      break;
                  }
                  Cogumelo::debug('FormController: FE fileData temp name: '.json_encode( $fileFieldValue['multiple'][ $fileKey ] ) );
                }
              }
              break;
          }
          $this->setFieldValue( $fieldName, $fileFieldValue );
        }
      }
    }
  }

  /**
   * Carga los valores del VO
   *
   * @param VO $dataVO Datos cargados por el programa
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
   * Carga los valores desde un array en el que coincidan la clave con un campo
   *
   * @param array $dataArray Datos preparados para añadir al formulario
   */
  public function loadArrayValues( $dataArray ) {
    // error_log('FormController: loadArrayValues: ' . print_r( $dataArray, true ) );

    if( isset( $dataArray ) && is_array( $dataArray ) && count( $dataArray ) > 0  ) {
      foreach( $dataArray as $fieldName => $value ) {
        if( $this->isFieldDefined( $fieldName ) ) {
          $this->setFieldValue( $fieldName, $value );
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
    if( $params && is_array($params) && count($params)>0 ) {
      foreach( $params as $paramName => $value ) {
        $this->setFieldParam( $fieldName, $paramName, $value );
        // $this->fields[ $fieldName ][ $paramName ] = $value;
      }
    }
    if( empty( $this->fields[$fieldName]['type'] ) ) {
      $this->fields[$fieldName]['type'] = 'text';
    }

    // Creamos ya algunas reglas en funcion del tipo
    switch( $this->fields[$fieldName]['type'] ) {
      case 'select':
      case 'checkbox':
      case 'radio':
        if( isset( $this->fields[$fieldName]['options'] ) ) {
          $this->setValidationRule( $this->fields[$fieldName]['name'], 'inArray',
            array_keys( $this->fields[$fieldName]['options'] ) );
        }
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
    Elimina un campo del formulario
    @param string $fieldName Nombre del campo
   */
  public function removeField( $fieldName ) {
    $fieldNames = is_array( $fieldName ) ? $fieldName : array( $fieldName );
    foreach( $fieldNames as $fieldName ) {
      if( isset( $this->fields[ $fieldName ] ) ) {
        unset( $this->fields[ $fieldName ] );
        $this->updateFieldToSession( $fieldName );
      }
      if( isset( $this->rules[ $fieldName ] ) ) {
        unset( $this->rules[ $fieldName ] );
        $this->updateFieldRulesToSession( $fieldName );
      }
    }
  }














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
    // error_log('FormController: setFieldParam: ' . $paramName . ': ' . print_r( $value, true ) );

    if( array_key_exists( $fieldName, $this->fields) ) {
      $done = false;

      $fieldType = $this->getFieldType( $fieldName );

      if( $paramName === 'value' && $fieldType === 'file' ) {
        $this->fields[ $fieldName ]['value'] = $this->prepareFieldFileValue( $fieldName, $value );
        $done = true;
      }

      if( !$done ) {
        $this->fields[ $fieldName ][ $paramName ] = $value;
      }
    }
    else {
      error_log('FormController: Error intentando almacenar un parámetro ('.$paramName.') en un campo inexistente: '.$fieldName );
    }
  }

  /**
    Tratamiento especial para el parametro 'value' de los campos 'file' en setFieldParam()
    @param string $fieldName Nombre del campo
    @param mixed $fieldValue Valor del campo
   */
  private function prepareFieldFileValue( $fieldName, $fieldValue ) {
    // error_log('FormController: prepareFieldFileValue: ' . $fieldName );
    $newValue = null;

    if( !empty( $fieldValue ) && is_array( $fieldValue ) ) {
      // Carga inicial

      if( !$this->getFieldParam( $fieldName, 'multiple' ) ) {
        // Only one file
        // error_log('FormController: setFieldValue Basic: only one file' );
        // error_log('FormController: FILE fieldValue inicial: '. print_r( $fieldValue, true ) );
        if( !isset( $fieldValue[ 'status' ] ) ) {
          $this->setFieldParam( $fieldName, 'data-fm_id', isset( $fieldValue['id'] ) ? $fieldValue['id'] : '' );

          foreach( $this->multilangFieldNames( 'title' ) as $titleLang ) {
            /**
              TODO: Arreglar os null en texto
            */
            $this->setFieldParam( $fieldName, 'data-fm_'.$titleLang,
              (isset( $fieldValue[ $titleLang ] ) && $fieldValue[ $titleLang ] !== 'null') ? $fieldValue[ $titleLang ] : '' );
          }

          $fieldValue = array( 'status' => 'EXIST', 'prev' => $fieldValue, 'values' => array() );
        }
        else {
          foreach( $this->multilangFieldNames( 'title' ) as $titleLang ) {
            $fieldValue[ 'values' ][ $titleLang ] = $this->getFieldParam( $fieldName, 'data-fm_'.$titleLang );
          }
        }
      }
      else {
        // Multiple: add files
        // error_log('FormController: setFieldValue Multiple: add files' );
        // error_log('FormController: FILE fieldValue inicial: '. print_r( $fieldValue, true ) );



        if( !isset( $fieldValue['multiple'] ) ) {
          $fieldValue = [ 'multiple' => [ $fieldValue ] ];
        }

        $this->setFieldParam( $fieldName, 'data-fm_group_id', isset( $fieldValue['idGroup'] ) ? $fieldValue['idGroup'] : '' );

        // Este puede que se abandone
        $this->setFieldParam( $fieldName, 'data-fm_id', isset( $fieldValue['idGroup'] ) ? $fieldValue['idGroup'] : '' );

        $groupValue = [
          'status' => 'GROUP',
          'multiple' => [],
          'prev' => $fieldValue['multiple']
        ];

        if( isset( $fieldValue['idGroup'] ) ) {
          $groupValue['idGroup'] = $fieldValue['idGroup'];
        }

        foreach( $fieldValue['multiple'] as $key => $fileData ) {
          $fileId = isset( $fileData['id'] ) ? 'FID_'.$fileData['id'] : $key;
          if( !isset( $fileData['status'] ) ) {
            $fileData = [ 'status' => 'EXIST', 'prev' => $fileData ];
          }
          $groupValue['multiple'][ $fileId ] = $fileData;
        }
        $fieldValue = $groupValue;
      }

      // error_log('FormController: ----------------------------------------------------------' );
      // error_log('FormController: FILE fieldValue FINAL: '. json_encode( $fieldValue ) );
      // error_log('FormController: ----------------------------------------------------------' );
      $newValue = $fieldValue;
    }

    return $newValue;
  }

  /**
    Elimina un parametro de un campo
    @param string $fieldName Nombre del campo
    @param string $paramName Nombre del parametro
   */
  public function removeFieldParam( $fieldName, $paramName ) {
    if( isset( $this->fields[ $fieldName ][ $paramName ] ) ) {
      unset( $this->fields[ $fieldName ][ $paramName ] );
      $this->updateFieldToSession( $fieldName );
    }
  }











  /**
   * Establece un parametro interno en un campo
   *
   * @param string $fieldName Nombre del campo
   * @param string $paramName Nombre del parametro
   * @param mixed $value Valor del parametro
   */
  public function setFieldInternal( $fieldName, $paramName, $value ) {
    if( array_key_exists( $fieldName, $this->fields ) ) {
      $this->fields[ $fieldName ][ 'cgIntFrmFieldInfo' ][ $paramName ] = $value;
    }
    else {
      error_log('FormController: Error intentando almacenar un parámetro ('.$paramName.') en un campo inexistente: '.$fieldName );
    }
  }

  /**
   * Crea los campos y les asigna las reglas en form
   *
   * @param $definitions Array fields info
   */
  public function definitionsToForm( $definitions ) {
    $numLangs = count( $this->langAvailable );
    foreach( $definitions as $fieldName => $definition ) {
      if( !isset( $definition['params'] ) ) {
        $definition['params'] = false;
      }
      if( isset( $definition['translate'] ) && $definition['translate'] === true ) {
        $baseClass = '';
        if( isset( $definition['params']['class'] ) &&  $definition['params']['class'] !== '' ) {
          $baseClass = $definition['params']['class'];
        }
        foreach( $this->langAvailable as $lang ) {
          if( $numLangs > 1 ) {
            $definition['params']['class'] = $baseClass . ' js-tr js-tr-sw js-tr-'.$lang;
          }
          else {
            $definition['params']['class'] = $baseClass . ' js-tr js-tr-'.$lang;
          }
          $this->setField( $fieldName.'_'.$lang, $definition['params'] );
          if( isset( $definition['rules'] ) ) {
            foreach( $definition['rules'] as $ruleName => $ruleParams ) {
              $this->setValidationRule( $fieldName.'_'.$lang, $ruleName, $ruleParams );
            }
          }
        }
      }
      else {
        $this->setField( $fieldName, $definition['params'] );
        if( isset( $definition['rules'] ) ) {
          foreach( $definition['rules'] as $ruleName => $ruleParams ) {
            $this->setValidationRule( $fieldName, $ruleName, $ruleParams );
          }
        }
      }
    }
  }

  /**
   * Crea un array con los nombre del los campos para cada idioma
   *
   * @param string|array $fieldName Nombre del campo
   *
   * @return array
   */
  public function multilangFieldNames( $fieldName ) {
    $newFieldNames = array();

    $originalFieldNames = is_array( $fieldName ) ? $fieldName : array( $fieldName );


    if( $this->langAvailable === false || count( $this->langAvailable ) < 1 ) {
      $newFieldNames[] = $originalFieldNames;
    }
    else {
      foreach( $originalFieldNames as $fieldName ) {
        foreach( $this->langAvailable as $lang ) {
          $newFieldNames[] = $fieldName.'_'.$lang;
        }
      }
    }

    return $newFieldNames;
  }

  /**
   * Clona un campo del formulario, sus parametros y reglas.
   *
   * @param string $fieldName Nombre del campo origen
   * @param string $newFieldName Nombre del campo clonado
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
   * Recupera los contenidos no internos de un campo del formulario
   *
   * @param string $fieldName Nombre del campo
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
   * Recupera el valor de un campo
   *
   * @param string $fieldName Nombre del campo
   *
   * @return mixed
   */
  public function getFieldValue( $fieldName ) {

    return $this->getFieldParam( $fieldName, 'value' );
  }

  /**
    Cambia valores vacios por null
    @param array $fieldNames Nombres de los campos
   */
  public function emptyValuesToNull( $fieldNames ) {
    foreach( $$fieldNames as $fieldName ) {
      if( $this->getFieldValue( $fieldName ) === '' ) {
        $this->setFieldValue( $fieldName, null );
      }
    }
  }

  /**
    Recupera un parametro de un campo
    @param string $fieldName Nombre del campo
    @param string $paramName Nombre del parametro
    @return mixed
   */
  public function getFieldParam( $fieldName, $paramName ) {
    $value = null;

    if( isset( $this->fields[ $fieldName ][ $paramName ] ) ) {
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
    * Recupera los nombres de todos los campos
    *
    * @return TYPE
   */
  public function getFieldsNamesArray() {

    return array_keys( $this->fields );
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

        // error_log('FormController: '. $fieldName .' === '. print_r( $fieldsValuesArray[ $fieldName ], true ) );
      }
      else {
        error_log('FormController: getValuesArray: '.$fieldName .' IGNORADO!!! ' );
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
        // error_log('FormController: '. $fieldName .' === '. print_r( $valuesArray[ 'fields' ][ $fieldName ], true ) );
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
          // error_log('FormController: '. $groupName.'/'.$idElem.'/'.$fieldName .' === '.print_r( $fieldsValuesArray[ $idElem ][ $fieldName ], true ) );
        }
        else {
          error_log('FormController: getValuesGroupArray: '.$groupName.'/'.$idElem.'/'.$fieldName .' IGNORADO!!! ' );
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









  /**********************************************************************/
  /***  Grupos                                                        ***/
  /**********************************************************************/

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

  /**********************************************************************/
  /***  Grupos (FIN)                                                  ***/
  /**********************************************************************/




  /**********************************************************************/
  /***  Ficheros                                                      ***/
  /**********************************************************************/

  /**
    Procesa los ficheros temporales del form para colocarlos en su lugar definitivo y registrarlos
    @return boolean
   */
  public function processFileFields( $fieldNames = false ) {
    // error_log('FormController: ---------------' );
    // error_log('FormController: processFileFields(fieldNames): ' . json_encode( $fieldNames ) );
    // error_log('FormController: ---------------' );

    $result = true;

    if( $fieldNames === false ) {
      $fieldNames = $this->getFieldsNamesArray();
    }

    foreach( $fieldNames as $fieldName ) {
      if( $result && $this->getFieldType( $fieldName ) === 'file' ) {
        Cogumelo::debug('FormController: processFileFields: Almacenando fileField: '.$fieldName );

        $fileFieldValue = $this->getFieldValue( $fieldName );
        // error_log('FormController: '. print_r( $fileFieldValue, true ) );

        if( !$this->getFieldParam( $fieldName, 'multiple' ) ) {
          // Basic: only one file
          Cogumelo::debug('FormController: processFileFields Basic: only one file' );
          // error_log('FormController: FILE fileFieldValue inicial: '. print_r( $fileFieldValue, true ) );

          if( isset( $fileFieldValue['status'] ) && $fileFieldValue['status'] !== false ) {
            switch( $fileFieldValue['status'] ) {
              case 'LOAD':
                Cogumelo::debug('FormController: processFileFields: LOAD' );
                $fileFieldValue['status'] = 'LOADED';
                $fileFieldValue['values'] = $fileFieldValue['validate'];
                $fileFieldValue['values']['destDir'] = $this->getFieldParam( $fieldName, 'destDir' );
                $this->setFieldValue( $fieldName, $fileFieldValue );
                $this->updateFieldToSession( $fieldName );
                Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . json_encode( $fileFieldValue ) );
                /*
                  $fileName = $this->secureFileName( $fileFieldValue['validate']['originalName'] );
                  $destDir = $this->getFieldParam( $fieldName, 'destDir' );
                  // QUITAR FILES_APP_PATH
                  $fullDestPath = self::FILES_APP_PATH . $destDir;
                  if( !is_dir( $fullDestPath ) ) {
                    // TODO: CAMBIAR PERMISOS 0777
                    if( !mkdir( $fullDestPath, 0777, true ) ) {
                      $result = false;
                      $this->addFieldRuleError( $fieldName, 'cogumelo',
                        'La subida del fichero ha fallado. (MD)' );
                      error_log('FormController: Imposible crear el directorio necesario. ' . $fullDestPath );
                    }
                  }

                  if( !$this->existErrors() ) {
                    // TODO: DETECTAR Y SOLUCIONAR COLISIONES!!!
                    Cogumelo::debug('FormController: Movendo ' . $fileFieldValue['validate']['absLocation'] . ' a ' . $fullDestPath.'/'.$fileName );
                    if( !rename( $fileFieldValue['validate']['absLocation'], $fullDestPath.'/'.$fileName ) ) {
                      $result = false;
                      $this->addFieldRuleError( $fieldName, 'cogumelo',
                        'La subida del fichero ha fallado. (MF)' );
                      error_log('FormController: Imposible mover el fichero al directorio adecuado.' .
                        $fileFieldValue['validate']['absLocation'] . ' a ' . $fullDestPath.'/'.$fileName );
                    }
                  }

                  if( !$this->existErrors() ) {
                    $fileFieldValue['status'] = 'LOADED';
                    $fileFieldValue['values'] = $fileFieldValue['validate'];
                    $fileFieldValue['values']['absLocation'] = $destDir.'/'.$fileName;
                    $this->setFieldValue( $fieldName, $fileFieldValue );
                    $this->updateFieldToSession( $fieldName );
                    Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . print_r( $fileFieldValue, true ) );
                  }
                */
                break;
              case 'REPLACE':
                Cogumelo::debug('FormController: processFileFields: REPLACE' );
                $fileFieldValue['values'] = $fileFieldValue['validate'];
                $fileFieldValue['values']['destDir'] = $this->getFieldParam( $fieldName, 'destDir' );
                $this->setFieldValue( $fieldName, $fileFieldValue );
                $this->updateFieldToSession( $fieldName );
                Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . json_encode( $fileFieldValue ) );
                /*
                  $fileName = $this->secureFileName( $fileFieldValue['validate']['originalName'] );
                  $destDir = $this->getFieldParam( $fieldName, 'destDir' );
                  // QUITAR FILES_APP_PATH
                  $fullDestPath = self::FILES_APP_PATH . $destDir;
                  if( !is_dir( $fullDestPath ) ) {
                    // TODO: CAMBIAR PERMISOS 0777
                    if( !mkdir( $fullDestPath, 0777, true ) ) {
                      $result = false;
                      $this->addFieldRuleError( $fieldName, 'cogumelo',
                        'La subida del fichero ha fallado. (MD)' );
                      error_log('FormController: Imposible crear el directorio necesario. ' . $fullDestPath );
                    }
                  }

                  if( !$this->existErrors() ) {
                    // TODO: DETECTAR Y SOLUCIONAR COLISIONES!!!
                    Cogumelo::debug('FormController: Movendo ' . $fileFieldValue['validate']['absLocation'] . ' a ' . $fullDestPath.'/'.$fileName );
                    if( !rename( $fileFieldValue['validate']['absLocation'], $fullDestPath.'/'.$fileName ) ) {
                      $result = false;
                      $this->addFieldRuleError( $fieldName, 'cogumelo',
                        'La subida del fichero ha fallado. (MF)' );
                      error_log('FormController: Imposible mover el fichero al directorio adecuado.' .
                        $fileFieldValue['validate']['absLocation'] . ' a ' . $fullDestPath.'/'.$fileName );
                    }
                  }

                  if( !$this->existErrors() ) {
                    //$fileFieldValue['status'] = 'LOADED';
                    $fileFieldValue['values'] = $fileFieldValue['validate'];
                    $fileFieldValue['values']['absLocation'] = $destDir.'/'.$fileName;
                    $this->setFieldValue( $fieldName, $fileFieldValue );
                    $this->updateFieldToSession( $fieldName );
                    Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . print_r( $fileFieldValue, true ) );
                  }
                */
                break;
              case 'DELETE':
                Cogumelo::debug('FormController: processFileFields: DELETE' );
                // TODO: EJECUTAR LOS PASOS PARA EL ESTADO DELETE!!!
                $fileFieldValue['values'] = $fileFieldValue['prev'];
                $this->setFieldValue( $fieldName, $fileFieldValue );
                $this->updateFieldToSession( $fieldName );
                Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . json_encode( $fileFieldValue ) );
                break;
              case 'EXIST':
                Cogumelo::debug('FormController: processFileFields OK: EXIST - NADA QUE HACER' );
                $fileFieldValue['values'] = $fileFieldValue['prev'];
                $this->setFieldValue( $fieldName, $fileFieldValue );
                $this->updateFieldToSession( $fieldName );
                Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . json_encode( $fileFieldValue ) );
                break;
            }
          } // if( isset( $fileFieldValue['status'] ) && $fileFieldValue['status'] !== false )
        }// Basic: only one file
        else {
          // Multiple: add files
          Cogumelo::debug('FormController: processFileFields Multiple: add files' );
          Cogumelo::debug('FormController: FILE fileFieldValue inicial: '. json_encode( $fileFieldValue ) );

          if( !empty( $fileFieldValue['multiple'] ) ) {
            foreach( $fileFieldValue['multiple'] as $fileKey => $fileData ) {

              if( isset( $fileData['status'] ) && $fileData['status'] !== false ) {
                switch( $fileData['status'] ) {
                  case 'LOAD':
                    Cogumelo::debug('FormController: processFileFields: LOAD' );
                    $fileData['status'] = 'LOADED';
                    $fileData['values'] = $fileData['validate'];
                    $fileData['values']['destDir'] = $this->getFieldParam( $fieldName, 'destDir' );
                    Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . json_encode( $fileData ) );
                    break;
                  case 'REPLACE':
                    Cogumelo::debug('FormController: processFileFields: REPLACE' );
                    $fileData['values'] = $fileData['validate'];
                    $fileData['values']['destDir'] = $this->getFieldParam( $fieldName, 'destDir' );
                    Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . json_encode( $fileData ) );
                    break;
                  case 'DELETE':
                    Cogumelo::debug('FormController: processFileFields: DELETE' );
                    // TODO: EJECUTAR LOS PASOS PARA EL ESTADO DELETE!!!
                    $fileData['values'] = $fileData['prev'];
                    Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . json_encode( $fileData ) );
                    break;
                  case 'EXIST':
                    Cogumelo::debug('FormController: processFileFields OK: EXIST - NADA QUE HACER' );
                    $fileData['values'] = $fileData['prev'];
                    Cogumelo::debug('FormController: Info: processFileFields OK. values: ' . json_encode( $fileData ) );
                    break;
                }
              } // if( isset( $fileData['status'] ) && $fileData['status'] !== false )

              $fileFieldValue['multiple'][ $fileKey ] = $fileData;
            } // foreach
            $this->setFieldValue( $fieldName, $fileFieldValue );
            $this->updateFieldToSession( $fieldName );
          } // if !empty $fileFieldValue['multiple']

        } // Multiple: add files

        // TODO: FALTA GUARDA LOS DATOS DEFINITIVOS DEL FICHERO!!!
        // En caso de fallo $result = false;

      } // if( $result && $this->getFieldType( $fieldName ) === 'file' )
    } // foreach( $this->getFieldsNamesArray() as $fieldName )

    if( !$result ) {
      // Si algo ha fallado, desacemos los cambios
      $this->revertFileFieldsLoaded();
    }

    // error_log('FormController: ---------------' );
    // error_log('FormController: processFileFields - FIN' );
    // error_log('FormController: ---------------' );
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
        // error_log('FormController: FILE: Revertindo LOADED fileField: '.$fieldName );

        $fileFieldValue = $this->getFieldValue( $fieldName );
        // error_log('FormController: '. print_r( $fileFieldValue, true ) );

        if( isset( $fileFieldValue['status'] ) && $fileFieldValue['status'] === 'LOADED' ) {
          $fileFieldValue['status'] = 'LOAD';
          unset( $fileFieldValue['values'] );
          $this->setFieldValue( $fieldName, $fileFieldValue );
          $this->updateFieldToSession( $fieldName );
          // error_log('FormController: Info: revertFileFieldsLoaded OK. values: ' . print_r( $fileFieldValue, true ) );
          /*
            // QUITAR FILES_APP_PATH
            $absLocationActual = self::FILES_APP_PATH . $fileFieldValue['values']['absLocation'];
            $absLocationAnterior = $fileFieldValue['validate']['absLocation'];
            Cogumelo::debug('FormController: Devolvendo ' . $absLocationActual .' a '. $absLocationAnterior );
            if( !rename( $absLocationActual, $absLocationAnterior ) ) {
              $result = false;
              $this->addFieldRuleError( $fieldName, 'cogumelo',
                'La subida del fichero ha fallado. (MR)' );
              error_log('FormController: Imposible devolver el fichero al directorio adecuado.' . $absLocationActual .' a '. $absLocationAnterior );
            }
            else {
              $fileFieldValue['status'] = 'LOAD';
              unset( $fileFieldValue['values'] );
              $this->setFieldValue( $fieldName, $fileFieldValue );
              $this->updateFieldToSession( $fieldName );
              Cogumelo::debug('FormController: Info: revertFileFieldsLoaded OK. values: ' . print_r( $fileFieldValue, true ) );
            }
          */
        } // if( isset( $fileFieldValue['status'] ) && $fileFieldValue['status'] === 'LOADED' )
      } // if( $this->getFieldType( $fieldName ) === 'file' )
    } // foreach( $this->getFieldsNamesArray() as $fieldName )

    return $result;
  } // function revertFileFieldsLoaded










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
    Mover con seguridad un fichero del tmp de PHP al tmp de nuestra aplicacion
    @param string $fileTmpLoc Fichero temporal de PHP
    @param string $fileName Nombre del fichero
    @return string Fichero temporal de la App. En caso de error: false
   */
  public function tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName, $fieldName ) {
    // error_log('FormController: tmpPhpFile2tmpFormFile: '.$fileTmpLoc.' --- '.$fileName.' --- '.$fieldName);
    $result = false;
    $error = false;

    $tmpCgmlFormPath = $this->tmpPath .'/'.
      preg_replace( '/[^0-9a-z_\.-]/i', '_', $this->getTokenId() ).
      '-'.$fieldName;
    if( !is_dir( $tmpCgmlFormPath ) ) {
      /**
       * TODO: CAMBIAR PERMISOS 0777
       */
      if( !mkdir( $tmpCgmlFormPath, 0777, true ) ) {
        $error = 'tmpPhpFile2tmpFormFile: Imposible crear el dir. necesario: '.$tmpCgmlFormPath;
        error_log('FormController: '.$error);
      }
    }

    if( !$error ) {
      $secureName = $this->secureFileName( $fileName );

      $tmpLocationCgml = $tmpCgmlFormPath .'/'. $secureName;
      /**
       * TODO: FALTA VER QUE NON SE PISE UN ANTERIOR!!!
       */
      if( !move_uploaded_file( $fileTmpLoc, $tmpLocationCgml ) ) {
        $error = 'tmpPhpFile2tmpFormFile: Fallo de move_uploaded_file pasando ('.$fileTmpLoc.') a ('.$tmpLocationCgml.')';
        error_log('FormController: '.$error);
      }
      else {
        $result = $tmpLocationCgml;
      }
    }

    // error_log('FormController: tmpPhpFile2tmpFormFile ERROR: '.$error );
    // error_log('FormController: tmpPhpFile2tmpFormFile RET: '.$result );

    return $result;
  } // function tmpPhpFile2tmpFormFile( $fileTmpLoc, $fileName )

  /**
    Crea un nombre de fichero seguro a partir del nombre de fichero deseado
    @param string $fileName Nombre del campo
    @return string
   */
  public function secureFileName( $fileName ) {
    // error_log('FormController: secureFileName: '.$fileName );
    $maxLength = 200;

    // "Aplanamos" caracteres no ASCII7
    $fileName = str_replace( $this->replaceAcents[ 'from' ], $this->replaceAcents[ 'to' ], $fileName );
    // Solo admintimos a-z A-Z 0-9 - / El resto pasan a ser -
    $fileName = preg_replace( '/[^a-z0-9_\-\.]/iu', '_', $fileName );
    // Eliminamos - sobrantes
    $fileName = preg_replace( '/__+/u', '_', $fileName );
    $fileName = trim( $fileName, '_' );

    $sobran = mb_strlen( $fileName, 'UTF-8' ) - $maxLength;
    if( $sobran < 0 ) {
      $sobran = 0;
    }

    $tmpExtPos = mb_strrpos( $fileName, '.' );
    if( $tmpExtPos > 0 && ( $tmpExtPos - $sobran ) >= 8 ) {
      // Si hay extensión y al cortar el nombre quedan 8 o más letras, recorto solo el nombre
      $tmpName = mb_substr( $fileName, 0, $tmpExtPos - $sobran );
      $tmpExt = mb_substr( $fileName, 1 + $tmpExtPos );
      $fileName = $tmpName . '.' . $tmpExt;
    }
    else {
      // Recote por el final
      $fileName = mb_substr( $fileName, 0, $maxLength );
    }

    // error_log('FormController: secureFileName RET: '.$fileName );

    return $fileName;
  }

  /**********************************************************************/
  /***  Ficheros (FIN)                                                ***/
  /**********************************************************************/





  /**********************************************************************/
  /***  Captcha (INI)                                                 ***/
  /**********************************************************************/


  public function captchaEnable( $status = null ) {
    if( $status !== null && Cogumelo::getSetupValue('google:recaptcha:key:site') ) {
      $this->captchaUse = ( $status ) ? true : false;
    }

    return $this->captchaUse;
  }

  public function getCaptchaResponse() {

    return $this->captchaResponse;
  }

  public function captchaValidate() {
    $validate = false;

    $secret = Cogumelo::getSetupValue('google:recaptcha:key:secret');
    $response = $this->getCaptchaResponse();

    if( $this->captchaEnable() && $response && $secret ) {
      $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?'.
        'secret='.$secret.'&response='.$response;
      $jsonResponse = file_get_contents( $verifyUrl );
      // error_log('FormController: $jsonResponse: '.$jsonResponse );
      $response = ( $jsonResponse ) ? json_decode( $jsonResponse ) : false;
      $validate = ( $response && $response->success ) ? true : false;
    }

    return $validate;
  }


  /**********************************************************************/
  /***  Captcha (FIN)                                                 ***/
  /**********************************************************************/





  /**********************************************************************/
  /***  HTML y JS                                                     ***/
  /**********************************************************************/

  /**
    Recupera el html y js que forman el form
    @return string
   */
  public function getHtmlForm() {
    $html='';

    $html .= $this->getHtmlOpen()."\n";
    $html .= $this->getHtmlFieldsAndGroups()."\n";
    $html .= $this->getHtmlClose()."\n";

    $html .= $this->getScriptCode()."\n";

    return $html;
  }

  // Alias por un error de nombre
  public function getHtmpOpen() {
    return $this->getHtmlOpen();
  }
  /**
    Recupera el html de la apertura del form
    @return string
   */
  public function getHtmlOpen() {
    $html='';

    $html .= '<form name="'.$this->getName().'" id="'.$this->id.'" data-token_id="'.$this->getTokenId().'" ';
    $html .= ' class="'.$this->cssPrefix.' '.$this->cssPrefix.'-form-'.$this->getName().'" ';
    $html .= ' action="javascript:void(0);"';
    if( $this->action ) {
      $html .= ' data-form-action="'.$this->action.'"';
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
      $html = '<div class="'.$this->cssPrefix.'-wrap '.$this->cssPrefix.'-group-wrap '.$this->cssPrefix.'-group-'.$groupName.'">'."\n";
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
        $html .= '<div data-form_id="'.$this->id.'" class="addGroupElement '.
          $this->cssPrefix.'-group-'.$groupName.'" groupName="'.$groupName.'">MAS</div>'."\n";
        $html .= '<div class="JQVMC-group-'.$groupName.'"></div>'."\n";
      }

      $html .= '</div><!-- /'.$this->cssPrefix.'-group-'.$groupName.' -->';
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

      $html .= '<div class="'.$this->cssPrefix.'-wrap '.$this->cssPrefix.'-groupElem'.
        ( $idElem !== false ? ' '.$this->cssPrefix.'-groupElem_C_'.$idElem : '' ).
        '">'."\n";

      $html .= implode( "\n", $this->getHtmlFieldsArray( $groupFieldNames ) )."\n";

      if( $idElem !== false ) {
        $html .= '<div data-form_id="'.$this->id.'" class="removeGroupElement '.$this->cssPrefix.'-group-'.$groupName.'" '.
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
    if( is_array( $fieldNames ) && count($fieldNames) > 0 ) {
      foreach( $fieldNames as $fieldName ) {
        if( $this->getFieldInternal( $fieldName, 'groupCloneRoot' ) !== true ) {
          // Procesamos los campos que no son raiz de campos agrupados
          $htmlField = $this->getHtmlField( $fieldName );
          if( $htmlField !== '' ) {
            $html[ $fieldName ] = '<div class="'.$this->cssPrefix.'-wrap '.$this->cssPrefix.'-field-'.$fieldName.
              ( $this->getFieldType( $fieldName ) === 'file' ? ' '.$this->cssPrefix.'-fileField ' : '' ).
              '">'.$htmlField.'</div>';
          }
        }
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

    if( count( $htmlFieldArray ) > 0 ) {
      if( isset( $htmlFieldArray['label'] ) ) {
        $html .= $htmlFieldArray['label']."\n";
      }
      if( isset( $htmlFieldArray['htmlBefore'] ) ) {
        $html .= $htmlFieldArray['htmlBefore']."\n";
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
            $html .= '<label class="labelOption">'.$inputAndText['input'].'<span class="labelText">'.
              $inputAndText['text'].'</span></label>';
          }
          $html .= '<span class="JQVMC-'.$fieldName.'-error JQVMC-error"></span>';
          break;
        case 'textarea':
          $html .= $htmlFieldArray['inputOpen'] . $htmlFieldArray['value'] . $htmlFieldArray['inputClose'];
          break;
        case 'reserved':
          $html = '';
          break;
        default:
          $html .= $htmlFieldArray['input'];
          break;
      }
      if( isset( $htmlFieldArray['htmlAfter'] ) ) {
        $html .= $htmlFieldArray['htmlAfter']."\n";
      }
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

    if( $field = $this->getField( $fieldName ) ) {

      $cloneOf = $this->getFieldInternal( $fieldName, 'cloneOf' );
      $groupName = $this->getFieldInternal( $fieldName, 'groupName' );

      $myFielId = isset( $field['id'] ) ? $field['id'] : false;

      //$myFielId = ( $myFielId === false && isset( $field['htmlEditor'] ) ) ? $fieldName : false;
      if( !empty( $field['htmlEditor'] ) ) {
        $this->htmlEditor = true;
        if( $myFielId === false ) {
          $myFielId = $fieldName;
        }
      }

      $html['fieldType'] = $field['type'];

      if( isset( $field['label'] ) ) {
        $html['label'] = '<label';
        $html['label'] .= ( $myFielId ? ' for="'.$myFielId.'"' : '' );
        $html['label'] .= ' class="'.$this->cssPrefix.( isset( $field['class'] ) ? ' '.$field['class'] : '' ).'"';
        $html['label'] .= isset( $field['style'] ) ? ' style="'.$field['style'].'"' : '';
        $html['label'] .= '>'.$field['label'].'</label>';
      }

      if( isset( $field['htmlBefore'] ) ) {
        $html['htmlBefore'] = '<div class="'.$this->cssPrefix.'before">'.$field['htmlBefore'].'</div>';
      }
      if( isset( $field['htmlAfter'] ) ) {
        $html['htmlAfter'] = '<div class="'.$this->cssPrefix.'after">'.$field['htmlAfter'].'</div>';
      }

      $attribs = ' form="'.$this->id.'"';
      $attribs .= ( $myFielId ? ' id="'.$myFielId.'"' : '' );
      $attribs .= ' class="'.$this->cssPrefix.'-field '.$this->cssPrefix.'-field-'.$fieldName.
        ( ( $field['type'] === 'file' ) ? ' '.$this->cssPrefix.'-fileField' : '' ).
        ( $cloneOf ? ' '.$this->cssPrefix.'-cloneOf-'.$cloneOf : '' ).
        ( $groupName ? ' '.$this->cssPrefix.'-group-'.$groupName : '' ).
        ( !empty( $field['htmlEditor'] ) ? ' '.$this->cssPrefix.'-htmlEditor' : '' ).
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
      $attribs .= !empty( $field['htmlEditor'] ) ? ' contenteditable="true"' : '';

      $r = $this->getValidationRules( $fieldName );
      $attribs .= isset( $r['maxlength'] ) ? ' maxlength="'.$r['maxlength'].'"' : '';

      foreach( $field as $dataKey => $dataValue ) {
        if( mb_strpos( $dataKey, 'data-' ) === 0 ) {
          $attribs .= ' '.$dataKey.'="'.$dataValue.'"';
        }
      }

      switch( $field['type'] ) {
        case 'select':
          $html['inputOpen'] = '<select name="'.$fieldName.'"'. $attribs.'>';

          $html['options'] = array();
          if( isset($field['options']) && is_array($field['options']) && count($field['options'])>0 ) {
            foreach( $field['options'] as $val => $text ) {
              if( is_array( $text ) && isset( $text['value'] ) ) {
                $optInfo = $text;
                $optInput = '<option value="'. htmlspecialchars( $optInfo[ 'value' ] ) .'"';
                if( isset( $optInfo[ 'disabled' ] ) && $optInfo[ 'disabled' ] ) {
                  $optInput .= ' disabled';
                }
                foreach( $optInfo as $dataKey => $dataValue ) {
                  if( mb_strpos( $dataKey, 'data-' ) === 0 ) {
                    $optInput .= ' '.$dataKey.'="'.$dataValue.'"';
                  }
                }
                $optInput .= '>' . $optInfo[ 'text' ] . '</option>';
                $html[ 'options' ][ $optInfo[ 'value' ] ] = array(
                  'input' => $optInput,
                  'text' => $optInfo[ 'text' ]
                );
              }
              else {
                $html['options'][$val] = array(
                  'input' => '<option value="'.htmlspecialchars( $val ).'">'.$text.'</option>',
                  'text' => $text
                );
              }
            }
            // Colocamos los selected
            if( isset( $field['value'] ) ) {
              $values = is_array( $field['value'] ) ? $field['value'] : array( $field['value'] );
              $dataOrder = 1;
              foreach( $values as $val ) {
                if( isset( $html['options'][$val]['input'] ) ) {
                  $html['options'][$val]['input'] = str_replace(
                    'option value="'.htmlspecialchars( $val ).'"',
                    'option data-order="'.$dataOrder.'" value="'.htmlspecialchars( $val ).'" selected="selected"',
                    $html['options'][$val]['input'] );
                  $dataOrder++;
                  if( !isset( $field['multiple'] ) ) {
                    break; // Si no es multiple, solo puede tener 1 valor
                  }
                }
              }
            }
          }

          $html['inputClose'] = '</select>'; // Comentario eliminado (petaba)
          break;

        case 'checkbox':
        case 'radio':
          $html['options'] = array();
          if( isset($field['options']) && is_array($field['options']) && count($field['options'])>0 ) {
            foreach( $field['options'] as $val => $text ) {
              if( is_array( $text ) && isset( $text['value'] ) ) {
                $infoArray = $text; // Cambio de nombre para no confundir
                $html['options'][$val] = array();
                $html['options'][$val]['text'] = $infoArray['text'];
                $html['options'][$val]['label'] = empty( $infoArray['label'] ) ? '' : '<label>'.$infoArray['label'].'</label>';

                $html['options'][$val]['input'] = '<input name="'.$fieldName.'"'.
                  ' value="'.htmlspecialchars( $infoArray['value'] ).'"'.
                  ' type="'.$field['type'].'"'.$attribs;
                foreach( $infoArray as $dataKey => $dataValue ) {
                  if( mb_strpos( $dataKey, 'data-' ) === 0 ) {
                    $html['options'][$val]['input'] .= ' '.$dataKey.'="'.$dataValue.'"';
                  }
                }
                $html['options'][$val]['input'] .= '>';
              }
              else {
                $html['options'][$val] = array();
                $html['options'][$val]['input'] = '<input name="'.$fieldName.'"'.
                  ' value="'.htmlspecialchars( $val ).'"'.
                  ' type="'.$field['type'].'"'.$attribs.'>';
                $html['options'][$val]['text'] = $text;
                $html['options'][$val]['label'] = $text!='' ? '<label>'.$text.'</label>' : '';
              }
            }
          }
          // Colocamos los checked
          if( isset( $field['value'] ) ) {
            $values = is_array( $field['value'] ) ? $field['value'] : array( $field['value'] );
            foreach( $values as $val ) {
              if( isset( $html['options'][$val]['input'] ) ) {
                $html['options'][$val]['input'] = str_replace(
                  'name="'.$fieldName.'" value="'.htmlspecialchars( $val ).'"',
                  'name="'.$fieldName.'" value="'.htmlspecialchars( $val ).'" checked="checked"',
                  $html['options'][$val]['input'] );
                if( $field['type']=='radio' ) {
                  break; // Radio solo puede tener 1 valor
                }
              }
            }
          }
          break;

        case 'textarea':
          $html['inputOpen'] = '<textarea name="'.$fieldName.'"'.$attribs.'>';
          $html['value'] = isset( $field['value'] ) ? htmlspecialchars( $field['value'] ) : '';
          $html['inputClose'] = '</textarea>';
          break;

        case 'file':
          $html['input'] = '<input name="'.$fieldName.'"';
          // $html['input'] .= isset( $field['value'] ) ? ' value="'.$field['value'].'"' : '';
          $html['input'] .= ' type="'.$field['type'].'"'.$attribs.'>';
          // error_log('FormController: FILE --- '.print_r( $field, true ) );
          break;

        case 'reserved':
          $html = array();
          break;

        default:
          // button, hidden, password, range, text, color, date, datetime, datetime-local,
          // email, image, month, number, search, tel, time, url, week
          $html['input'] = '<input name="'.$fieldName.'"';
          $html['input'] .= isset( $field['value'] ) ? ' value="'.htmlspecialchars( $field['value'] ).'"' : '';
          $html['input'] .= ' type="'.$field['type'].'"'.$attribs.'>';
          break;
      }
    } // if( $field = $this->getField( $fieldName ) )
    else {
      Cogumelo::debug( 'form->getHtmlFieldArray Error: No existe '.$fieldName );
    }

    return $html;
  } // function getHtmlFieldArray

  /**
    Recupera el html del Captcha del form
    @return string
   */
  public function getHtmlCaptcha() {
    $html = '';

    $keySite = Cogumelo::getSetupValue('google:recaptcha:key:site');

    if( $this->captchaEnable() ) {
      $html = '<div class="'.$this->cssPrefix.'-wrap '.$this->cssPrefix.'-captchaField g-recaptcha" '.
        ' form="'.$this->getId().'" data-sitekey="'.$keySite.'"></div>';
    }

    return $html;
  }

  /**
    Recupera el html del cierre del form
    @return string
   */
  public function getHtmlClose() {
    $html = '</form><!-- '.$this->getName().' -->';

    return $html;
  }

  /**
    Recupera el html con el JS del form
    @return string
   */
  public function getScriptCode() {
    $html = '';

    // js controler V2

    $scRules = ( count( $this->rules ) > 0 ) ? json_encode( $this->rules ) : 'false';
    $scMsgs = ( count( $this->messages ) > 0 ) ? json_encode( $this->messages ) : 'false';

    $opt = [];
    if( $this->getKeepAlive() ) {
      $opt['keepAliveTime'] = $this->getKeepAlive();
    }
    if( $this->getMarginTop() ) {
      $opt['marginTop'] = $this->getMarginTop();
    }
    if( $this->htmlEditor ) {
      form::loadDependence( 'ckeditor' );
      $opt['htmlEditor'] = true;
    }
    if( $this->getEnterSubmit() !== null ) {
      $opt['enterSubmit'] = $this->getEnterSubmit();
    }

    $formOptions = json_encode( $opt );


    $jsFileGroups = [];
    foreach( $this->getFieldsNamesArray() as $fieldName ) {
      if( $this->getFieldParam( $fieldName, 'type' ) === 'file' && $this->getFieldParam( $fieldName, 'multiple' ) ) {
        $value = $this->getFieldValue( $fieldName );
        if( isset( $value['multiple'] ) ) {
          $jsFileGroups[ $value['idGroup'] ] = [];
          foreach( $value['multiple'] as $fileInfo ) {
            $jsFileGroups[ $value['idGroup'] ][] = $fileInfo['prev'];
          }
        }
      }
    }


    $html .= '<!-- Cogumelo module form ' . $this->getName() . ' -->' . "\n";
    $html .= '<'.'script>'."\n\n";
    $html .= '$( document ).ready( function() {'."\n\n";
    $html .= '  console.log( "* PREPARANDO validateForm de '.$this->id.'" );'."\n\n";

    $html .= '  var $formCtrl = new cogumelo.formControllerClass( "'.$this->id.'", '.$formOptions.' );'."\n\n";

    if( count( $jsFileGroups ) ) {
      foreach( $jsFileGroups as $fileGroupId => $fileGroupInfo ) {
        $html .= '  $formCtrl.fileGroup['.$fileGroupId.'] = '.json_encode( $fileGroupInfo ).';'."\n";
      }
      $html .= "\n\n";
    }

    $html .= '  var $validateForm = $formCtrl.setValidateForm( '.$scRules.', '.$scMsgs.' );'."\n\n";
    $html .= '  console.log( "* NEW validateForm: ", $formCtrl, $validateForm );'."\n\n";

    foreach( $this->getFieldsNamesArray() as $fieldName ) {
      if( $this->getFieldType( $fieldName ) === 'file' ) {
        $fileInfo = $this->getFieldValue( $fieldName );
        if( $fileInfo[ 'status' ] === 'EXIST' ) {
          $html .= '  $formCtrl.fileFieldToOk( "'.$fieldName.'", { '.
            '"id": "'.$fileInfo['prev']['id'].'", "name": "'.$fileInfo['prev']['name'].'", '.
            '"aKey": "'.$fileInfo['prev']['aKey'].'", "type": "'.$fileInfo['prev']['type'].'" } );'."\n\n";
        }
      }
    }


    $html .= '});'."\n\n"; // document ready END
    $html .= '</script>'."\n\n";

    if( $this->captchaEnable() ) {
      $html .= '<'.'script src="https://www.google.com/recaptcha/api.js" async defer></script>'."\n\n";
    }

    $html .= '<!-- Cogumelo module form '.$this->getName().' - END -->'."\n\n";

    //$html .= '<pre>'. print_r( $this->fields, true ) .'</pre>';

    return $html;
  } // function getScriptCode


  // js controler V1

  // public function getScriptCode() {
  //   $html = '';

  //   // js controler V1

  //   $separador = '';

  //   $html .= '<!-- Cogumelo module form ' . $this->getName() . ' -->' . "\n";


  //   if( $this->htmlEditor ) {
  //     form::loadDependence( 'ckeditor' );
  //   }

  //   $scRules = ( count( $this->rules ) > 0 ) ? json_encode( $this->rules ) : 'false';
  //   $scMsgs = ( count( $this->messages ) > 0 ) ? json_encode( $this->messages ) : 'false';

  //   $jsFileGroups = [];
  //   foreach( $this->getFieldsNamesArray() as $fieldName ) {
  //     if( $this->getFieldParam( $fieldName, 'type' ) === 'file' && $this->getFieldParam( $fieldName, 'multiple' ) ) {
  //       $value = $this->getFieldValue( $fieldName );
  //       if( isset( $value['multiple'] ) ) {
  //         $jsFileGroups[ $value['idGroup'] ] = [];
  //         foreach( $value['multiple'] as $fileInfo ) {
  //           $jsFileGroups[ $value['idGroup'] ][] = $fileInfo['prev'];
  //         }
  //       }
  //     }
  //   }


  //   $html .= '<'.'script>'."\n".
  //     'var cogumelo = cogumelo || {};'."\n".
  //     'cogumelo.formController = cogumelo.formController || {};'."\n\n";

  //   if( count( $jsFileGroups ) ) {
  //     $html .= 'cogumelo.formController.fileGroup = cogumelo.formController.fileGroup || [];'."\n";
  //     foreach( $jsFileGroups as $fileGroupId => $fileGroupInfo ) {
  //       $html .= 'cogumelo.formController.fileGroup['.$fileGroupId.'] = '.json_encode( $fileGroupInfo ).';'."\n";
  //     }
  //     $html .= "\n";
  //   }

  //   $html .= '$( document ).ready( function() {'."\n".
  //     '  $validateForm_'.$this->id.' = setValidateForm( "'.$this->id.'", '.$scRules.', '.$scMsgs.' );'."\n".
  //     '  console.log( $validateForm_'.$this->id.' );'."\n";

  //   foreach( $this->getFieldsNamesArray() as $fieldName ) {
  //     if( $this->getFieldType( $fieldName ) === 'file' ) {
  //       $fileInfo = $this->getFieldValue( $fieldName );
  //       if( $fileInfo[ 'status' ] === 'EXIST' ) {
  //         $html .= '  fileFieldToOk( "'.$this->id.'", "'.$fieldName.'", { '.
  //           '"id": "'.$fileInfo['prev']['id'].'", "name": "'.$fileInfo['prev']['name'].'", '.
  //           '"aKey": "'.$fileInfo['prev']['aKey'].'", "type": "'.$fileInfo['prev']['type'].'" } );'."\n";
  //       }
  //     }
  //   }

  //   if( $this->htmlEditor ) {
  //     $html .= '  activateHtmlEditor( "'.$this->id.'" );'."\n";
  //   }

  //   if( $this->getMarginTop() ) {
  //     $html .= '  setFormInfo( "'.$this->id.'", "marginTop", '.$this->getMarginTop().' );'."\n";
  //   }



  //   $html .= '});'."\n";
  //   $html .= '</script>'."\n";

  //   if( $this->captchaEnable() ) {
  //     $html .= '<'.'script src="https://www.google.com/recaptcha/api.js" async defer></script>'."\n";
  //   }

  //   $html .= '<!-- Cogumelo module form '.$this->getName().' - END -->'."\n";

  //   //$html .= '<pre>'. print_r( $this->fields, true ) .'</pre>';

  //   return $html;
  // } // function getScriptCode


  /**
   * Añade tareas para ejecutar en el navegador al finalizar bien el submit
   *
   * @param string $successName Nombre clave del suceso: accept, redirect, ...
   * @param mixed $successParam Contenido del evento: Msg, url, ...
   */
  public function setSuccess( $successName, $successParam = true ) {
    // error_log('FormController: setSuccess: name='. $successName . ' param=' .  $successParam );
    // 'onSubmitOk' : JS function
    // 'onSubmitError' : JS function
    // 'onFileUpload' : JS function
    // 'onFileDelete' : JS function

    // Tareas predefinidas para cuando un formulario finaliza correctamente
    // 'jsEval' : Ejecuta el texto indicado con un eval
    // 'accept' : Muestra el texto como un alert
    // 'redirect' : Pasa a la url indicada con un window.location.replace
    // 'reload' : window.location.reload
    // 'resetForm' : Borra el formulario

    $this->success[ $successName ] = $successParam;
  }

  /**
   * Elimina una o todas las tareas que se han definido para el navegador al finalizar bien el submit
   *
   * @param string $successName Nombre clave del suceso: accept, redirect, ... De no indicarse, serán TODOS
   */
  public function removeSuccess( $successName = false ) {
    // error_log('FormController: removeSuccess: ' . print_r( $this->success, true ) );

    if( $successName ) {
      if( isset( $this->success[ $successName ] ) ) {
        unset( $this->success[ $successName ] );
      }
    }
    else {
      $this->success = false;
    }

    return $this->success;
  }

  /**
   * Recupera las tareas que se han definido para el navegador al finalizar bien el submit
   *
   * @return array
   */
  public function getSuccess() {
    // error_log('FormController: getSuccess: ' . print_r( $this->success, true ) );

    return $this->success;
  }

  /**
   * Recupera un JSON con el Ok o los errores que hay que enviar al navegador
   *
   * @param mixed $moreInfo Added to json in the 'moreInfo' field
   *
   * @return string
   */
  public function getJsonResponse( $moreInfo = false ) {
    $json = '';
    if( !$this->existErrors() ) {
      $json = $this->getJsonOk( $moreInfo );
    }
    else {
      // $this->addFormError( 'NO SE HAN GUARDADO LOS DATOS.', 'formError' );
      $json = $this->getJsonError( $moreInfo );
    }

    return $json;
  }

  /**
   * Envía el JSON con el Ok o los errores al navegador
   *
   * @param mixed $moreInfo Added to json in the 'moreInfo' field
   *
   * @return string
   */
  public function sendJsonResponse( $moreInfo = false ) {
    $json = $this->getJsonResponse( $moreInfo );

    header('Content-Type: application/json; charset=utf-8');
    echo $json;

    return $json;
  }

  /**
   * Recupera un JSON de OK con los sucesos que hay que lanzar en el navegador
   *
   * @param mixed $moreInfo Added to json in the 'moreInfo' field
   *
   * @return string
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
   * Recupera un JSON de ERROR con los errores que hay que mostrar en el navegador
   *
   * @param mixed $moreInfo Added to json in the 'moreInfo' field
   *
   * @return string
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
      'result' => ( count( $this->fields ) > 1 ) ? 'error' : 'errorSession',
      'jvErrors' => $jvErrors,
      'success' => $this->getSuccess()
    );
    if( $moreInfo !== false ) {
      $result['moreInfo'] = $moreInfo;
    }

    return json_encode( $result );
  }

  /**********************************************************************/
  /***  HTML y JS (FIN)                                               ***/
  /**********************************************************************/



  /**********************************************************************/
  /***  Validaciones y gestion de errores                             ***/
  /**********************************************************************/

  /**
   * Establece una regla de validacion para un campo
   *
   * @param string $fieldName Nombre del campo
   * @param string $ruleName Nombre de la regla
   * @param mixed $ruleParams Parámetros de la regla
   */
  public function setValidationRule( $fieldName, $ruleName, $ruleParams = true ) {
    if( isset( $this->fields[ $fieldName ] ) ) {
      if( $ruleName === 'required' && $this->getFieldType( $fieldName ) === 'file' ) {
        $ruleName === 'fileRequired';
      }
      $this->rules[ $fieldName ][ $ruleName ] = $ruleParams;
      $this->updateFieldRulesToSession( $fieldName );
    }
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
    // error_log('FormController: cloneValidationRules: '.$fieldNameFrom.', '.$fieldNameTo );
    $fieldRules = $this->getValidationRules( $fieldNameFrom );
    if( is_array( $fieldRules ) ) {
      foreach( $fieldRules as $ruleName => $ruleParams ) {
        // error_log('FormController: '. 'cloneValidationRules: '.$ruleName );
        $this->setValidationRule( $fieldNameTo, $ruleName, $ruleParams );
      }
      $this->updateFieldRulesToSession( $fieldNameTo );
    }
  }

  /**
    Elimina una regla de un campo
    @param string $fieldName Nombre del campo
    @param string $ruleName Nombre de la regla
   */
  public function removeValidationRule( $fieldName, $ruleName ) {
    if( $ruleName === 'required' && $this->getFieldType( $fieldName ) === 'file' ) {
      $ruleName === 'fileRequired';
    }
    if( isset( $this->rules[ $fieldName ][ $ruleName ] ) ) {
      unset( $this->rules[ $fieldName ][ $ruleName ] );
      $this->updateFieldRulesToSession( $fieldName );
    }
  }

  /**
    Elimina todas las reglas de un campo
    @param string $fieldName Nombre del campo
   */
  public function removeValidationRules( $fieldName ) {
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

    return !empty( $this->rules[ $fieldName ]['required'] ) || !empty( $this->rules[ $fieldName ]['fileRequired'] );
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
      error_log('FormController: FALTA CARGAR LOS VALIDADORES' );
    }

    return $validate;
  }










  /**
    Verifica que se cumplen todas las reglas establecidas
    @return boolean
   */
  public function validateForm() {
    // error_log('FormController: validateForm:' );

    $formValidated = true;

    // Tienen que existir los validadores y los valores del form
    if( $this->issetValidationObj() ) {

      if( $this->captchaEnable() && !$this->captchaValidate() ) {
        $this->addFormError( 'Captcha no válido' ); // , $msgClass );
        error_log('FormController: Captcha no válido' );
      }

      foreach( $this->rules as $fieldName => $fieldRules ) {
        if( $this->getFieldInternal( $fieldName, 'groupCloneRoot' ) !== true ) {
          // Procesamos los campos que no son raiz de campos agrupados
          // error_log('FormController: validateForm: campo: '.$fieldName );
          $fieldValidated = $this->validateField( $fieldName );
          $formValidated = $formValidated && $fieldValidated;
        }
      } // foreach( $this->rules as $fieldName => $fieldRules )
    } // if( $this->issetValidationObj() )
    else {
      $formValidated = false;
      error_log('FormController: FALTA CARGAR LOS VALIDADORES' );
    }

    return $formValidated;
  } // function validateForm()










  /**
    Verifica que se cumplen las reglas establecidas para un campo
    @param string $fieldName Nombre del campo
    @return boolean
   */
  public function validateField( $fieldName ) {
    // error_log('FormController: validateField: '.$fieldName );
    $fieldValidated = true;

    if( $this->isEmptyFieldValue( $fieldName ) ) {
      if( $this->isRequiredField( $fieldName ) ) {
        // error_log('FormController: ERROR: evaluateRule( '.$fieldName.', VACIO, required, ...  )' );
        $this->addFieldRuleError( $fieldName, 'required' );
        $fieldValidated = false;
      }
    } // if( $this->isEmptyFieldValue( $fieldName ) )
    else {
      //error_log('FormController: evaluateRule: non VACIO - Evaluar contido coas reglas...' );

      $fieldRules = $this->getValidationRules( $fieldName );
      $fieldType = $this->getFieldType( $fieldName );
      $fieldValues = $this->getFieldValue( $fieldName );

      // Hay que tener cuidado con ciertos fieldValues con estructura de array pero que son un único elemento
      if( $fieldType === 'file' && ( isset( $fieldValues['status'] ) || isset( $fieldValues['multiple'] ) ) ) {
        $fieldValues = array( $fieldValues );
      }

      if( !is_array( $fieldValues ) ) {
        $fieldValues = array( $fieldValues );
      }

      foreach( $fieldValues as $value ) {
        $fieldValidateValue = true;
        if( !empty( $fieldRules ) ) {
          foreach( $fieldRules as $ruleName => $ruleParams ) {
            // error_log('FormController: evaluateRule( '.$fieldName.', '.print_r( $value, true ).', '.$ruleName.', '.print_r( $ruleParams, true ) .' )' );

            if( $ruleName === 'equalTo' ) {
              $fieldRuleValidate = ( $value === $this->getFieldValue( str_replace('#', '', $ruleParams )) );
            }
            else {
              $fieldRuleValidate = $this->evaluateRule( $fieldName, $value, $ruleName, $ruleParams );
            }
            //error_log('FormController: evaluateRule RET: '.print_r( $fieldRuleValidate, true ) );

            if( !$fieldRuleValidate ) {
              error_log('FormController: ERROR: evaluateRule( '.$fieldName.', '.print_r( $value, true ).', '.
                  $ruleName.', '.print_r( $ruleParams, true ) .' )' );
              $this->addFieldRuleError( $fieldName, $ruleName );
              //$this->fieldErrors[ $fieldName ][ $ruleName ] = $fieldRuleValidate;
            }

            $fieldValidateValue = $fieldValidateValue && $fieldRuleValidate;
          } // foreach( $fieldRules as $ruleName => $ruleParams )
        }
        $fieldValidated = $fieldValidated && $fieldValidateValue;
      } // foreach( $fieldValues as $value )

    } // else if( $this->isEmptyFieldValue( $fieldName ) )

    return( $fieldValidated );
  }

  /**
   * Añade un mensaje de error al formulario
   *
   * @param string $msgError Mensaje de error
   * @param string $msgClass
   */
  public function addFormError( $msgError, $msgClass = false ) {
    // error_log('FormController: '. "addFormError: $msgError, $msgClass" );
    $this->formErrors[] = array( 'msgText' => $msgError, 'msgClass' => $msgClass );
  }

  /**
   * Añade un mensaje de error a un campo del formulario
   *
   * @param string $fieldName Nombre del campo
   * @param string $msgError Mensaje de error
   */
  public function addFieldError( $fieldName, $msgError ) {
    $this->addFieldRuleError( $fieldName, 'cogumelo', $msgError );
  }

  /**
   * Añade un mensaje de error a una regla de un campo del formulario
   *
   * @param string $fieldName Nombre del campo
   * @param string $ruleName
   * @param string $msgError Mensaje de error
   */
  public function addFieldRuleError( $fieldName, $ruleName, $msgError = false ) {
    // error_log('FormController: '. "addFieldRuleError: $fieldName, $ruleName, $msgError " );
    $this->fieldErrors[ $fieldName ][ $ruleName ] = $msgError;
  }

  /**
   * Añade un mensaje de error una regla de un grupo del formulario
   *
   * @param string $groupName Nombre del grupo
   * @param string $ruleName
   * @param string $msgError Mensaje de error
   */
  public function addGroupRuleError( $groupName, $ruleName, $msgError = false ) {
    // error_log('FormController: '. "addGroupRuleError: $groupName, $ruleName, $msgError " );
    $this->fieldErrors[ $groupName ][ $ruleName ] = $msgError;
  }

  /**
   * Obtiene la lista de errores en el formulario (Globales)
   */
  public function getFormErrors() {
    return( $this->formErrors );
  }

  /**
   * Obtiene la lista de errores en los campos del formulario
   */
  public function getFieldsErrors() {
    return( $this->fieldErrors );
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

  /**********************************************************************/
  /***  Validaciones y gestion de errores (FIN)                       ***/
  /**********************************************************************/

} // END FormController class
