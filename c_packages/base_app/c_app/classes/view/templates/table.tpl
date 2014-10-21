<!DOCTYPE html>
<html>
  <head>
    <title>FORMs con Cogumelo</title>

    {$css_includes}

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
      <div class="tableSearch">
        <input type="text"><button>Search</button>
      </div>
      <div class="tableFilters">
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
      <div class="tableActions">
        <div><input type="checkbox"></div>
        <div><img></div>
        <div><img></div>
        <select>
          <option value="1">Action 1</option>
          <option value="2">Action 2</option>
          <option value="3">Action 3</option>
        </select>
        <!-- Paginador -->
        <div class="tablePaginator">
          <div class="tablePage"><input type="text" value="100"> de 219</div>
          <div class="tablePreviousPage"><img></div>
          <div class="tableNextPage"><img></div>
        </div>
      </div>
      <div class="tableOrders">
        <table>
          <tr>
            <td></td>
            <td>Name <img></td>
            <td>Description <img></td>
            <td>Date <img></td>
            <td>Province <img></td>
            <td>Status <img></td>
          </tr>
        </table>
      </div>
    </div>
    <table>
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
    <div class="tablePaginator">
      <div class="tablePage"><input type="text" value="100"> de 219</div>
      <div class="tablePreviousPage"><img></div>
      <div class="tableNextPage"><img></div>
    </div>
  <!-- --------- END HTML TABLE --------- -->
  </body>
</html>