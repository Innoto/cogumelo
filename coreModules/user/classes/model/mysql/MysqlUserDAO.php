<?php


Cogumelo::load('coreModel/mysql/MysqlDAO.php');
user::load('model/UserVO.php');

//
//  Mysql useradmin DAO
//

class MysqlUserDAO extends MysqlDAO
{
  var $VO = "UserVO";

  var $filters = array(
      'find' => "name  LIKE CONCAT('%',?,'%') OR login LIKE CONCAT('%', ?, '%')",
      'edadmax' => "edad <= ?",
      'edadmin' => "edad >= ?"
    );


  //
  //  Authenticate user
  //
  //  Return: UserVO (null if 0 rows)
  function authenticateUser($connectionControl, $login, $password)
  {
    $objVO  = new $this->VO();

    // SQL Query
    $strSQL = "SELECT * FROM `".$objVO::$tableName."` WHERE `login` = ? and `password` = ? ;";

    if( $res = $this->execSQL($connectionControl, $strSQL, array($login, $password)) ) {

      if($res->num_rows == 1){
        return $this->find($connectionControl, $login, 'login');
      }
      else{
        return false;
      }
    }
    else{
      return COGUMELO_ERROR;
    }

  }

  //
  //  Use an UserVO to update User last login
  //
  function updateTimeLogin($connectionControl, $id, $date)
  {
    $objVO  = new $this->VO();
    $strSQL = "UPDATE `".$objVO::$tableName."` SET timeLastLogin = ? WHERE `id` = ? ;";
    $res = $this->execSQL($connectionControl, $strSQL, array($date, $id));
    return $res;
  }
}