<?php

class TableView {


  static function getTableHtml( $tableId, $tableDataUrl ) {

  $tableHtml = '


  <script>

    var cogumeloTables = cogumeloTables || {};
    $(function() {
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
          <button class="openFilters">+'.__("Filters").'</button>
        </div>
        <div class="tableSearch clearfix">
          <form  onsubmit="return false;"><input type="text"><button type="button" class="clear" style="display:none;">X</button><button class="search" type="submit">'.__("Search").'</button></form>
        </div>
      </div>
      <div class="tableMoreFilters clearfix" style="display:none;">
        <div class="MoreFilters" >
          <div class="clearfix">
            <div class="FilterMain">
              <label>Categoria</label>
              <select>
                <option value="1">'.__("Error loading filters").'</option>
              </select>
            </div>

          </div>

          <div class="buttonsContainer">
            <button class="clearFilters">'.__("Clear filters").'</button>
            <button class="closeFilters">'.__("Close").'</button>
          </div>
        </div>
      </div>
      <div class="tableResumeFilters clearfix">
        <div>'.__("You are filtering by").':
          <span class="filterValues">'.__("(Resource type, Routes).").'</span>
          <button class="clearFilters">'.__("Clear filters").'</button>
        </div>
      </div>
      <div class="tableActions clearfix">
        <div class="addElem"><img src="'.Cogumelo::getSetupValue('publicConf:vars:media').'/module/table/img/add.png" alt="Add"></div>
        <div class="exportContainer">
          <select class="exportSelect">

          </select>
        </div>
        <select class="actionSelect">

        </select>
        <!-- Paginador -->
        <div class="tablePaginator">
          <div class="totalElements">
            <span class="totalRows">0</span> elements
          </div>
          <div class="tablePage">
            <span>Page: <input type="text" value="1"> of <span class="totalPages">1</span></span>
          </div>
          <div class="tablePreviousPage"><img src="'.Cogumelo::getSetupValue('publicConf:vars:media').'/module/table/img/a-left.png" alt="previous page"></div>
          <div class="tableNextPage"><img src="'.Cogumelo::getSetupValue('publicConf:vars:media').'/module/table/img/a-right.png" alt="next page"></div>
        </div>
      </div>

    </div>
    <table class="tableClass clearfix">


    </table>
    <!-- Paginador -->
    <div class="tablePaginator ">
      <div class="totalElements">
        <span class="totalRows">0</span> elements
      </div>
      <div class="tablePage">
        <span>Page: <input type="text" value="1"> of <span class="totalPages">1</span></span>
      </div>
      <div class="tablePreviousPage"><img src="'.Cogumelo::getSetupValue('publicConf:vars:media').'/module/table/img/a-left.png" alt="previous page"></div>
      <div class="tableNextPage"><img src="'.Cogumelo::getSetupValue('publicConf:vars:media').'/module/table/img/a-right.png" alt="next page"></div>
    </div>
  </div>
  <!-- END HTML TABLE id: "' . $tableId . '" data url: "' . $tableDataUrl . '" -->

    ';

    return $tableHtml;
  }


}
