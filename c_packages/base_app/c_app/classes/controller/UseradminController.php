<?php

Cogumelo::load('c_controller/DataController');
Cogumelo::load('model/UseradminVO');


//
// Useradmin Controller Class
//
class  UseradminController extends DataController
{
	var $data;

	function __construct()
	{	
		$this->data = new Facade("Useradmin");
		$this->voClass = 'UseradminVO';
	}
	
	//
	//	Update Useradmin passwd.
	//
	function UpdatePasswd($id, $password)		
	{
/*	  	$data = $this->data->Updatepass($id, $password);
	  	if($data) Cogumelo::Log(__METHOD__." SUCCEED with ID=".$id, 3);
		else Cogumelo::Log(__METHOD__." FAILED with ID=".$id, 3);
		
		return $data;*/
	}
	
	function AuthenticateUseradmin($useradmin)
	{		
/*		$data = $this->data->AuthenticateUseradmin($useradmin);
		
		if($data) Cogumelo::Log(__METHOD__." SUCCEED with login=".$useradmin->getter('login'), 3);
		else Cogumelo::Log(__METHOD__." FAILED with login=".$useradmin->getter('login').". Useradmin NOT authenticated", 3);
		
		return $data;*/
	}
}
