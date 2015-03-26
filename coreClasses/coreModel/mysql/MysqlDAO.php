<?php

Cogumelo::load('coreController/Cache.php');
Cogumelo::load('coreModel/DAO.php');
Cogumelo::load('coreModel/mysql/MysqlDAORelationship.php');
Cogumelo::load('coreModel/mysql/MysqlDAOResult.php');


/**
* Mysql DAO (Abstract)
*
* @package Cogumelo Model
*/
class MysqlDAO extends DAO
{
  var $VO;


  /**
  * Composes order mysql (ORDER BY) String
  * 
  * @param array $ORDArray array('id1'=>-1, 'id2'=>1)
  * 
  * @return string
  */
  function orderByString($ORDArray)
  {
    // Direction (ASC, DESC) Array
    if( is_array($ORDArray) )
    {
      $orderSTR = " ORDER BY ";
      $coma = "";
      foreach ($ORDArray as $elementK => $elementV)
      {
        if( !preg_match('/\s/',$elementK) )
        if ($elementV < 0)
          $orderSTR .= $coma .$elementK." DESC";
        else
          $orderSTR .= $coma .$elementK." ASC";

        $coma=", ";
      }
      return $orderSTR;
    }
    else
      return "";
  }


  //
  // 
  //

  /**
  * Execute a SQL query command
  * 
  * @param object $connectionControl mysqli connection object
  * @param string $sql query
  * @param string $val_array value array
  * 
  * @return mixed
  */
  function execSQL(&$connectionControl, $sql, $val_array = array())
  {

    // obtaining debug data
    $d = debug_backtrace();
    $caller_method = $d[1]['class'].'.'.$d[1]['function'].'()';

    //set prepare sql
    $connectionControl->stmt = $connectionControl->db->prepare( $sql );

    if( $connectionControl->stmt ) {  //set prepare sql

      $bind_vars_type = $this->getPrepareTypes($val_array);

      $bind_vars_str = "";
      foreach($val_array as $ak=>$vk){
        $bind_vars_str .= ', $val_array['.$ak.']';
      }


      // bind params
      if($bind_vars_type != "") {
        eval('$connectionControl->stmt->bind_param("'. $bind_vars_type .'"'. $bind_vars_str .');');
      }
        $connectionControl->stmt->execute();

        if($connectionControl->stmt->error == ''){
          $ret_data = $connectionControl->stmt->get_result();
        }
        else {

          Cogumelo::error( "MYSQL STMT ERROR on ".$caller_method.": ".$connectionControl->stmt->error.' - '.$sql);
          $ret_data = COGUMELO_ERROR;
        }

    }
    else {
      Cogumelo::error( "MYSQL QUERY ERROR on ".$caller_method.": ".$connectionControl->db->error.' - '.$sql);

      $ret_data = COGUMELO_ERROR;
    }

    return $ret_data;
  }



  /**
  * get string of chars according prepare type (ex. i:integer, d:double, s:string, b:boolean)
  * 
  * @param array $values_array 
  * 
  * @return string
  */
  function getPrepareTypes($values_array){

    $return_str = "";
    foreach($values_array as $value) {
      if(is_integer($value)) $return_str.= 'i';
      else
      if(is_string($value)) $return_str.= 's';
      else
      if(is_float($value)) $return_str.= 'd';
      else
      if(is_bool($value)) $return_str.= 'b';
    }

    return $return_str;
  }


  /**
  * Generates where clausule
  * 
  * @param array $fiters
  * 
  * @return string
  */
  function getFilters($filters){

    $where_str = "";
    $val_array = array();


    if($filters) {

      $VO = new $this->VO();


      foreach($filters as $fkey => $filter_val) {

        if( array_key_exists($fkey, $this->filters) ) {
          $fstr = " AND ".$this->filters[$fkey];


          // where string
          $where_str.=$fstr;


          // dump value or array value into $values array
          if( is_array($filter_val) ) {
            foreach($filter_val as $val) {
              $val_array[] = $val;
            }
          }
          else {
            $var_count = substr_count( $fstr , "?");
            for($c=0; $c < $var_count; $c++) {
              $val_array[] = $filter_val;
            }
          }
        }


      }
    }

    return array(
        'string' => " WHERE true".$where_str,
        'values' => $val_array
      );
  }


  /**
  * Generic List ittems
  * 
  * @param object $connectionControl mysqli connection object
  * @param array $filters filters array
  * @param array $range query range
  * @param array $order order array
  * @param array 
  * @param boolean $resolveDependences if want to resolve relationship dependences
  * @param boolean $cache save query result into cache
  * 
  * @return object
  */
  function listItems(&$connectionControl, $filters, $range, $order, $fields, $resolveDependences = false, $cache = false)
  {

    // SQL Query
    $VO = new $this->VO();

    // joins
    $mysqlDAORel = new MysqlDAORelationship();
    $joins = $mysqlDAORel->getVOJoins( $this->VO, $resolveDependences, $filters);

    // where string for join queries
    $joinWhereArrays = $mysqlDAORel->getFilterArrays();

    // where string for main query
    $whereArray = $this->getFilters($filters);

    // merge where arrays and array values
    $allWhereArrays = $joinWhereArrays;
    $allWhereArrays[] = $whereArray;


    $allWhereARraysValues = array();
    foreach( $allWhereArrays as $wa ) {
      //var_dump($wa['values']);

      $allWhereARraysValues = array_merge( $allWhereARraysValues, $wa['values'] );
    }


    // order string
    $orderSTR = ($order)? $this->orderByString($order): "";

    // range string
    $rangeSTR = ($range != array() && is_array($range) )? sprintf(" LIMIT %s, %s ", $range[0], $range[1]): "";


    if($resolveDependences) {
      $this->execSQL($connectionControl,'SET group_concat_max_len='.DB_MYSQL_GROUPCONCAT_MAX_LEN.';');
    }



    $strSQL = "SELECT ".
              $VO->getKeysToString($fields, $resolveDependences ) .
              " FROM `" . 
              $VO::$tableName ."` " . 
              $joins. 
              $whereArray['string'] . $orderSTR . $rangeSTR . ";";


    if ( $cache && DB_ALLOW_CACHE  )
    {
      $queryId = md5($strSQL.serialize( $allWhereArrays ));
      $cached =  new Cache();

      if($cache_data = $cached->getCache($queryId)  ) {
        // With cache, serving cache ...
        Cogumelo::debug('Using cache: cache Get with ID: '.$queryId );
        $queryID = $daoresult = new MysqlDAOResult( $this->VO , $cache_data, true); //is a cached result
      }
      else{
        // With cache, but not cached yet. Caching ...
        $res = $this->execSQL($connectionControl,$strSQL, $allWhereARraysValues );

        if( $res != COGUMELO_ERROR ) {
          Cogumelo::debug('Using cache: cache Set with ID: '.$queryId );
          $daoresult = new MysqlDAOResult( $this->VO , $res);
          $cached->setCache($queryId, $daoresult->fetchAll_RAW() );
        }
        else{
          $daoresult = COGUMELO_ERROR;
        }
      }
    }
    else
    {

      //  Without cache!
      $res = $this->execSQL($connectionControl,$strSQL, $allWhereARraysValues );

      if( $res != COGUMELO_ERROR ){
        $daoresult = new MysqlDAOResult( $this->VO , $res);
      }
      else{
        $daoresult = COGUMELO_ERROR;
      }
    }

    return $daoresult;


  }


  /**
  * Generic List Count
  * 
  * @param object $connectionControl mysqli connection object
  * @param array $filters filters array
  * 
  * @return integer
  */
  function listCount(&$connectionControl, $filters)
  {
    $retVal = null;

    // where string and vars
    $whereArray = $this->getFilters($filters);

    // SQL Query
    $VO = new $this->VO();
    $StrSQL = "SELECT count(*) as number_elements FROM `" . $VO::$tableName . "` ".$whereArray['string'].";";

    $res = $this->execSQL($connectionControl,$StrSQL, $whereArray['values']);
    if( $res !== COGUMELO_ERROR )  {

        //$res->fetch_assoc();
        $row = $res->fetch_assoc();
        $retVal = $row['number_elements'];

    }
    else {
      $retVal = COGUMELO_ERROR;
    }


    return $retVal;
  }


  /**
  * Insert record
  * 
  * @param object $connectionControl mysqli connection object
  * @param object $voObj VO or Model object
  * 
  * @return mixed
  */
  function create(&$connectionControl, $VOobj)
  {

    $cols = array();
    foreach( $VOobj::$cols as $colk => $col) {
      if($VOobj->getter($colk) !== null) {
        $cols[$colk] = $col;
      }
    }


    $campos = '`'.implode('`,`', array_keys($cols)) .'`';


    $valArray = array();
    $answrs = "";
    foreach( array_keys($cols) as $colName ) {
      $val = $VOobj->getter($colName);
      $valArray[] = $val;
      $answrs .= ', ?';
    }

    $strSQL = "INSERT INTO `".$VOobj::$tableName."` (".$campos.") VALUES(".substr($answrs,1).");";

    $res = $this->execSQL($connectionControl, $strSQL, $valArray);
    if($res != COGUMELO_ERROR) {
      $VOobj->setter($VOobj->getFirstPrimarykeyId(), $connectionControl->db->insert_id);

      return $VOobj;
    }
    else {
      return COGUMELO_ERROR;
    }
  }

  /**
  * Update record
  * 
  * @param object $connectionControl mysqli connection object
  * @param object $voObj VO or Model object
  * 
  * @return mixed
  */
  function update(&$connectionControl, $VOobj)
  {

    // primary key value
    $pkValue = $VOobj->getter( $VOobj->getFirstPrimarykeyId() );


    // add getter values to values array
    $setvalues = '';
    $valArray = array();
    foreach( $VOobj::$cols as $colk => $col) {
      if($VOobj->getter($colk) !== null) {
        $setvalues .= ', '.$colk.'= ? ';
        $valArray[] = $VOobj->getter($colk);
      }
    }

    // add primary key value to values array
    $valArray[] = $pkValue;

    $strSQL = "UPDATE `".$VOobj::$tableName."` SET ".substr($setvalues, 1)." WHERE `".$VOobj->getFirstPrimarykeyId()."`= ?;";

    $res = $this->execSQL($connectionControl, $strSQL, $valArray);
    if( $res != COGUMELO_ERROR ) {
      return $VOobj;
    }
    else {
      return COGUMELO_ERROR;
    }
  }

  /**
  * delete from key
  * 
  * @param object $connectionControl mysqli connection object
  * @param string $key key to search
  * @param mixed $value value to search
  * 
  * @return boolean
  */
  function deleteFromKey(&$connectionControl, $key, $value)
  {

    $VO = new $this->VO();
    // SQL Query
    $strSQL = "DELETE FROM `" . $VO::$tableName . "` WHERE `".$key."`=? ;";

    $res = $this->execSQL($connectionControl, $strSQL, array($value));
    if( $res != COGUMELO_ERROR ){
      return true;
    }
    else {
      return null;
    }
  }

  /**
  * return list of question marks separated by comma
  * 
  * @param array $elements 
  * 
  * @return string
  */
  function getQuestionMarks( $elements ){
    $qm = str_repeat( '?, ', count($elements)-1 ) . '?';
    return $qm;
  }

}
