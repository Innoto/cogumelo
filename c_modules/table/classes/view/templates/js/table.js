


$(function() {
  var showTableStatus = "default";
// Handler for .ready() called.
  showTable('default');
//  bindsTable();
});


function showTable( status ){
  var filters = $('.tableMoreFilters');
  var resumeFilters = $('.tableResumeFilters');

  switch (status){
    case "filtered":
      showTableStatus = status;
      filters.hide();
      resumeFilters.show();
    break;
    case "openFilters":
      showTableStatus = status;
      filters.show();
      resumeFilters.hide();
    break;
    case "closeFilters":
    case "default":
    default:
      showTableStatus = status;
      filters.hide();
      resumeFilters.hide();
    break;
  }
}



function cogumeloTable( tableId, tableUrl ) {
  var that = this;

  $('.'+tableId+'.tableContainer .openFilters').on("click", function(){
    showTable('openFilters');
  });
  $('.'+tableId+'.tableContainer .closeFilters').on("click", function(){
    showTable('closeFilters');
  });




}
