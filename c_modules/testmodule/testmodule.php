<?php

Cogumelo::load("c_controllers/module/Module");

class testmodule extends Module
{

	var $url_patterns = array(
					'#^cousa/mostrar\/?(.*)$#' => 'view:Cousadmin::mostra_cousa',
			'#^cousa/crear$#' => 'view:Cousadmin::crea',
			'#^cousa$#' => 'view:Cousadmin::lista',
		'#^testmodule#' => 'view:TestmoduleView::inicio',
	);
}