


$(function() {
  var showTableStatus = "default";
// Handler for .ready() called.
  //showTable('default');
//  bindsTable();
});






function cogumeloTable( tableId, tableUrl ) {
  var that = this;
  that.firstTime = true;
  that.range = [];
  that.order = false;
  that.currentTab = false;
  that.extraFilters = false;
  that.tableData = false;
  that.currentPage = 1;
  that.selectAllPagesValue = false;
  // search and filters
  that.search = false;


  // table elements
  that.filters = $('.'+tableId+'.tableContainer .tableMoreFilters');
  that.filtersContent = $('.'+tableId+'.tableContainer .tableMoreFilters .MoreFilters .clearfix');
  that.resumeFilters = $('.'+tableId+'.tableContainer .tableResumeFilters');
  that.tableContent = $('.'+tableId+'.tableContainer .tableClass');
  that.tabsContent = $('.'+tableId+'.tableContainer .tableFilters select');
  that.rowsEachPage = $('.'+tableId+'.tableContainer .tablePageElements input');
  that.totalRows = $('.'+tableId+'.tableContainer .tablePaginator  .totalRows');
  that.pagersTotal = $('.'+tableId+'.tableContainer .tablePaginator .tablePage .totalPages');
  that.pagersCurrent = $('.'+tableId+'.tableContainer .tablePaginator .tablePage input');
  that.headTableCheckBoxQstr = '.'+tableId+'.tableContainer .tableClass .headCheckBox';
  that.selectAllPages = '.'+tableId+'.tableContainer .tableClass .selectAllPages';
  that.dropSelectAll = '.'+tableId+'.tableContainer .tableClass .dropSelectAll';
  that.allTableCheckBoxesQstr = '.'+tableId+'.tableContainer .tableClass .eachRowCheckBox';
  that.searchForm = $('.'+tableId+'.tableContainer .tableSearchFilters .tableSearch form');
  that.searchInput = $('.'+tableId+'.tableContainer .tableSearchFilters .tableSearch form input');


  // buttons and action elements
  that.openFiltersButton = $('.'+tableId+'.tableContainer .openFilters');
  that.closeFiltersButton = $('.'+tableId+'.tableContainer .closeFilters');
  that.clearFiltersButton = $('.'+tableId+'.tableContainer .clearFilters');
  that.anyColHeaderQstr = '.'+tableId+'.tableContainer table.tableClass tr th';
  that.pagersPrevious = $('.'+tableId+'.tableContainer .tablePaginator .tablePreviousPage');
  that.pagersNext = $('.'+tableId+'.tableContainer .tablePaginator .tableNextPage');
  that.actionSelect = $('.'+tableId+'.tableContainer .tableActions .actionSelect');
  that.exportSelect = $('.'+tableId+'.tableContainer .exportContainer .exportSelect');
  that.searchButton = $('.'+tableId+'.tableContainer .tableSearchFilters .tableSearch form button.search');
  that.searchClearButton = $('.'+tableId+'.tableContainer .tableSearchFilters .tableSearch form button.clear');
  that.addNewItem = $('.'+tableId+'.tableContainer .addElem');



  that.interfaceAction = function( status ){

    switch (status){
      case "filtered":

        break;
      case "openFilters":
        that.showTableStatus = status;
        that.filters.show();
        that.resumeFilters.hide();
        that.openFiltersButton.hide();
        break;
      case "closeFilters":
        that.filters.hide();
        if(that.extraFilters != false ){
          that.showFiltersResume();
        }
        that.openFiltersButton.show();

        break;
      case "search":
        that.searchClearButton.show();
        break;
      case "unsearch":
        that.searchClearButton.hide();
        break;
      case "clearFilters":
        that.extraFilters = false;
        that.setExtraFilters();
        that.resumeFilters.hide();
        that.setPager(1);
        break;
      case "default":
      default:
        that.showTableStatus = status;
        that.filters.hide();
        that.resumeFilters.hide();
      break;
    }
  };



  that.load = function( doAction, res ) {

    // range
    if( !that.tableData ) {
      var currentRange = null;
    }
    else {
      //var currentRange = [ (that.currentPage-1)*parseInt(that.tableData.rowsEachPage), (that.currentPage-1)*parseInt(that.tableData.rowsEachPage) + that.currentPage*parseInt(that.tableData.rowsEachPage) -1 ];
      var currentRange = [ (that.currentPage-1)*parseInt(that.tableData.rowsEachPage), that.tableData.rowsEachPage ];
    }

    // action
    if( typeof doAction == 'undefined' ){
      var action = {action: 'list', keys: false};
    }
    else {
      var action = doAction;
    }


    $.ajax({
      url: tableUrl ,
      type: 'POST',
      data: {
        firstTime : that.firstTime,
        exportType: false,
        tab : that.tabsContent.val(),
        filters: that.extraFilters,
        order: that.order,
        range: currentRange,
        action: action,
        search: that.search,
        rowsEachPage: that.tableData.rowsEachPage,
        clientCurrentPage: that.currentPage,
        selectAllPages: that.selectAllPagesValue
      },
      success: function(tableData) {
        that.tableData = tableData;

        that.clearData();
        that.initTabValues();
        that.setSearchValue();

        if( typeof that.tableData.previousPostData != 'undefined' && typeof that.tableData.previousPostData.filters != 'undefined' ) {

          if(  that.tableData.previousPostData.filters === 'false' ) {
            that.extraFilters = false;
          }
          else {
            that.extraFilters = that.tableData.previousPostData.filters;
          }

        }
        that.setExtraFilters();
        that.interfaceAction('openFilters');
        that.interfaceAction('closeFilters');


        that.setActionValues();
        that.setExportValues();
        that.initOrderValues();
        that.setHeaders();
        that.setRows();

        if( typeof that.tableData.previousPostData != 'undefined' && typeof that.tableData.previousPostData.clientCurrentPage != 'undefined' ) {
          that.rememberPage( that.tableData.previousPostData.clientCurrentPage );
        }

        that.setPager();
        if (res) res();
        that.firstTime = false;
      }
    });



  };

  that.clearData = function() {
    that.tableContent.html('');
  };

  that.initTabValues = function() {

    if( !that.currentTab && typeof that.tableData.tabs != 'undefined' ) {
      that.currentTab = { key: that.tableData.tabs.tabsKey, default:that.tableData.tabs.defaultKey};

      if( that.tableData.tabs != false ){
        $.each( that.tableData.tabs.tabs , function(i,e)  {
          if(i == that.currentTab.default){
            var sel = ' SELECTED ';
          }
          else {
            var sel = ' ';
          }
          that.tabsContent.append('<option ' + sel + ' value="' + i + '">' + e + '</option>');

        });
      }
      else {
        that.tabsContent.hide();
      }

    }
  };

  that.setExtraFilters = function() {


    that.filtersContent.html('');

    $.each( that.tableData.extraFilters , function(i, e){
      var opts = '';
      //cogumelo.log(e);


      $.each( e.options , function(i2,e2) {
        var isSelected = ' ';

        if(  that.extraFilters == false  && e.default == i2 ) {
          isSelected = ' SELECTED ';
        }
        else
        if(  that.extraFilters != false && that.extraFilters[i] === i2 ) {
          isSelected = ' SELECTED ';
        }

        opts += '<option value="'+ i2 +'" ' + isSelected + ' >' + e2 + '</option>';
      });


      that.filtersContent.append(
        '<div class="FilterMain">' +
          '<label> ' + e.title + ' </label>' +
          '<select data-filter-id="' + i + '" >' +
            opts +
          '</select>'+
        '</div>'
      );
    });

    that.filtersContent.find('select, input').unbind("change");
    that.filtersContent.find('select, input').on("change", that.getFilterValues );

  };

  that.getFilterValues = function() {
    that.extraFilters  = {};
    that.filtersContent.find('select, input').each( function(i,e) {
      //cogumelo.log( $(e).attr('data-filter-id'), $(e).val() );
//      eval('that.extraFilters.' + $(e).attr('data-filter-id') + ' = "' + $(e).val() + '"' );

      eval( "that.extraFilters['" + $(e).attr('data-filter-id') + "'] = '" + $(e).val() + "'");
    });

    that.setPager(1);
  };

  that.showFiltersResume = function() {

    var resumeString = '';
    var coma = '';


    $.each(that.extraFilters, function(i,e){

      eval('var filter = that.tableData.extraFilters["'+ i +'"]');



      //cogumelo.log( i, title , e );
      var valueString = e;



      $.each( filter.options, function(i2,e2) {
        if(i2 == e) {
          valueString = e2;
        }
      });

      if(e != '*') {cogumelo.log('filtro',filter);
        resumeString += coma + ' (<b>' + filter.title + '</b>: '+valueString+')';
      }
      coma = ',';
    });

    that.resumeFilters.find('span.filterValues').html( resumeString );

    if( resumeString !== ''){
      that.resumeFilters.show();
    }

  };

  that.setActionValues = function() {

    that.actionSelect.html("");

    $.each(that.tableData.actions, function(i,e) {
      if( e == null ){
        that.actionSelect.append('<option value="-" disabled>――――――</option>');
      }
      else {
        that.actionSelect.append('<option value='+i+'> ' + e + '</option>');
      }
    });

  };

  that.setExportValues = function() {

    that.exportSelect.html("");

    $.each(that.tableData.exports, function(i,e) {
      that.exportSelect.append('<option value='+i+'> ' + e.name + '</option>');
    });

  };

  that.initOrderValues = function() {

    if( !that.order ) {
      that.order = [];
      $.each( that.tableData.colsDef , function(i,e)  {
        that.order.push( {"key": i, "value": 1} );
      });

    }
  };

  that.getOrderValue = function( ordIndex ) {

    var ret = false;
      $.each( that.order , function(i,e)  {
        if( e.key == ordIndex ) {
          ret = e.value;
        }
      });

    return ret;
  };

  that.setOrderValue = function( ordIndex ) {

    var ordArray = [];
    $.each( that.order , function(i,e)  {

      if( e.key == ordIndex ) {
        var nval = e;
        if(nval.value == 1) {
          nval.value = -1;
        }
        else {
          nval.value = 1;
        }

        ordArray.unshift( nval );
      }
      else {
        ordArray.push(e);
      }
    });

    that.order = ordArray;
    that.load();
  };


  that.setHeaders = function() {

    var checkBoxSelected = '';
    if(that.selectAllPagesValue == true){
      checkBoxSelected = ' checked ';
    }

    var orderUpImg = '<img src="'+cogumelo.publicConf.media+'/module/table/img/up.png">';
    var orderDownImg = '<img src="'+cogumelo.publicConf.media+'/module/table/img/down.png">';
    var h = '<th><div class="selectAllPages" style="display:none">'+__('Select all pages')+'</div><div class="selectAll"><input class="headCheckBox" type="checkbox" '+checkBoxSelected+'><i class="dropSelectAll fa fa-align-left"></div></th>';


    $.each(that.tableData.colsDef, function(i,e)  {

      if( that.getOrderValue(i) == 1 ) {
        var ord = orderDownImg;
      }
      else {
        var ord = orderUpImg;
      }


      if( typeof $(that.tableData.colsClasses).attr( i )  != 'undefined' ) {
        var colClasses = $(that.tableData.colsClasses).attr( i );
      }
      else {
        var colClasses = '';
      }


      h += '' +
        '<th colKey="' + i + '" class="thKey '+colClasses+' ">' +
        ' <div class="clearfix">' +
        '  <div>' + e + '</div>' +
        '  <div>' + ord + '</div>' +
        ' </div>' +
        '</th>';

    });

    that.tableContent.append('<tr>'+h+'</tr>');


    // select/unselect all checkbox
    $(that.headTableCheckBoxQstr).on("change", function(el) {

      if($(that.allTableCheckBoxesQstr).prop('checked') === true ) {
        that.selectAllPagesValue = false;
        that.load();
      }
      $(that.allTableCheckBoxesQstr).prop('checked', $(el.target).prop('checked') );
    });


    $(that.dropSelectAll).on("click", function(el) {
      $(that.selectAllPages).show();
    });

    $(that.selectAllPages).on("click", function(el) {
      that.actionSelectAllPages();
    });

    $(document).on('click', function(el){
      if(!$(el.target).hasClass('dropSelectAll')) {
        $(that.selectAllPages).hide();
      }

    });

    // click event table headers
    $(that.anyColHeaderQstr).on("click", function(thElement){

      var el = false;

      if( $(thElement.target).parent().hasClass('thKey') ){
        el = $(thElement.target).parent();
      }
      else
      if( $(thElement.target).parent().parent().hasClass('thKey') ) {
        el = $(thElement.target).parent().parent();
      }
      else
      if( $(thElement.target).parent().parent().parent().hasClass('thKey') ) {
        el = $(thElement.target).parent().parent().parent();
      }

      if( el ) {
        that.setOrderValue( el.attr('colKey') );
      }
    });



  };


  that.actionSelectAllPages = function() {
    that.selectAllPagesValue = true;
    cogumelo.log();
    //$(that.headTableCheckBoxQstr).attr('checked', true);
    that.load();

  },


  that.setSearchValue = function(  ) {
    var searchString = this.tableData.search;
    if( searchString != "false" && searchString != null  && searchString != '' && typeof searchString == 'string' ) {
      that.interfaceAction('search');
      $(that.searchInput.val(searchString)) ;
      that.search = searchString;
    }
  };

  that.rememberPage = function(page) {
    that.pagersCurrent.val( page );



    that.currentPage= parseInt(page);



  };


  that.setPager = function( page ) {

    var mustReload = false;
    var maxPage = 1;

    that.rowsEachPage.val(that.tableData.rowsEachPage);

    if( that.tableData.totalRows > that.tableData.rowsEachPage ){
      maxPage = Math.ceil( that.tableData.totalRows / that.tableData.rowsEachPage );
    }


    if( typeof page != 'undefined' ) {
      mustReload = true;

      if( page <= maxPage && page > 0 ){
        that.currentPage = page;
      }
    }


    that.totalRows.html( that.tableData.totalRows );

    that.pagersTotal.html( maxPage );

    if( that.pagersCurrent.val() == '') {
      that.pagersCurrent.val( that.currentPage );
    }



    if( that.currentPage == maxPage ){
      that.pagersNext.addClass('unactive'); // nextPage unactive
    }
    else {
      that.pagersNext.removeClass('unactive'); // nextPage active
    }

    if( that.currentPage == 1) {
      that.pagersPrevious.addClass('unactive'); // previousPage unactive
    }
    else {
      that.pagersPrevious.removeClass('unactive'); // previousPage unactive
    }

    if( mustReload ) {
      that.load();
    }


  };



  that.setRows = function(){
    var trows = '';
    var evenClass='';

    $.each(that.tableData.table , function( rowIndex, row ) {
      if(evenClass == '') { evenClass='even'; } else { evenClass=''; }

      tUrl = row.tableUrlString;


      var blockSelectedDisabled = '';
      if( that.selectAllPagesValue == true ){
        blockSelectedDisabled = ' checked disabled ';
      }

      trows += '<tr class="' + evenClass + '">';
      trows += '<td> <input '+blockSelectedDisabled+' class="eachRowCheckBox"  rowReferenceKey="'+row.rowReferenceKey+'" type="checkbox"> </td>';

      $.each( row, function( i, e ){

        if( typeof $(that.tableData.colsClasses).attr( i )  != 'undefined' ) {
          var cowClasses = $(that.tableData.colsClasses).attr( i );
        }
        else {
          var cowClasses = '';
        }

        if( i != 'rowReferenceKey' && i != 'tableUrlString' ){
          trows += '<td class="'+cowClasses+'"  onclick="window.location=\''+ tUrl + '\';">' + e +'</td>';
        }
      });

      trows += '<tr>';
    });

    that.tableContent.append( trows );

    // uncheck head checkbox when change any row
    $(that.allTableCheckBoxesQstr).unbind('change');
    $(that.allTableCheckBoxesQstr).on('change', function( chClick ){
      $(that.headTableCheckBoxQstr).prop('checked', false);
    });
  };

  that.setElementsEachPage = function( number ) {
    that.tableData.rowsEachPage = number;
    that.setPager(1);        
    that.load();
  };

  that.actionExport = function() {

    if(that.exportSelect.val() != '0') {

      // range
      var currentRange = null;
      // action
      var action = {action: 'list', keys: false};
/*
      $.post(
        tableUrl,
        {
          exportType: that.exportSelect.val(),
          tab : that.tabsContent.val(),
          order: that.order,
          range: currentRange,
          action: action,
          filters: false,
          search: that.search
        },
        function(result){
          //var binUrl = retData.url;
          //cogumelo.log(retData);

          var blob=new Blob([result]);

          var link=document.createElement('a');
          link.href=window.URL.createObjectURL(blob);
          link.download="myFileName.json";
          link.click();
      });
*/

      $.fileDownload(tableUrl, {
          httpMethod: "POST",
          data: {
            exportType: that.exportSelect.val(),
            tab : that.tabsContent.val(),
            order: that.order,
            range: currentRange,
            action: action,
            filters: that.extraFilters,
            search: that.search
          }
      });



      that.exportSelect.val('0');
    }
  };

  that.actionOnSelectedRows = function(actExt, resExt ) {

    var selectedRows = [];

    $(that.allTableCheckBoxesQstr).each( function(i,e){

      if( $(e).prop('checked') ){
        selectedRows.push( $(e).attr('rowReferenceKey') );
      }
    });

    if(actExt)
      act = actExt;
    else
      act = that.actionSelect.val();

    if( act != '0' && selectedRows.length > 0 ){

      if( this.selectAllPagesValue == true ){
        var numberStrForAction = that.tableData.totalRows;
      }
      else {
        var numberStrForAction = selectedRows.length;
      }

      cogumelo.clientMsg.confirm(
        __('Apply action "') + that.actionSelect.find('option:selected').html() + __('" on ') +  numberStrForAction+ ' ' +__('elements'),
        function( accion ) {
          if( accion === true) {
            that.load( {action: act, keys: selectedRows}, resExt );
          }
          else {
            that.load();
          }
        }
      );

    }
    else {
      that.load();
    }

  };



  that.actionSearch = function( searchText ) {
    if( searchText != '' ) {
      that.search = searchText;

      // that.setPager(1);
      that.currentPage = 1;

      that.load();
      that.interfaceAction('search');
    }
  };


  that.searchClear = function() {
    that.searchInput.val('');
    that.search = false;

    // that.setPager(1);
    that.currentPage = 1;

    that.load();
    that.interfaceAction('unsearch');
  };

  // EVENTS

  // click open filters
  that.openFiltersButton.on("click", function(){
    that.interfaceAction('openFilters');
  });

  // click close filters
  that.closeFiltersButton.on("click", function(){
    that.interfaceAction('closeFilters');
  });

  // click clear filters
  that.clearFiltersButton.on("click", function(){
    that.interfaceAction('clearFilters');
  });



  // Action select
  that.actionSelect.on("change", function( ){
    that.actionOnSelectedRows();
  });

  // Export select
  that.exportSelect.on("change", function( ){
    that.actionExport();
  });


  // pager events
  that.rowsEachPage.on("change", function( inputEachPage ){
    that.setElementsEachPage( $(inputEachPage.target).val() );
  });

  // tabs change
  that.tabsContent.on("change", function(){
    that.setPager(1);
    //that.load();
  });

  // search
  that.searchForm.on("submit", function(){
    that.actionSearch( that.searchInput.val() );
  });

  // search clear
  that.searchClearButton.on("click", function(){
    that.searchClear();
  });

  // add new element
  that.addNewItem.on("click", function(){
    window.location = that.tableData.newItemUrl;
  });


  // pager events
  that.pagersCurrent.on("change", function( inputCurrentPage ){
    that.setPager( $(inputCurrentPage.target).val() );
  });

  that.pagersPrevious.on("click", function(){
    that.setPager(that.currentPage - 1);
  });

  that.pagersNext.on("click", function(){
    that.setPager(that.currentPage + 1);
  });




  // FIRST TIME
  that.interfaceAction('default');
  that.load();
}
