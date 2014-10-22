<!DOCTYPE html>
<html>
  <head>
    <title>FORMs con Cogumelo</title>

    {$css_includes}

    <link rel="stylesheet/less" type="text/css" href="/styles/table.less">

    <script>
      less = {
        env: "development",
        async: false,
        fileAsync: false,
        poll: 1000,
        functions: { },
        dumpLineNumbers: "all",
        relativeUrls: true,
        errorReporting: 'console'
      };
    </script>

    {$js_includes}

  </head>
  <body>

  <!-- --------- HTML TABLE ------------- -->
    <div class="tableHeaderContainer">
      <div class="tableSearch clearfix">
        <input type="text"><button>Search</button>
      </div>
      <div class="tableFilters clearfix">
        <select>
          <option value="1">Aceptado</option>
          <option value="2">Recibido</option>
          <option value="3">Rechazado</option>
        </select>
        <div class="tableMoreFilters">
            <div class="filters">
              Filters
            </div>
            <div class="MoreFilters" style="display:none;">
              <select>
                <option value="1">Aceptado</option>
                <option value="2">Recibido</option>
                <option value="3">Rechazado</option>
              </select>
              <select>
                <option value="1">Aceptado</option>
                <option value="2">Recibido</option>
                <option value="3">Rechazado</option>
              </select>
              <select>
                <option value="1">Aceptado</option>
                <option value="2">Recibido</option>
                <option value="3">Rechazado</option>
              </select>
              <button>Apply filters</button>
            </div>
        </div>
      </div>
      <div class="tableActions clearfix">
        <div class="selectAll"><input type="checkbox"></div>
        <div class="addElem"><img src="/img/table/add.png" alt="Add"></div>
        <div class="exportContainer"><img src="/img/table/export.png" alt="Export"></div>
        <select>
          <option value="1">Action 1</option>
          <option value="2">Action 2</option>
          <option value="3">Action 3</option>
        </select>
        <!-- Paginador -->
        <div class="tablePaginator">
          <div class="tablePage"><input type="text" value="100"> de 219</div>
          <div class="tablePreviousPage"><img src="/img/table/a-left.png" alt="previous page"></div>
          <div class="tableNextPage"><img src="/img/table/a-right.png" alt="next page"></div>
        </div>
      </div>

    </div>
    <table class="tableClass clearfix">
      <tr>
        <th></th>
        <th>Name <img src="/img/table/up.png"></th>
        <th>Description <img src="/img/table/up.png"></th>
        <th>Date <img src="/img/table/up.png"></th>
        <th>Province <img src="/img/table/up.png"></th>
        <th>Status <img src="/img/table/up.png"></th>
      </tr>
      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr>
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

      <tr class="even">
        <td><input type="checkbox"></td>
        <td>Aenean vel risus est</td>
        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis conseq</td>
        <td>15-11-2014</td>
        <td>Lugo</td>
        <td>Accepted</td>
      </tr>

    </table>
    <!-- Paginador -->
    <div class="tablePaginator ">
      <div class="tablePage"><input type="text" value="100"> de 219</div>
      <div class="tablePreviousPage"><img src="/img/table/a-left.png" alt="previous page"></div>
      <div class="tableNextPage"><img src="/img/table/a-right.png" alt="next page"></div>
    </div>
  <!-- --------- END HTML TABLE --------- -->
  </body>
</html>