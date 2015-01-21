<?php


/**
* DataController Class
*
* abstract DataController class
*
* @author: pablinhob
*/


Cogumelo::load("coreModel/Facade.php");


abstract class DataController {

	var $voClass;

	// query parameters
	private $range = array();
	private $order = array();
	private $filters = array();


	function __construct() {

	}


  /*
  * @param mixed $id identifier
  * @param string $key vo key to set at id (false is VO primary key)
  */
	function find($value, $key=false)
	{
		Cogumelo::debug( "Called find on ".get_called_class()." with id=". $value);
		$data = $this->data->Find($value, $key);

		return $data;
	}


  /*
  *	List items from table
  *
  * @param array $filters array of filters
  * @param array $range two element array with result range ex. array(0,100)
  * @param array $order order for query
  * @apram boolean $cache true means cache is enabled
  */
	function listItems($filters = false, $range = false, $order = false, $fields = false, $resolveDependences = false, $cache = false)
	{

		Cogumelo::debug( "Called listItems on ".get_called_class() );
		$data = $this->data->listItems($filters, $range, $order, $fields, $resolveDependences, $cache);

		return $data;
	}


  /*
  *	Count items from table
  *
  * @param array $filters array of filters
	*/
	function listCount($filters = false)
	{

		Cogumelo::debug( "Called listCount on ".get_called_class() );
		$data = $this->data->listCount($filters);

		return $data;
	}


  /*
  *	create item
  *
  * @param mixed $data be (array) or (VO object)
  */
	function create($data)
	{
		Cogumelo::debug( "Called create on ".get_called_class() );
		if(!is_object($data)){
			$data = new $this->voClass($data);
    }

		$data = $this->data->Create($data);

		return $data;
	}

  /*
  * create item
  *
  * @param mixed $data is a array
  */
  function createFromArray($data){
    Cogumelo::debug( "Called create on ".get_called_class() );

    $data = new $this->voClass($data);
    $data = $this->data->Create($data);

    return $data;
  }

  /*
  *	update item
  *
  * @param mixed $data can be array or VO object
  */
	function update($data)
	{
		Cogumelo::debug( "Called update on ".get_called_class() );
		if(!is_object($data)){
			$data = new $this->voClass($data);
    }

		$data = $this->data->Update($data);

		return $data;
	}

  /*
  * update item
  *
  * @param mixed $data is a array
  */
  function updateFromArray($data)
  {
    Cogumelo::debug( "Called update on ".get_called_class() );
    $data = new $this->voClass($data);
    $data = $this->data->Update($data);

    return $data;
  }


  /*
  *	delete items
  *
  * @param mixed $id must be primary key of VO
  */
	function deleteFromIds($arrayIds)
	{
		Cogumelo::debug( "Called delete on ".get_called_class()." with ids=". implode(",", $arrayIds) );
		$data = $this->data->deleteFromIds($arrayIds);

    return $data;
	}

  /*
  * delete item
  *
  * @param mixed $id must be primary key of VO
  */

  function deleteFromId($id){
    Cogumelo::debug( "Called delete on ".get_called_class()." with id=".$id );

    $arrayIds = array($id);
    $data = $this->data->deleteFromIds($arrayIds);

    return $data;
  }

  /*
  * delete items
  *
  * @param mixed $VOarray from listItems
  */

  function deleteFromList($arrayVoList){

    $arrayIds = array();

    foreach ($arrayVoList as $voKey => $voItem) {
      $primarykeyIdName = $voItem->getFirstPrimarykeyId();
      $primarykeyIdValue = $voItem->getter($primarykeyIdName);
      array_push($arrayIds, $primarykeyIdValue);
    }

    $data = $this->deleteFromIds($arrayIds);

    return $data;
  }
}