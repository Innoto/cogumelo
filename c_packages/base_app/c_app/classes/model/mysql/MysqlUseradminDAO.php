<?php


Cogumelo::load('c_model/mysql/MysqlDAO.php');
Cogumelo::load('model/UseradminVO.php');

//
//	Mysql useradmin DAO
//

class MysqlUseradminDAO extends MysqlDAO
{
	var $VO = "UseradminVO";
	var $filters = array(
			'find' => "name  LIKE CONCAT('%',?,'%') OR login LIKE CONCAT('%', ?, '%')",
			'edadmax' => "edad <= ?",
			'edadmin' => "edad >= ?"
		);


	//
	//	Authenticate administrator user
	//
	//	Return: useradminVO	(null if 0 rows)
	function authenticate($connection, $login, $pass)
	{		
		// SQL Query
		$StrSQL = "SELECT * FROM `useradmin` WHERE `login` = '".$connection->real_escape_string( $login )."' and `passwd` = SHA1('".$connection->real_escape_string( $pass )."');";
		// Secure SQL Query for log dump
		$StrSQLSecure = "SELECT * FROM `useradmin` WHERE `login` = '".$connection->real_escape_string( $login )."' and `passwd` = SHA1('XXX');";
		
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
	//	Use an useradminVO to update a Useradmin password. 
	//	
	function updatePasswd($connection, $useradmin)
	{
		// SQL Query
		$StrSQL = sprintf("UPDATE `useradmin` SET 		   	    
		    passwd = SHA1('%s')		    		    
		  WHERE `id` = %s ;",						
			$connection->real_escape_string($useradmin->getter('passwd')),						
			$connection->real_escape_string($useradmin->getter('id'))
		);
		
		// Secure SQL Query (not real)
		$StrSQLSecure = sprintf("UPDATE `useradmin` SET 		   	    
		    passwd = SHA1('XXX')		    		    
		  WHERE `id` = %s ;",														
			$useradmin->getter('id')
		);
		
		return MysqlDAOutils::execSQL($connection, $StrSQL, $StrSQLSecure);	
	}

	//
	//	Use an useradminVO to update Useradmin last login 
	//	
	function updateTime($connection, $useradmin)
	{
		// SQL Query
		$StrSQL = sprintf("UPDATE `useradmin` SET 		   	    
		    time_lastlogin = '%s'		    		    
		  WHERE `id` = %s ;",						
			$useradmin->getter('time_lastlogin'),						
			$useradmin->getter('id')
		);
		
		return MysqlDAOutils::execSQL($connection,$StrSQL);
	}
}

?>
