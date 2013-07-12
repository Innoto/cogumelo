<?php
/*
Cogumelo v0.2 - Innoto S.L.
Copyright (C) 2010 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

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
// NOTA: Este ficheiro só foi parcialmente revisado para a revisión 0.2 de Cogumelo
//


//	Table Class
//	(Creates an object table to print and use by ajax)
//

class Table
{
	public $id; //table ID
	const session_id = 'table_session_id';

	public $order;
	public $pages;
	public $page;
	public $rows;
	
	public $filterString;
	public $comboboxes;
	
	protected function __construct()
	{
			$this->filterString = false;
			$this->comboboxes = false;
			$this->page = 1;
			$this->order = array();
			if(method_exists( $this, 'setComboboxes' ))
				$this->setComboboxes();
	}
	
	//
	// save to session when the object is destructed
	//
	function __destruct()
	{
		$_SESSION[$this::session_id] = serialize($this);
	}
	
	//
	// Recover Table from session or initialize one
	//
	static function Factory()
	{
		$class = get_called_class();
		
		if( !class_exists($class) )
			Cogumelo::Error("Attepping to initialize a non-defined class");
		
		// if session is set and is unserializable return it
		if( isset($_SESSION[$class::session_id]) )
		{
			$old = @ unserialize($_SESSION[$class::session_id]);
			if( ! $old instanceof $class ) Cogumelo::ServerError("Table with class '$class' appears to have it's session corrupted.");
			return $old;
		}
		
		// else return a new instance, doing magic with args to pass them to the constructor ;)
		$source = "return new $class(";
		foreach(func_get_args() as $n => $v)
			$source .= ( ($n==0)? '' : ', ' ) . ( (is_array($v) || is_object($v) )? var_export($v, true) : $v );
		return eval($source.");");
	}
	
	//
	//	Load Data
	//	(Load data into Table Array)		
	//
	function Load($findTerm)
	{
		if($findTerm != null)
		{
			if($findTerm == "NOTHING")
			{
				$this->filterString = "";
			}
			else
				$this->filterString = $findTerm;
		}	

		
		$this->rows = $this->GetData();
		
		// Set pages number
		$this->pages = $this->GetPages();
		
		if ($this->page > $this->pages)
			$this->page = $this->pages;
		
	}

	//
	//	Get Json Data
	//	(return all table data json format)		
	//
	function GetJSON()
	{
		$dataArray['Id'] = $this->id;
		$dataArray['Info'] = $this->GetInfo();
		$dataArray['filters'] = $this->GetFilters(); 
		$dataArray['headers'] = $this->GetHead();
		$dataArray['options'] = $this->options; 
		$dataArray['Data'] = $this->GetRows();

		return json_encode($dataArray);
		
	}
	
	//
	//	Get Data
	//	(return page data in array)		
	//
	function GetRows()
	{
			return $this->rows;
	}
	
	//
	//	Get Table info
	//	(return table info in array or json format)		
	//
	function GetInfo()
	{
		// make array with page info
		$infoArray = array(
				'page' => $this->page,
				'pages' => $this->pages,
				'totalregs' => $this->GetTotalregs()
		);
		
		return $infoArray;
	}
	
	//
	//	Get Table Headers Info
	//	
	//
	function GetHEad()
	{
		$headinfo = null;
		

		foreach($this->head as $key => $item)
		{
			$headinfo[$key]['id'] = $key;
			$headinfo[$key]['name'] = $item;
			$headinfo[$key]['order'] = $this->GetColOrder($key);
		}
		
		return $headinfo;
	}
	
	//
	//	Page Set
	//	(Set page)
	//
	function PageSet($page)
	{
		if($page > 0 && $page <= $this->pages)
			$this->page = $page;
			
		// Load table data
		$this->Load(null);
	}
	
	//
	//	Page Next
	//	(Set next page)
	//
	function PageNext()
	{
		if($this->page < $this->pages)
			++$this->page;

		// Load table data
		$this->Load(null);
	}
	
	//
	//	Page Prev
	//	(Set previous page)
	//
	function PagePrev()
	{
		if($this->page > 1)
			--$this->page;
			
		// Load table data
		$this->Load(null);
	}
	
	//
	//	Get Column order
	//	(return column order)
	//
	function GetColOrder($col)
	{
		// if order is not set
		if( !isset($this->order[$col]) )
		{
			$this->order[$col] = -1;
		}

		return $this->order[$col] ;
	
	}

	//
	//	Set Column order
	//	(column order)
	//
	function ChangeColOrder($col)
	{
	
		// if order is not set
		if(!$this->order[$col])
		{

			$order = -1;
		}
		
		// change value
		if ($this->order[$col] == -1)
			$order = 1;
		else
			$order = -1;
			
		//first on order array

		$swORD = $this->order;
		$this->order = null;
		$this->order[$col] = $order;
		
		foreach($swORD as $orK => $orV)
		{
				if($orK != $col)
					$this->order[$orK] = $orV; 
		}

		// Load table data
		$this->Load(null);
	}
	
	
	//
	//	To get data from table (Used by ajax)
	//
	function TableCommand($command = null)
	{
		global $getData;
		
		switch($command)
		{
			case 'getdata':
				$this->Load(null);
				die( $this->GetJSON() );
				break;
			case 'pageset':
				$this->PageSet($getData['page']);
				die( $this->GetJSON() );
				break;
			case 'pagenext':
				$this->PageNext();
				die( $this->GetJSON() );
				break;
			case 'pageprev':
				$this->PagePrev();
				die( $this->GetJSON() );
				break;
			case 'orderby':
				$this->ChangeColOrder($getData['headerid']);
				die( $this->GetJSON() );
				break;
			case 'find':
				if($getData['term'] == null) $term="NOTHING";
				else $term=$getData['term'];
				$this->Load($term);
				die( $this->GetJSON() );
				break;
			case 'setcomboboxes':
				if($this->comboboxes)
					$this->setComboboxes($getData['comboboxid'], $getData['value']);
				$this->Load(null);
				die( $this->GetJSON() );
				break;
			case 'tabSet':
				$this->tabSet($getData['tab']);
				$this->Load(null);
				die( $this->GetJSON() );
				break;
			default:
				$this->CustomCommand($command);
				$this->Load(null);
				die( $this->GetJSON() );
				break;
		}
	}
	
	function getSelectedRows($selRows)
	{
		
		return explode(',',$selRows);
	}
	
}

class TableFixCommand{
	public $icon;
	public $url;
	public $title;	
	
	function __construct( $url = "", $icon = "", $title = "")
	{
		$this->icon = $icon;
		$this->url = $url;
		$this->title = $title;
	}

}
?>
