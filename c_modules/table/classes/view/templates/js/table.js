


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
  that.tableData = false;
  that.currentPage = 1;


  // table elements
  that.filters = $('.'+tableId+'.tableContainer .tableMoreFilters');
  that.resumeFilters = $('.'+tableId+'.tableContainer .tableResumeFilters');
  that.tableContent = $('.'+tableId+'.tableContainer .tableClass');  
  that.tabsContent = $('.'+tableId+'.tableContainer .tableFilters select'); 
  that.pagersTotal = $('.'+tableId+'.tableContainer .tablePaginator .tablePage .totalPages');  
  that.pagersCurrent = $('.'+tableId+'.tableContainer .tablePaginator .tablePage input');  
  
  // buttons and action elements
  that.openFiltersButton = $('.'+tableId+'.tableContainer .openFilters');
  that.closeFiltersButton = $('.'+tableId+'.tableContainer .closeFilters');
  that.anyColHeaderQstr = '.'+tableId+'.tableContainer table.tableClass tr th';
  that.pagersPrevious = $('.'+tableId+'.tableContainer .tablePaginator .tablePreviousPage');
  that.pagersNext = $('.'+tableId+'.tableContainer .tablePaginator .tableNextPage'); 



  that.interfaceAction = function( status ){

    switch (status){
      case "filtered":
        that.showTableStatus = status;
        that.filters.hide();
        that.resumeFilters.show();
      break;
      case "openFilters":
        that.showTableStatus = status;
        that.filters.show();
        that.resumeFilters.hide();
      break;
      case "closeFilters":
      case "default":
      default:
        that.showTableStatus = status;
        that.filters.hide();
        that.resumeFilters.hide();
      break;
    }
  }



  that.load = function() {

    // range
    if( !that.tableData ) {
      var currentRange = null;
    }
    else {
      var currentRange = [ (that.currentPage-1)*parseInt(that.tableData.rowsEachPage), (that.currentPage-1)*parseInt(that.tableData.rowsEachPage) + that.currentPage*parseInt(that.tableData.rowsEachPage) -1 ];
    }


    $.ajax({
      url: tableUrl ,
      type: 'POST',
      data: {
        tab : that.tabsContent.val(),
        order: that.order,
        range: currentRange

      },
      success: function(tableData) {
        that.tableData = tableData;

        that.clearData();
        that.initTabValues();
        that.initOrderValues();
        that.setHeaders();
        that.setRows();
        that.setPager();

      }
    });



  }

  that.clearData = function() {
    that.tableContent.html('');
  }

  that.initTabValues = function() {

    if( !that.currentTab ) {
      that.currentTab = { key: that.tableData.tabs.tabsKey, default:that.tableData.tabs.defaultKey};
      
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

    var orderUpImg = '<img src="/media/module/table/img/up.png">';
    var orderDownImg = '<img src="/media/module/table/img/down.png">';    
    var h = '<th></th>';


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
      maxPage = Math.floor( that.tableData.totalRows / that.tableData.rowsEachPage );
      mustReload = true;
    }


    if( typeof page != 'undefined' && page < maxPage && page > 0 ){
      that.currentPage = page;
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
    
    $.each(that.tableData.table , function( rowIndex, row ) {
      trows += '<tr>';
      trows += '<td></td>';

      $.each( row, function( i, e ){
        trows += '<td>' + e +'</td>';
      });

      trows += '<tr>';
    });

    that.tableContent.append( trows );
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

  // tabs change
  that.tabsContent.on("change", function(){
    that.load();
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
