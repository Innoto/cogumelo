$(function() {
  var showTableStatus = "default";
// Handler for .ready() called.
  showTable('default');
  bindsTable();
});

function bindsTable(){
  $('.openFilters').on("click", function(){
    showTable('openFilters');
  });
  $('.closeFilters').on("click", function(){
    showTable('closeFilters');
  });
}

function showTable( status ){
console.log(status);
console.log("entro");
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