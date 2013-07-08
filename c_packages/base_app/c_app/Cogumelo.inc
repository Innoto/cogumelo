<?php


class Cogumelo extends CogumeloClass
{	
	
	function __construct()
	{
		parent::__construct();
	}
	
	protected function setUrlPatterns(){
		return  array(

			'#^admin(.*)$#' => 'view:Adminview::metodo',
			'#^dev$#' => 'view:DevView::main',
			'#^404$#' => 'view:Adminview::page404',
			// default views
			'#^$#' => 'view:Adminview::seccion' // App home url
		);
	}
}	
