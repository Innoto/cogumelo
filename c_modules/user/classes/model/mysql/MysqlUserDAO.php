<?php


Cogumelo::load('c_model/mysql/MysqlDAO.php');
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
  function authenticate($connection, $login, $pass)
  {
    // SQL Query
    $StrSQL = "SELECT * FROM `user` WHERE `login` = '".$connection->real_escape_string( $login )."' and `password` = SHA1('".$connection->real_escape_string( $pass )."');";
    // Secure SQL Query for log dump
    $StrSQLSecure = "SELECT * FROM `user` WHERE `login` = '".$connection->real_escape_string( $login )."' and `password` = SHA1('XXX');";

    if( $res = MysqlDAOutils::execSQL($connection,$StrSQL, StrSQLSecure) ) {
      if($res->num_rows == 1)
        return $this->FindByLogin($connection, $login);
      else
        return false;
    }
    else
      return false;

  }


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

  //
  //  Use an UserVO to update User last login
  //
  function updateTime($connection, $user)
  {
    // SQL Query
    $StrSQL = sprintf("UPDATE `user` SET
        timeLastLogin = '%s'
      WHERE `id` = %s ;",
      $user->getter('timeLastLogin'),
      $user->getter('id')
    );

    return MysqlDAOutils::execSQL($connection,$StrSQL);
  }
}

?>
