<?php

Cogumelo::load('coreModel/mysql/MysqlDAO.php');


/**
 * Generic DAO autogenerator
 *
 * @package Cogumelo Model
 */
class MysqlAutogeneratorDAO extends MysqlDAO
{

  /**
   *
   * @param object $voObj VO or Model to generate MysqlDAO
   *
   * @return void
   */
  function __construct($voObj) {
    
    $this->VO = $voObj->getVOClassName();
    $this->setFilters( $voObj->getFilters() );
  }

  /**
   * Sets MysqlDAO filters
   *
   * @param array $filters
   *
   * @return void
   */
  function setFilters( $filters ) {
    // process here filters format if needed
    $this->filters = $filters;
  }

}