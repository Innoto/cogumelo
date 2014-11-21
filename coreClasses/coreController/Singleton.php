<?php


/**
* Singleton Class
*
* A singleton class to make cogumelo main object run as singleton
*
* @author: pablinhob
*/


abstract class Singleton
{
	private static $instance = false;
	
    protected function __construct() {}
    final private function __clone() {}

    protected static function getInstance($class)
    {
		if(self::$instance === false){
			self::$instance =  new $class;
		}

		return self::$instance;
    }
}