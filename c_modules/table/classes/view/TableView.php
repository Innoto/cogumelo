<?php

class TableView {


  static function getTableHtml( $tableId, $tableDataUrl ) {

  $tableHtml = '


  <script>
    $(function() {
      if( typeof pageTables == "undefined"){
        var cogumeloTables = {};
      }

      cogumeloTables.'. $tableId .' = new cogumeloTable("'. $tableId . '", "' . $tableDataUrl .'");
    });
  </script>

  <!-- HTML TABLE id: "' . $tableId . '" data url: "' . $tableDataUrl . '" -->
  <div class="' . $tableId . ' tableContainer">
    <div class="tableHeaderContainer">
      <div class="tableSearchFilters clearfix">

        <div class="tableFilters clearfix">
          <select>

          </select>
          <button class="openFilters">+Filtros</button>
        </div>
        <div class="tableSearch clearfix">
          <input type="text"><button>Search</button>
        </div>
      </div>
      <div class="tableMoreFilters clearfix" style="display:none;">
        <div class="MoreFilters" >
          <div class="clearfix">
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>

              <div class="FilterMain">
                <label>Categoria</label>
                <select>
                  <option value="1">Categoria 1</option>
                  <option value="2">Categoria 2</option>
                  <option value="3">Categoria 3</option>
                </select>
                <div class="FilterMain">
                  <label>Categoria</label>
                  <select>
                    <option value="1">Categoria 1</option>
                    <option value="2">Categoria 2</option>
                    <option value="3">Categoria 3</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">Categoria 1</option>
                <option value="2">Categoria 2</option>
                <option value="3">Categoria 3</option>
              </select>
            </div>
          </div>

          <div class="buttonsContainer">
            <button class="clearFilters">Eliminar filtros</button>
            <button class="closeFilters">Cerrar</button>
          </div>
        </div>
      </div>
      <div class="tableResumeFilters clearfix">
        <span>Estas filtrando por: (Categoria 1, Aceptado).</span>
        <button class="clearFilters">Eliminar filtros</button>
      </div>
      <div class="tableActions clearfix">
        <div class="addElem"><img src="media/module/table/img/add.png" alt="Add"></div>
        <div class="exportContainer"><img src="media/module/table/img/export.png" alt="Export"></div>
        <select class="actionSelect">
          <option value="0">Acciones</option>
          <option value="delete">Borrar</option>
          <option value="move1">Mover a (A Coru&ntilde;a)</option>
          <option value="move2">Mover a (A Lugo)</option>                    
        </select>
        <!-- Paginador -->
        <div class="tablePaginator">
          <div class="tablePage"><input type="text" value="1"> de <span class="totalPages">1</span></div>
          <div class="tablePreviousPage"><img src="media/module/table/img/a-left.png" alt="previous page"></div>
          <div class="tableNextPage"><img src="media/module/table/img/a-right.png" alt="next page"></div>
        </div>
      </div>

    </div>
    <table class="tableClass clearfix">


    </table>
    <!-- Paginador -->
    <div class="tablePaginator ">
      <div class="tablePage"><input type="text" value="1"> de <span class="totalPages">1</span></div>
      <div class="tablePreviousPage"><img src="media/module/table/img/a-left.png" alt="previous page"></div>
      <div class="tableNextPage"><img src="media/module/table/img/a-right.png" alt="next page"></div>
    </div>
  </div>
  <!-- END HTML TABLE id: "' . $tableId . '" data url: "' . $tableDataUrl . '" -->

    ';

    return $tableHtml;
  }


}