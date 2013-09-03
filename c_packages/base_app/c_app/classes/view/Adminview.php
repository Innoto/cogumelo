<?php


Cogumelo::load('c_view/View');
Cogumelo::load('controller/UseradminController');


class Adminview extends View
{

	function __construct($base_dir){
		parent::__construct($base_dir);
	}

	function accessCheck() {
		return true;
	}


	function seccion($url_path=''){
		$this->common();
		$this->template->exec();
	}

	function common() {
		$this->template->setTpl("testpage.tpl");
		//$this->template->addJs("vendor/jquery.js", "basics");
		//$this->template->addJs("vendor/jquery.js");
	}


	function page404() {
		echo "Recurso non atopado";
	}
}

