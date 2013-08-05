



$(document).ready(function(){
  $( "#logs_tabs" ).tabs(); 
  readLogs();
  bindsSQL();
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
      console.debug("Ajax no finish data load");
    });
  },5000);
    
}

function bindsSQL(){
  /* CREATE DB AND USER*/
  $('#create_db').click(function(){
    var user = $('#user_root').val();
    var pass = $('#user_pass').val();
    if( user && pass && user != "" && pass !="" ){

      $.ajax({
        url: "/devel/create_db_scheme",
        type: "POST",
        data:{
          u: user,
          p: pass
        },
        success: function(){

        },
        error: function(){
          alert('Failure create DB')
        }

      });

    }else{
      alert("Introduce tus datos ROOT de Mysql");
    }

  });
  /*FIN CREATE DB*/

  /*CREATE TABLE*/
  $('#create_table').click(function(){
    var user = $('#user_root').val();
    var pass = $('#user_pass').val();
    if( user && pass && user != "" && pass !="" ){
      $.ajax({
        url: "/devel/create_db_tables",
        success: function(){

        },
        error: function(){
          alert('Failure create TABLES')
        }
      });

    }else{
      alert("Introduce tus datos ROOT de Mysql");
    }

  });
  /*FIN CREATE TABLE*/

}