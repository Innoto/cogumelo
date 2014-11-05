


$(function() {
  var showTableStatus = "default";
// Handler for .ready() called.
  //showTable('default');
//  bindsTable();
});






function cogumeloTable( tableId, tableUrl ) {
  var that = this;
  that.range = [];
  that.order = [];
  that.tableData = {};



  // table elements
  that.filters = $('.'+tableId+'.tableContainer .tableMoreFilters');
  that.resumeFilters = $('.'+tableId+'.tableContainer .tableResumeFilters');
  that.tableContent = $('.'+tableId+'.tableContainer .tableClass');  
  
  // buttons and action elements
  that.openFiltersButton = $('.'+tableId+'.tableContainer .openFilters');
  that.closeFiltersButton = $('.'+tableId+'.tableContainer .closeFilters');
  that.anyColHeaderQstr = '.'+tableId+'.tableContainer table.tableClass tr th';




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
    $.ajax({
      url: tableUrl ,
      data: {

      },
      success: function(tableData) {
        that.tableData = tableData;

        that.clearData();
        that.initOrderValues();
        that.setHeaders();
        that.setRows();

      }
    });



  }

  that.clearData = function() {
    that.tableContent.html('');
  }

  that.initOrderValues = function() {

    if( that.order.length == 0 ) {
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

      $.each( that.order , function(i,e)  {

        if( e.key == ordIndex ) {
          if(e.value == 1) {
            that.order[i].value = -1;
          }
          else {
            that.order[i].value = 1;
          }
        }
      });

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

  that.setRows = function(){
    var trows = '';
    
    $.each(that.tableData.table , function( rowIndex, row ) {
      trows += '<tr>';
      trows += '<td></td>';

      $.each( row, function( i, e ){
        trows += '<td>' + e + '</td>';
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




  // FIRST TIME 
  that.interfaceAction('default');
  that.load();
}
