
$(document).ready(function(){
  $( "#logs_tabs" ).tabs(); 
  readLogs();
  reloadDebugger();
});


function readLogs(){
  setInterval(function(){
    $.ajax({
      type: "GET",
      url: "/devel/read_logs",
      data : "",
      dataType: "json"
      
    }).done(function(e){

      $.each(e, function(index, val) {
        $("#"+val.log_name).prepend('<div class="cll_container">'+val.data_log+'</div>');
        $("#"+val.log_name).find("div.cll_container").animate({ color : 'green'}, 15000).removeClass('cll_container');        
      });
    }).fail(function(e){
      console.log("Ajax no finish data load");
    });
  },2000);
    
}

function reloadDebugger(){
  setInterval(function(){
    $.ajax({
      type: "GET",
      url: "/devel/get_debugger",
      data : "",
      dataType: "json"
      
    }).done(function(e){
console.debug(e + "SI" );
      $('.debugItemsContainer').html('<div>asdasdas asd asd as </div');      
    }).fail(function(e){
console.debug(e + "NON" );
      $('.debugItemsContainer').prepend(e);   
    });
  },5000);
}