<?php

Cogumelo::load('c_model/VO');
//testmodule::load('model/ComplementoVO');


class CousaVO extends VO
{
  static $tableName = 'cousa';
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
    ),
    'hobby'=> array(
      'desc' => 'Afición',
      'type'=>'VARCHAR',
      'size'=>30
    )/*,
    'complemento' => array(
      'desc' => 'Complemento',
      'type'=>'FOREIGN',
      'vo' => 'ComplementoVO',
      'vo_key' => 'id'
    )*/
  );



  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }



}