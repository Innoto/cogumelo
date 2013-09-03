<?php

Cogumelo::load('c_view/View');
Cogumelo::load('controllers/data/FromVOtoDBController');


class DevView extends View
{

	function __construct($base_dir){
		parent::__construct($base_dir);
	}

	function accessCheck() {

		if($_SERVER["REMOTE_ADDR"] != "127.0.0.1"){
			Cogumelo::error("Must be developer machine to enter on this site");
			return false;
		}
		else
			return true;
	}

	function main($url_path=''){
		$fvotodb = new FromVOtoDBController();

		echo "<pre>";

		var_dump( $fvotodb->getTablesSQL() );
		$fvotodb->createTables();
	}




}

