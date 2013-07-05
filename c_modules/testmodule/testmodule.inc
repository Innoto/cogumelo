<?php

Cogumelo::load("c_controllers/module/Module");

class testmodule extends Module
{

	var $url_patterns = array(
		'#^testmodule#' => 'view:TestmoduleView::inicio',
	);
}