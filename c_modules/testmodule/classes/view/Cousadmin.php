<?php


Cogumelo::load('c_view/View.php');
Cogumelo::load('c_view/Table.php');
testmodule::load('controller/CousaController.php');


class Cousadmin extends View
{

  var $cousacontrol;
  function __construct($base_dir){
    parent::__construct($base_dir);

    $this->cousacontrol = new CousaController();
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }


  function lista_plana() {
    echo "<pre>";
    $cousas = $this->cousacontrol->listItems(false, false, false, true);

    while($cou = $cousas->fetch()) {

      echo "<br>";
      var_dump($cou);
      var_dump($cou->getter('complemento.name'));
    }

  }


  function panel_admin() {



      $this->template->setTpl("cousadmin.tpl", "testmodule");
      $this->template->addClientScript('vendor_lib/jQuery.js', 'client');
      $this->template->addClientScript('vendor_lib/Class.js', 'client');
      $this->template->addClientScript('vendor_lib/jquery.address.js', 'client');
      $this->template->addClientScript('lib/cogumelo.table.js', 'client');
      $this->template->exec();


  }

  //
  // Actions
  //

  function mostra_cousa($url = false) {

  var_dump($this->cousacontrol->find($url) );

  }

  function crea() {

    $novacousa = array('name'=> 'Cousa Adams', 'fingers' => 5,'hobby' => 'tocar o piano');

    $this->cousacontrol->create($novacousa);

    echo "Creado nova entrada para cousa";
  }



  function vo($url = false) {

    testmodule::load('model/CousaVO');

    $vo = new CousaVO();
    //Cogumelo::objDebug($vo);
    echo($vo->keysToString(false));
    echo "<pre>";
    var_dump( $vo->getJoinArray() );
  }


  //
  //  Tablas
  //


  function cousa_tabla() {

    $FAKE_POST = array('cogumelo_table' => '{"filters":[],"range":[0,20], "order":[], "method":false}' );


    // creamos obxecto taboa pasandolle o POST
    $tabla = new Table($FAKE_POST);

    // establecemos pestañas, así como o key identificativo á hora de filtrar
    $tabla->setTabs('estado', array('1'=>'Activos', '2'=>'Papelera') );


    // establecemos os table filters
    $tabla->setFilters(
        array(
            array('id'=> 'buscar', 'desc'=>'Búsqueda de cousas', 'type'=>'search', 'default'=> false),
            array('id'=> 'categoria', 'desc'=>'Categorías', 'type'=>'list', 'default'=> 5,
                'list' => array(
                  1 => 'Elemento 1',
                  2 => 'Elemento 2',
                  3 => array('list_name'=>'Elemento 3', 'id'=> 'subcategoria', 'desc'=>'Subcategorías', 'type'=>'list',
                      'list' => array(
                        1 => 'Elemento 1',
                        2 => 'Elemento 2',
                        3 => 'elemento 3'
                      )
                  ),
                  4 => 'Elemento 4'
                )
              )
          )
    );



    // Nome das columnas
    $tabla->setCol('id', 'Id');
    $tabla->setCol('name', 'Nome da cousa');
    $tabla->setCol('fingers', "Númerod de dedos");
    $tabla->setCol('nivel', "Nivel");

    // establecer reglas a campo concreto con expresions regulares
    $tabla->colRule('nivel', '#^#', 'Sen nivel asignado');
    //$tabla->colRule('id', '#^[5..10]%#', 'Usuario medio');
    //$tabla->colRule('id', '#^[i..4]%#', 'Usuario cutre');

    // metodos aceptados
    $tabla->allowMethods(array('delete', 'update'));

    // imprimimos o JSON da taboa
    $tabla->return_table_json($this->cousacontrol);

  }


}