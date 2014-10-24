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
      <div class="tableSearchFilters clearfix">

        <div class="tableFilters clearfix">
          <button>+Filtros</button>
          <select>
            <option value="1">Aceptado</option>
            <option value="2">Recibido</option>
            <option value="3">Rechazado</option>
          </select>
        </div>
        <div class="tableSearch clearfix">
          <input type="text"><button>Search</button>
        </div>
      </div>

      <div class="tableMoreFilters clearfix">
        <div class="MoreFilters" >
          <fieldset>
            <legend>filters</legend>
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
              <button>Cancel filters</button>
              <button>Apply filters</button>
            </div>
          </fieldset>
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
        <th>
          <div class="clearfix">
            <div>Name</div>
            <div><img src="/img/table/up.png"></div>
          </div>
        </th>
        <th>
          <div class="clearfix">
            <div>Description</div>
            <div><img src="/img/table/up.png"></div>
          </div>
        </th>
        <th>
          <div class="clearfix">
            <div>Date</div>
            <div><img src="/img/table/up.png"></div>
          </div>
        </th>
        <th>
          <div class="clearfix">
            <div>Province</div>
            <div><img src="/img/table/up.png"></div>
          </div>
        </th>
        <th>
          <div class="clearfix">
            <div>Status</div>
            <div><img src="/img/table/up.png"></div>
          </div>
        </th>
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