<?php
Cogumelo::load('c_view/View.php');
common::autoIncludes();

class Tview extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  function main(){
    /*$this->template->addClientStyles('styles/table.less');
    $this->template->addClientScript('js/table.js');*/
    table::autoIncludes();
    $this->template->setTpl('paxinaTabla.tpl');
    $this->template->assign('codigoTabla', table::getTableHtml('tview', '/tableinterfacedata') );
    $this->template->exec();
  }

  function tableData() {
    /*$this->template->addClientStyles('styles/table.less');
    $this->template->addClientScript('js/table.js');
    $this->template->setTpl('table.tpl');
    $this->template->exec();*/


    table::autoIncludes();

//var_dump($_POST);
    // POST DE PEGA
    $_POST['method'] = array('name' => 'list', 'value', false);
  //  $_POST['tab'] = false;
    $_POST['filters'] = array();
    //$_POST['range'] = array(0,50);
    //$_POST['order'];
/*
    $_POST['cogumeloTable'] = '{'.
      '  "method":{ "name" : "list", "value": false},' .
      '  "filters": [],' .
      '  "range": [ 0, 50 ],' .
      '  "order": [{"key": "id", "value": -1}, {"key": "lostName", "value": 1 }] '.
      '}';
*/

    Cogumelo::load('controller/LostController.php');
    $lostControl =  new LostController();

    // creamos obxecto taboa pasandolle o POST
    $tabla = new TableController( $lostControl, $_POST );

    // establecemos pestañas, así como o key identificativo á hora de filtrar
    $tabla->setTabs('lostProvince', array('1'=>'A Coruña', '2'=>'Lugo'), '2' );


    // establecemos os table filters 

   /* $tabla->setFilters(
      array()
    );*/

/*    
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
                  3 => 'Elemento 3'
                )
              ),
              4 => 'Elemento 4'
          )
        )
      )
    );
*/

    

    // Nome das columnas
    $tabla->setCol('id', 'Id');
    $tabla->setCol('lostName', 'Nome');
    $tabla->setCol('lostSurname', 'Apelido');
    $tabla->setCol('lostMail', 'Correo');
    $tabla->setCol('lostProvince', 'Provincia');
    $tabla->setCol('lostPhone', 'Teléfono');

    // establecer reglas a campo concreto con expresions regulares
    $tabla->colRule('lostProvince', '#1#', 'A Coruña');
    $tabla->colRule('lostProvince', '#2#', 'Lugo');


    // imprimimos o JSON da taboa
    $tabla->returnTableJson();
    


  } // function loadForm()
}

