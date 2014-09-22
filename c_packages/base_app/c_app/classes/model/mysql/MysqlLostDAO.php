<?php


Cogumelo::load('c_model/mysql/MysqlDAO');
Cogumelo::load('model/LostVO');

//
//	Mysql lost DAO
//

class MysqlLostDAO extends MysqlDAO
{
	var $VO = "LostVO";
	var $filters = array();

}

?>
