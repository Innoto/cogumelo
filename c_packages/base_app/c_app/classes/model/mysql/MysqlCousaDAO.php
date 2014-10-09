<?php




Cogumelo::load('c_model/mysql/MysqlDAO.php');
testmodule::load('model/CousaVO.php');

//
//	Mysql cousa DAO
//

class MysqlCousaDAO extends MysqlDAO
{
	var $VO = "CousaVO";
	var $filters = array(
		);

}