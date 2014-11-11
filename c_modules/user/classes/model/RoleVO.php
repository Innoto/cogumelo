<?php

Cogumelo::load('c_model/VO.php');

class RoleVO extends VO
{
  static $tableName = 'role';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'name' => array(
      'name' => 'Name',
      'type' => 'CHAR',
      'size' => '10'
    ),
    'description'=> array(
      'name' => 'Descripción',
      'type' => 'TEXT',
      'size' => '300'
    )
  );

  static $insertValues = array(
    array('name' => 'superAdmin', 'description' => 'SuperAdmin'),
    array('name' => 'user', 'description' => 'User'),
    array('name' => 'admin', 'description' => 'Admin'),
    array('name' => 'gestor', 'description' => 'Gestor'),
    array('name' => 'editor', 'description' => 'Editor'),

  );

  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}