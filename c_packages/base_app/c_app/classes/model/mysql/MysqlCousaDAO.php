<?php




Cogumelo::load('c_model/mysql/MysqlDAO');
testmodule::load('model/CousaVO');

//
//	Mysql cousa DAO
//

class MysqlCousaDAO extends MysqlDAO
{
	var $VO = "CousaVO";
	var $filters = array(
		);

}