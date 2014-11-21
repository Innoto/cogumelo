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

    Cogumelo::console($password);
    // SQL Query
    $strSQL = "SELECT * FROM `user` WHERE `login` = ? and `password` = ? ;";

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

/*
  //
  //  Use an UserVO to update a User password.
  //
  function updatePassword($connection, $user)
  {
    // SQL Query
    $StrSQL = sprintf("UPDATE `user` SET
        password = SHA1('%s')
      WHERE `id` = %s ;",
      $connection->real_escape_string($user->getter('password')),
      $connection->real_escape_string($user->getter('id'))
    );

    // Secure SQL Query (not real)
    $StrSQLSecure = sprintf("UPDATE `user` SET
        password = SHA1('XXX')
      WHERE `id` = %s ;",
      $user->getter('id')
    );

    return MysqlDAOutils::execSQL($connection, $StrSQL, $StrSQLSecure);
  }
*/

  //
  //  Use an UserVO to update User last login
  //
  function updateTimeLogin($connectionControl, $id, $date)
  {
    $strSQL = "UPDATE `user` SET timeLastLogin = ? WHERE `id` = ? ;";
    $res = $this->execSQL($connectionControl, $strSQL, array($date, $id));
    return $res;
  }
}