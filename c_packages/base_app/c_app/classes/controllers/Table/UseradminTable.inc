<?php
/*
Cogumelo v0.3 - Map-Experience
Copyright (C) 2012 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
USA.
*/

//
// NOTA: Este arquivo só se modificou en parte para a revisión 0.2 de Cogumelo.
//

app::Load('UseradminController');

Cogumelo::Load('Table');


//
//	Useradmin Table Class
//

Class UseradminTable extends Table
{
	public $id;
	public $PageRows;
	public $head;
	private $useradminControl;
	public $options;
	
	const session_id = 'useradmintab';
	
	function __construct()
	{
		$this->id= 'useradminTable';
		$this->PageRows = 25;
		$this->status = 1;
		
		// table options when a property is disabled we set it as (false)
		$this->options['multiselect'] = true;
		$this->options['showID'] = true;
		$this->options['find'] = false;
		$this->options['onloadForm'] = true;
		$this->options['tabs'] = Array(
			activos => Array(
					'id'=> '1', 
					'name'=>'Usuarios administradores', 
					'commands'=> Array(
							'destroy'=> Array('id'=> 'destroy', 'name'=> 'Borrar', 'command'=> 'setasdestroy')
					)
			)
		);
		
		
		
		$this->head = array(
				'id' => 'Id',
				'name' => 'nombre',
				'login' => 'Login'							
		);

		$this->useradminControl = new UseradminController();
	
		parent::__construct();
	}
	
	function GetData()
	{
		if( $this->page == 0) $this->page =1;
		
		$range[0] = ($this->page - 1) * $this->PageRows;
		$range[1] = $this->PageRows;

		$TMProws = null;
		$count = 0;
		$page = $this->useradminControl->ListUseradmins($range, $this->order , $this->GetFilters());

		
		if( $this->useradminControl->ListUseradminsCount( $this->GetFilters() ) != 0  && $page != null)
		{
			foreach($page as $row){
				$TMProws[$count]['id'] = $row->getter('id');
				$TMProws[$count]['name'] = $row->getter('name');
				$TMProws[$count]['login'] = $row->getter('login');							
				$count++;
			}
		}
		return($TMProws);
	}
	
	function GetPages()
	{
		
		return ceil($this->useradminControl->ListUseradminsCount( $this->GetFilters() ) / $this->PageRows);
	}
	
	function GetTotalregs()
	{	
		return $this->useradminControl->ListUseradminsCount( $this->GetFilters() );
	}
	
	function tabSet($tab=null)
	{
			
	}
	
	function GetFilters()
	{
		$filters['find'] = $this->filterString;
		$filters['status'] = $this->status;
		return $filters;
	}
	
	
	///
	///	Actions
	///	
	function CustomCommand($cmd)
	{
		global $getData;
		
		$rowsArray = $this->getSelectedRows($getData['items']);
		
		foreach($rowsArray as $row)
		{
			if($row && $row!=0)
			{
				switch($cmd)
				{									
					case 'setasdestroy':
						
						$this->useradminControl->Destroy($row);			
						break;
					default: exit;								
				}
			}
		}
	}
	
	
}
?>
