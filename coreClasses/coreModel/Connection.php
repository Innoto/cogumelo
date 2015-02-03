<?php


abstract class Connection
{
	public static function factory($devel_data = false)
	{

		$class = 'coreModel/'. DB_ENGINE . '/'. ucfirst(DB_ENGINE) ."Connection";
		Cogumelo::load($class.'.php');
		
		$dbObj = ucfirst(DB_ENGINE)."Connection";
		return new $dbObj($devel_data);
	}
}
