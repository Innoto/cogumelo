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

	function setObj() {
		echo "SET OBJ<br>";

		$objeto = array("id"=>100, "name"=>"Pablo");
		Cogumelo::objDebugPush($objeto);
	}

	function getObj() {
		echo "GET OBJ<br>";
		echo "<pre>";
		var_dump(Cogumelo::objDebugPull() );
	}


	function page404() {
		//echo "Recurso non atopado";
	}
}

