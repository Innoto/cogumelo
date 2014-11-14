<?php

Cogumelo::load('c_model/mysql/MysqlDAO.php');
filedata::load('model/FiledataVO.php');

//
//  Mysql useradmin DAO
//

class MysqlFiledataDAO extends MysqlDAO
{
  var $VO = "FiledataVO";
  var $filters = array(
      'find' => "name  LIKE CONCAT('%',?,'%') OR login LIKE CONCAT('%', ?, '%')"
    );

}