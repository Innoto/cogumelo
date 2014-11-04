


$(function() {
  var showTableStatus = "default";
// Handler for .ready() called.
  //showTable('default');
//  bindsTable();
});






function cogumeloTable( tableId, tableUrl ) {
  var that = this;


  that.showTable = function( status ){

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







  that.filters = $('.'+tableId+'.tableContainer .tableMoreFilters');
  that.resumeFilters = $('.'+tableId+'.tableContainer .tableResumeFilters');
  that.showTableStatus = false;


  $('.'+tableId+'.tableContainer .openFilters').on("click", function(){
    that.showTable('openFilters');
  });
  $('.'+tableId+'.tableContainer .closeFilters').on("click", function(){
    that.showTable('closeFilters');
  });

  that.showTable('default');


}
