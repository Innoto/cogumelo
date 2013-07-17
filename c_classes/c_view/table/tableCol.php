<?php

abstract class tableCol {
	protected $id;
	protected $name;

	function __construct($id, $name=false){

		$this->id=$id;
		$this->name = $name;

	}

	function setName($name) {
		$this->name = $name;
	}


	function getName(){
		return $this->name;
	}

	function setId($id) {
		$this->id = $id;
	}

	function getId() {
		return $this->id;
	}

	function getCol($vo, $table){
		if(method_exists($this, 'processCol'))
			return $this->processCol($vo, $table);
		else
			return $vo->getter($this->id);
	}

	abstract function processCol($col, $table);

}