<?php


class Cogumelo extends CogumeloClass
{	
	
	function __construct()
	{
		parent::__construct();

	}
	
	var $url_patterns = array(
		/*
		'#^cousa/mostrar\/?(.*)$#' => 'view:Cousadmin::mostra_cousa',
		'#^cousa/crear$#' => 'view:Cousadmin::crea',
		'#^cousa$#' => 'view:Cousadmin::lista',
*/
		'#^admin\/?(.*)$#' => 'view:Adminview::metodo',
		//'#^dev$#' => 'view:DevView::main',
		'#^404$#' => 'view:Adminview::page404',
		// default views

		'#^getobj$#' => 'view:Adminview::getobj',
		'#^setobj$#' => 'view:Adminview::setobj',
		'#^createdb$#' => 'view:Adminview::create_user_db_tables',
		'#^$#' => 'view:Adminview::seccion' // App home url
	);

}	
