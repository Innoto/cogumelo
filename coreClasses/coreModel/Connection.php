<?php

/**
 * Connection Abstract class 
 *
 * @package Cogumelo Model
 */
abstract class Connection
{
	/**
	 * get connection object
   *
   * @param mixed $devel_data 
   *
   * @return object
   */
	public static function factory($devel_data = false)
	{

		$class = 'coreModel/'. DB_ENGINE . '/'. ucfirst(DB_ENGINE) ."Connection";
		Cogumelo::load($class.'.php');
		
		$dbObj = ucfirst(DB_ENGINE)."Connection";

    if( !$devel_data ) {

      static $cogumelo_connection_instance = null;
      if (null === $cogumelo_connection_instance) {
          $cogumelo_connection_instance = new $dbObj();
      }
    }
    else {
      $cogumelo_connection_instance = new $dbObj( $devel_data );
    }

    return $cogumelo_connection_instance;
	}
}
