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
    $this->filters = self::filtersAsMysql( $voObj::getFilters() );
  }

  /**
   * Sets MysqlDAO filters
   *
   * @param array $filters
   *
   * @return void
   */
  static function filtersAsMysql( $filters ) {
    // process here filters format if needed
    return  $filters;
  }

}