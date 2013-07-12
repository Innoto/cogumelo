<?




Cogumelo::load('c_model/mysql/MysqlDAO');
Cogumelo::load('model/CousaVO');

//
//	Mysql cousa DAO
//

class MysqlCousaDAO extends MysqlDAO
{
	var $VO = "CousaVO";
	var $filters = array(
		);

}