<?php

// load all VO's
Cogumelo::load('coreModel/VOUtils.php');
VOUtils::includeVOs();


/**
 * Value Object (Used by the model)
 *
 * @package Cogumelo Model
 */
Class VO
{
  var $name = '';
  var $data = array();
  var $depData = array();
  var $depKeys = array();
  var $relObj = false;

  function __construct(array $datarray, $otherRelObj= false ) {
    $this->setData( $datarray, $otherRelObj );
  }

  /**
   * Sets data of VO
   *
   * @param array $datarray array (data referenced by keys)
   * @param object $otherRelObj internal use only
   *
   * @return void
   */
  function setData(array $datarray, $otherRelObj= false ){

    // get class name
    $this->name = get_class( $this );

    // Common developer errors
    if(!isset($this::$tableName)){
      Cogumelo::error('all Model Must have declared an $this::$tableName');
      return false;
    }
    if(!isset($this::$cols)){
      Cogumelo::error($this::$tableName.'Model must have an self::$cols array (See Cogumelo documentation)');
      return false;
    }
    if(!$this->getFirstPrimarykeyId()){
      Cogumelo::error($this::$tableName.'Model must be declared at least one primary key in $this::$cols array (See Cogumelo documentation)');
      return false;
    }


    // seting relationship keys
    if( $otherRelObj == false ) {
      $this->relObj = VOUtils::getRelObj( $this->name ) ;
    }
    else if( is_object( $otherRelObj ) ) {
      $this->relObj = $otherRelObj;
    }

    $this->depKeys = VOUtils::getRelKeysByRelObj( $this->relObj, true );
    $this->setVarList( $datarray );
  }


  /**
   * set variable list (initializes entity)
   *
   * @param array $datarray array (data referenced by keys)
   *
   * @return object
   */
  function setVarList(array $datarray) {

    // rest of variables
    foreach($datarray as $k=>$data) {

      if( preg_match('#(.*)\.(.*)#', $k ,$tAndkey) ){
        $datakey = $tAndkey[2];
      }
      else {
        $datakey = $k;
      }

      // set dependence VOs
      if( array_key_exists( $datakey , $this->depKeys) ){
        if( $data ) {
          $this->setDepVOs( $data, $this->depKeys[$datakey], VOUtils::searchVOinRelObj( $this->depKeys[$datakey], $this->relObj) );
        }
      }
      // set cols
      else {
        $this->setter( $datakey, $data );
      }
    }
  }


  /**
   * set dependence VOs from data
   *
   * @param array $data
   * @param string $voName name of VO or Model
   * @param object $relObj related object
   *
   * @return void
   */
  function setDepVOs( $data, $voName, $relObj ) {


    if( is_array($data) ) {
      foreach( $data as $d ) {
        $this->setDepVO($d, $voName, $relObj->parentId, $relObj);
      }
    }
    else
    {
      //var_dump('['.$data.']');
      //var_dump( json_decode('['.$data.']') );
      //var_dump( $json_errors[json_last_error()] );
      //exit;

      // when is first rel decode it
      if( $d = json_decode('['.$data.']')  ){

        if( sizeof($d)>0 ) {
          foreach($d as $dep) {
            $this->setDepVO($dep, $voName, $relObj->parentId, $relObj);
          }
        }

       }
      else {
        // JSON DECODE ERROR
        //Cogumelo::error('Problem decoding VO JSON in '.$this->name.'. Provably the result is truncated, try to increase DB_MYSQL_GROUPCONCAT_MAX_LEN constant in configuration or optimize query.');
        $constants = get_defined_constants(true);
        $json_errors = array();
        foreach ($constants["json"] as $name => $value) {
            if (!strncmp($name, "JSON_ERROR_", 11)) {
                $json_errors[$value] = $name;
            }
        }

        // Show the errors for different depths.
        foreach (range(4, 3, -1) as $depth) {
          echo 'Problem decoding VO JSON : ', $json_errors[json_last_error()], PHP_EOL, PHP_EOL;
          Cogumelo::error('['.$data.']');
        }
      }


    }
  }


  /**
   * set dependence VO from data
   *
   * @param object $dataVO
   * @param string $voName name of VO or Model
   * @param string $key vo dependence key
   * @param object $relObj related object
   *
   * @return object
   */
  function &setDepVO( $dataVO, $voName, $key, $relObj  ) {
    $retvO = false;

    if( $this->isForeignKey( $key ) ){
      $retVO = new $voName( (array) $dataVO, $relObj );
      $this->depData[ $key ][] = $retVO;
    }
    else {
      $retVO = new $voName( (array) $dataVO, $relObj );
      $this->depData[ $key] [] = $retVO;
    }

    return $retVO;
  }


  /**
   * get VO or Model Name
   *
   * @return string
   */
  function getVOClassName() {
    return $this->name;
  }


  /**
   * gets primary key id
   *
   * @return string
   */
  function getFirstPrimarykeyId() {

    foreach($this::$cols as $cid => $col) {
      if(array_key_exists('primarykey', $this::$cols[$cid])){
        if($this::$cols[$cid]['primarykey'] == true){
          return $cid;
        }
      }
    }

    return false;
  }


  /**
   * get columns list
   *
   * @return array
   */
  function getCols( $realCols = false ){
    global $LANG_AVAILABLE;
    $retCols = array();

    foreach( $this::$cols as $colK=>$col ) {
      if( isset($col['multilang']) && $col['multilang'] == true && $realCols) {
        foreach ( array_keys($LANG_AVAILABLE) as $langKey) {
          $retCols[ $colK.'_'.$langKey ] = $col;
        }
      }
      else {
        $retCols[ $colK ] = $col;
      }
    }

    return $retCols;


  }

  function isMultilangCol( $colK ) {
    $ret = false;

    $this::$cols[ $colK ];

    if( isset($col['multilang']) && $col['multilang'] == true ){
      $ret = true;
    }

    return $ret;
  }


  /**
   * get BBDD table name
   *
   * @return string
   */
  function getTableName(){
    return $this::$tableName;
  }


  /**
   * set any data attribute by key
   *
   * @param mixed $setterkey key or array
   * @param mixed $value
   *
   * @return void
   */
  function &setter( $setterkey, $value = null, $lang = false ) {

    $cols = $this->getCols();

    // if a setter is for concrete lang or col have multilang
    if(
      ( !$lang && array_key_exists($setterkey,$cols) && array_key_exists('multilang',$cols[$setterkey]) && $cols[$setterkey]['multilang'] ) ||
      ( $lang && array_key_exists($setterkey, $cols ) )
    ) {

      if(!$lang)
        $lang = LANG_DEFAULT;

      $setterkey .= '_'.$lang;
    }



    if( is_array($setterkey) && $value === null ) {
      foreach( $setterkey as $k => $e) {
        $this->setter($k, $e);
      }
      $retObj = true;
    }


    if( array_key_exists($setterkey, $cols ) || array_key_exists( $this->langKey($setterkey, true) , $cols ) ) {
      // set values
      if( !is_object($value) && !is_array($value) ) {
        $this->data[$setterkey] = $value;
      }
      $retObj = $this;
    }
    else{
      Cogumelo::debug("key '". $setterkey ."' not exist in VO::". $this::$tableName);
    }

    return $retObj;
  }


  function langKey( $key, $getKey = false ) {

    global $LANG_AVAILABLE;
    $ret = false;
    $regex = '#(.*)_(('.implode(')|(', array_keys($LANG_AVAILABLE) ).'))#';

    $pm = preg_match($regex, $key, $match);

    if($getKey) {
      if( isset($match[1]) ){
        $ret = $match[1];
      }
      else{
        $ret = $key;
      }
    }
    else {
      $ret = $pm;
    }

    return $ret;
  }

  /**
   * set data dependence
   *
   * @param string $fk attribute name
   * @param object $voObj VO or Model
   *
   * @return void
   */
  function setterDependence( $fk, $voObj ){
    $retVO = false;
    $voName = $voObj->getVOClassName();

    $references = array();
    foreach( $this->relObj->relationship as $rel ){
      if( $rel->vo == $voName ) {
        $references[ $voName ] = $rel;
      }
    }

    // Dependence not exist
    if( sizeof($references) == 0 ) {

      Cogumelo::error( $voObj->getVOClassName() .' is not dependence of: '.$this->getVOClassName() );
    }
    else{
      $retVO = $this->setDepVO( $voObj->data, $voName, $fk, array_pop( $references) );
    }


    $this->refreshRelationshipKeyIds();
    return $retVO;
  }


  /**
   * get any data attribute by key
   *
   * @return mixed
   */
  function getter($getterkey, $lang = false) {

    $value = null;
    $cols = $this->getCols();

    if(
      (!$lang && array_key_exists($getterkey,$cols) && array_key_exists('multilang',$cols[$getterkey]) && $cols[$getterkey]['multilang'] ) )
    {
      $getterkey .= '_'.LANG_DEFAULT;
    }
    else
    if( $lang ) {
      $getterkey .= '_'.$lang;
    }

    // get values
    if( array_key_exists($getterkey, $this->data) ) {
      $value = $this->data[$getterkey];
    }

    return $value;
  }

  /**
   * dependence getter
   *
   * @param string $reference reference key
   *
   * @return array
   */
  function getterDependence( $reference, $onlyModel = false ) {
    $depReturn = false;

    if( array_key_exists($reference, $this->depData) ){
      $depReturn = &$this->depData[ $reference ];
    }

    if( $onlyModel && $depReturn ) {
      $depsFiltered = array();


      foreach ( $depReturn as $depK => $dep ) {

        if( $dep->getVOClassName() == $onlyModel) {
          $depsFiltered[ $depK ] = $dep;
        }
      }

      if( sizeof($depsFiltered) ) {
        $depReturn = $depsFiltered;
      }
      else {
        $depReturn = false;
      }
    }

    return $depReturn;
  }



  /**
   * dependence deletion
   *
   * @param string $reference reference key
   *
   * @return array
   */
  function deleteDependence( $reference, $onlyModel = false, $delete = true ) {

    if( array_key_exists($reference, $this->depData) ){
      $depReturn = &$this->depData[ $reference ];
    }

    if( $onlyModel && $depReturn ) {
      $depsFiltered = array();


      foreach ( $depReturn as $depK => $dep ) {

        if( $dep->getVOClassName() == $onlyModel) {
          $depsFiltered[ $depK ] = $dep;
        }
      }

      if( sizeof($depsFiltered) ) {
        $depReturn = $depsFiltered;
      }
      else {
        $depReturn = false;
      }
    }

  }




  function getKeys() {

    $retArray = array();

    foreach( $this->getCols() as $cK => $c) {
      $retArray[] = $cK;
    }

    return $retArray;
  }

  /**
   * get key list into string
   *
   * @return string
   */
  function getKeysToString( $fields, $resolveDependences=false ) {
    $retFields = array();

    $originalCols = $this->getCols( true );

    // main vo Fields
    if( !$fields ) {
      $retFields = array_merge($retFields, array_keys( $originalCols ) );
    }
    else {
      $retFields = array_merge($retFields, $fields );
    }

    $originalCols = $this->getCols();

    foreach($retFields as $fkey => $retF )  {
        $retFields[$fkey] = $this->getTableName().'.'.$retF;

    }

    // relationship cols
    if( $resolveDependences ) {
      $retFields = array_merge($retFields, VOUtils::getRelKeys(  $this->name, false, $resolveDependences ) );
    }

    return implode(', ', $retFields);
  }


  /**
   * dependence data
   *
   * @return array
   */
  function &getDependences() {
    return $this->depData;
  }



  /**
   * get all dependences in no nested array
   *
   * @return array
   */
  function getDepInLinearArray( &$vo = false, $parentArrayKey=false, $vosArray = array() ) {

    if(!$vo){
      $vo = $this;
    }

    $currentArrayKey = sizeof($vosArray);
    $vosArray[] = array( 'ref' => $vo, 'parentKey' => $parentArrayKey );

//var_dump( $vo->getVOClassName() );

    $depData = $vo->depData;
    if( sizeof($depData) > 0  ) {
      foreach( $depData as $depVO ){
        if( is_array($depVO) ) {
          foreach($depVO as $dVO) {
            $vosArray = $vo->getDepInLinearArray( $dVO, $currentArrayKey, $vosArray );
          }
        }
        else {
          $vosArray = $vo->getDepInLinearArray( $depVO, $currentArrayKey, $vosArray );
        }
      }
    }

    return $vosArray;
  }


  function isForeignKey( $key ) {
    $res = false;
    if( array_key_exists( 'type', $this::$cols[ $key ]) &&  $this::$cols[ $key ]['type'] == 'FOREIGN') {
      $res = true;
    }


    return $res;
  }


  /**
   * get nested array with data (including loaded dependences)
   *
   * @return array
   */
  function getAllData() {

    $relationshipArrayData = array();

    foreach ( $this->getDependences()  as $dep ){

      if( is_array( $dep ) ){
        $depA = array();
        foreach( $dep as $d  ) {
          $depA[] = $d->getAllData();
        }
        $relationshipArrayData[] = $depA;
      }
      else {
        $relationshipArrayData[] = $dep->getAllData();
      }

    }

    return array( 'modelName' => $this->name, 'data' => $this->data, 'relationship' =>$relationshipArrayData);
  }



  /**
   * refresh all relationship ids from sons to parents
   *
   * @return void
   */
  function refreshRelationshipKeyIds() {
    $deps = $this->getDepInLinearArray();

    while( $dep = array_pop( $deps ) ){

      if(
        $dep['parentKey'] !== false
      ) {

        $vo = $dep['ref'];


        $voParent = $deps[ $dep['parentKey'] ]['ref'];

        if( $voParent->getter( $vo->relObj->parentId) ) {
          $vo->setter( $vo->relObj->relatedWithId , $voParent->getter( $vo->relObj->parentId) );
        }
        else
        if( $vo->getter( $vo->relObj->relatedWithId) ) {
          $voParent->setter( $vo->relObj->parentId , $vo->getter( $vo->relObj->relatedWithId  ) );
        }

        //echo "this ".$vo->getVOClassName().".".$vo->relObj->relatedWithId.": ". $vo->getter( $vo->relObj->relatedWithId  ).'<br>';
        //echo "parent ".$voParent->getVOClassName().".". $vo->relObj->parentId.": ". $voParent->getter( $vo->relObj->parentId  ).'<br><br>';



      }
    }
  }

}
