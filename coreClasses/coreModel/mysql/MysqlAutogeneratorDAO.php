<?php

Cogumelo::load('coreModel/mysql/MysqlDAO.php');

class MysqlAutogeneratorDAO extends MysqlDAO
{
  function __construct($voObj) {
  	
  	$this->VO = $voObj->getVOClassName();
  	$this->setFilters( $voObj->getFilters() );
  }

  function setFilters( $filters ) {
	$this->filters = $filters;
  }

}