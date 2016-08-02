


$(function() {
  var showTableStatus = "default";
// Handler for .ready() called.
  //showTable('default');
//  bindsTable();
});






function cogumeloTable( tableId, tableUrl ) {
  var that = this;
  that.range = [];
  that.order = false;
  that.currentTab = false;
  that.extraFilters = false;
  that.tableData = false;
  that.currentPage = 1;

  // search and filters
  that.search = false;


  // table elements
  that.filters = $('.'+tableId+'.tableContainer .tableMoreFilters');
  that.filtersContent = $('.'+tableId+'.tableContainer .tableMoreFilters .MoreFilters .clearfix');
  that.resumeFilters = $('.'+tableId+'.tableContainer .tableResumeFilters');
  that.tableContent = $('.'+tableId+'.tableContainer .tableClass');
  that.tabsContent = $('.'+tableId+'.tableContainer .tableFilters select');
  that.pagersTotal = $('.'+tableId+'.tableContainer .tablePaginator .tablePage .totalPages');
  that.pagersCurrent = $('.'+tableId+'.tableContainer .tablePaginator .tablePage input');
  that.headTableCheckBoxQstr = '.'+tableId+'.tableContainer .tableClass .headCheckBox';
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
        break;
      case "search":
        that.searchClearButton.show();
        break
      case "unsearch":
        that.searchClearButton.hide();
        break;
      case "closeFilters":
        that.filters.hide();
        if(that.extraFilters != false){
          that.showFiltersResume();
        }

        break;
      case "clearFilters":
        that.extraFilters = false;
        that.setExtraFilters();
        that.resumeFilters.hide();
        break;
      case "default":
      default:
        that.showTableStatus = status;
        that.filters.hide();
        that.resumeFilters.hide();
      break;
    }
  }



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
        exportType: false,
        tab : that.tabsContent.val(),
        filters: that.extraFilters,
        order: that.order,
        range: currentRange,
        action: action,
        search: that.search

      },
      success: function(tableData) {
        that.tableData = tableData;

        that.clearData();
        that.initTabValues();
        if( that.extraFilters == false ){
          that.setExtraFilters();
        }
        that.setActionValues();
        that.setExportValues();
        that.initOrderValues();
        that.setHeaders();
        that.setRows();
        that.setPager();
        if (res) res();
      }
    });



  }

  that.clearData = function() {
    that.tableContent.html('');
  }

  that.initTabValues = function() {

    if( !that.currentTab ) {
      that.currentTab = { key: that.tableData.tabs.tabsKey, default:that.tableData.tabs.defaultKey};

      if( that.tableData.tabs != false){
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
  }

  that.setExtraFilters = function() {


    that.filtersContent.html('');

    $.each( that.tableData.extraFilters , function(i, e){
      var opts = '';
      //console.log(e);

      $.each( e.options , function(i2,e2) {

        var isSelected = ' ';

        if( e.default == i2 ) {
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

  }

  that.getFilterValues = function() {
    that.extraFilters  = {};
    that.filtersContent.find('select, input').each( function(i,e) {
      //console.log( $(e).attr('data-filter-id'), $(e).val() )
      eval('that.extraFilters.' + $(e).attr('data-filter-id') + ' = "' + $(e).val() + '"' );
    });

    that.load();
  }

  that.showFiltersResume = function() {

    var resumeString = '';
    var coma = '';

    $.each(that.extraFilters, function(i,e){
      eval('var filter = that.tableData.extraFilters.'+i)

      //console.log( i, title , e );
      var valueString = e;



      $.each( filter.options, function(i2,e2) {
        if(i2 == e) {
          valueString = e2;
        }
      });

      if(e != '*') {
        resumeString += coma + ' (<b>' + filter.title + '</b>: '+valueString+')';
      }
      coma = ',';
    });

    that.resumeFilters.find('span.filterValues').html( resumeString );

    if( resumeString !== ''){
      that.resumeFilters.show();
    }

  }

  that.setActionValues = function() {

    that.actionSelect.html("");

    $.each(that.tableData.actions, function(i,e) {
      that.actionSelect.append('<option value='+i+'> ' + e + '</option>');
    });

  }

  that.setExportValues = function() {

    that.exportSelect.html("");

    $.each(that.tableData.exports, function(i,e) {
      that.exportSelect.append('<option value='+i+'> ' + e.name + '</option>');
    });

  }

  that.initOrderValues = function() {

    if( !that.order ) {
      that.order = [];
      $.each( that.tableData.colsDef , function(i,e)  {
        that.order.push( {"key": i, "value": 1} );
      });

    }
  }

  that.getOrderValue = function( ordIndex ) {

    var ret = false;
      $.each( that.order , function(i,e)  {
        if( e.key == ordIndex ) {
          ret = e.value;
        }
      });

    return ret;
  }

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
  }


  that.setHeaders = function() {

    var orderUpImg = '<img src="'+cogumelo.publicConf.media+'/module/table/img/up.png">';
    var orderDownImg = '<img src="'+cogumelo.publicConf.media+'/module/table/img/down.png">';
    var h = '<th><div class="selectAll"><input class="headCheckBox" type="checkbox"></div></th>';


    $.each(that.tableData.colsDef, function(i,e)  {

      if( that.getOrderValue(i) == 1 ) {
        var ord = orderDownImg;
      }
      else {
        var ord = orderUpImg;
      }

      h += '' +
        '<th colKey="' + i + '" class="thKey">' +
        ' <div class="clearfix">' +
        '  <div>' + e + '</div>' +
        '  <div>' + ord + '</div>' +
        ' </div>' +
        '</th>';

    });

    that.tableContent.append('<tr>'+h+'</tr>');


    // select/unselect all checkbox
    $(that.headTableCheckBoxQstr).on("change", function(el) {
      $(that.allTableCheckBoxesQstr).prop('checked', $(el.target).prop('checked') );;
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



  }


  that.setPager = function( page ) {

    var mustReload = false;
    var maxPage = 1;

    if( that.tableData.totalRows > that.tableData.rowsEachPage ){
      maxPage = Math.ceil( that.tableData.totalRows / that.tableData.rowsEachPage );
    }


    if( typeof page != 'undefined' ) {
      mustReload = true;

      if( page <= maxPage && page > 0 ){
        that.currentPage = page;
      }
    }

    that.pagersTotal.html( maxPage );
    that.pagersCurrent.val( that.currentPage );


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
  }



  that.setRows = function(){
    var trows = '';
    var evenClass='';

    $.each(that.tableData.table , function( rowIndex, row ) {
      if(evenClass == '') { evenClass='even'; } else { evenClass=''; }

      tUrl = row.tableUrlString;
      trows += '<tr class="' + evenClass + '">';
      trows += '<td> <input class="eachRowCheckBox" rowReferenceKey="'+row.rowReferenceKey+'" type="checkbox"> </td>';


      $.each( row, function( i, e ){
        if( i != 'rowReferenceKey' && i != 'tableUrlString' ){
          trows += '<td  onclick="window.location=\''+ tUrl + '\';">' + e +'</td>';
        }
      });

      trows += '<tr>';
    });

    that.tableContent.append( trows );

    // uncheck head checkbox when change any row
    $(that.allTableCheckBoxesQstr).unbind('change');
    $(that.allTableCheckBoxesQstr).on('change', function( chClick ){
      $(that.headTableCheckBoxQstr).prop('checked', false)
    });
  }

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
          //console.log(retData);

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
            filters: false,
            search: that.search
          }
      });



      that.exportSelect.val('0')
    }
  }

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
      that.load( {action: act, keys: selectedRows}, resExt );
    }
    else {
      that.load();
    }

  }



  that.actionSearch = function( searchText ) {
    if( searchText != '' ) {
      that.search = searchText;
      that.load();
      that.interfaceAction('search');
    }
  }


  that.searchClear = function() {
    that.searchInput.val('');
    that.search = false;
    that.load();
    that.interfaceAction('unsearch');
  }

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
    that.actionExport()
  });

  // tabs change
  that.tabsContent.on("change", function(){
    that.load();
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
