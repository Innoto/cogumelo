<?php
Cogumelo::load('coreView/View.php');
common::autoIncludes();

class Tview extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  /**
  * Evaluate the access conditions and report if can continue
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

    table::autoIncludes();


    Cogumelo::load('controller/LostController.php');
    $lostControl =  new LostController();

    // creamos obxecto taboa
    $tabla = new TableController( $lostControl );

    // establecemos pestañas, así como o key identificativo á hora de filtrar
    $tabla->setTabs('lostProvince', array('1'=>'A Coruña', '2'=>'Lugo', '*'=> 'Toda Galicia' ), '*');


    // set id search reference.
    $tabla->setSearchRefId('tableSearch');

    // set table Actions
    $tabla->setActionMethod('Borrar', 'delete', 'deleteFromId($rowId)');
    $tabla->setActionMethod('Mover a Lla Coruña', 'moveToAcoruna', 'updateFromArray( array($primaryKey=>$rowId,  "lostProvince"=>1) )');
    $tabla->setActionMethod('Mover a Lugo', 'moveToLugo', 'updateFromArray( array($primaryKey=>$rowId,  "lostProvince"=>2) )');

    // set list Count methods in controller
    $tabla->setListMethodAlias('listItems');
    $tabla->setCountMethodAlias('listCount');


    // set Urls
    $tabla->setEachRowUrl('"/tablaformulario/".$rowId');
    $tabla->setNewItemUrl('/tablanuevo/');

    // Nome das columnas
    //$tabla->setCol('id', 'Id');
    $tabla->setCol('lostSurname', 'Apelido');
    $tabla->setCol('lostName', 'Nome');
    $tabla->setCol('lostMail', 'Correo');
    $tabla->setCol('lostProvince', 'Provincia');
    $tabla->setCol('lostPhone', 'Teléfono');

    // establecer reglas a campo concreto con expresions regulares
    $tabla->colRule('lostProvince', '#1#', 'A Coruña');
    $tabla->colRule('lostProvince', '#2#', 'Lugo');

    // imprimimos o JSON da taboa
    $tabla->exec();
    


  } // function loadForm()
}





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