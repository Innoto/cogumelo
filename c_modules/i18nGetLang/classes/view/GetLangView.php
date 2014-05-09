<?php

Cogumelo::load('c_view/View');

class GetLangView extends View
{

	function __construct($base_dir){
		parent::__construct($base_dir);
	}

	function accessCheck() {
		return true;
	}

	// load media from app
	function setlang($url_path=''){
		echo "<br> SET Lang global variables<br><br>";
	}
}