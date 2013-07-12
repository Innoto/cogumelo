<?php

Cogumelo::load("c_controllers/module/Module");

class mediaserver extends Module
{

	var $url_patterns = array(
		'#^media/module(.*)#' => 'view:MediaserverView::module',			
		'#^media(.*)#' => 'view:MediaserverView::application'
	);
}