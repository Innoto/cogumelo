<?php


Cogumelo::load('c_model/mysql/MysqlDAO.php');
Cogumelo::load('model/LostVO.php');

//
//	Mysql lost DAO
//

class MysqlLostDAO extends MysqlDAO
{
	var $VO = "LostVO";
	var $filters = array();

}

?>
