<?php

Cogumelo::load('c_model/VO');


class ComplementoVO extends VO
{	
	static $tableName = 'complemento';
	static $cols = array(
		'id' => array(
			'type'=>'INT', 
			'primarykey'=>true,
			'autoincrement'=>true
		),
		'name' => array(
			'desc' => 'Nome', 
			'type'=>'VARCHAR', 
			'size'=>30
		),
		'fingers'=> array(
			'desc' => 'Número de dedos', 
			'type'=>'INT'
		)
	);



	function __construct($datarray = array())
	{
		parent::__construct($datarray);
	}
	


}