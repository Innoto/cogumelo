
$(document).ready(function(){
  $( "#logs_tabs" ).tabs(); 
  readLogs();
  autoLoadDebugger();
  drawERScheme();
  botonBinds();
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
  },5000);
    
}


function autoLoadDebugger(){
  setInterval(function(){
    loadDebugger();
  }, 10000);
}

function loadDebugger(){
  $.ajax({
    type: "POST",
    url: "/devel/get_debugger",
    dataType: "json",
    cache: false      
  }).done(function(e){
    $.each( e , function( key, val ) {
      $('.debugItemsContainer').append('<div class="headerDebugItem"><h3>'+val.comment+'</h3><span>'+val.date+'</span></div><div class="debugItemContainer">'+val.debuging+'</div>');   
    });      
  }).fail(function(e){
    console.log("fallo ou baleiro");
  });
}


function botonBinds(){
  $('.refreshDebugger').on('click', function(){
    loadDebugger();
  });
  $('.clearDebugger').on('click', function(){
    $('.debugItemsContainer').html('');
  });
}




function drawERScheme() {

  var datos = {
    {name: 'FileVO', relationship: [], module:'file'},
    {name: 'UserVO', relationship: [], module:'user'},
    {name: 'UserRoleVO', relationship: [], module:'user'},
    {name: 'RoleVO', relationship: [], module:'user'},
    {name: 'RolePermissionVO', relationship: [], module:'user'},
    {name: 'PermissionVO', relationship: [], module:'user'}
  };







  drawERD(  
    '#svgDiv',
    {
          nodes: [
            {name:'userVO', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
            {name:'FileVO', color: '#'+Math.floor(Math.random()*16777215).toString(16) },  
            {name:'c', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
            {name:'d', color: '#'+Math.floor(Math.random()*16777215).toString(16) },  
            {name:'e', color: '#'+Math.floor(Math.random()*16777215).toString(16) },  
            {name:'f', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
            {name:'g', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'h', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'i', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'h', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
            {name:'i', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 

                {name:'j', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'k', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'l', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'m', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'n', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'o', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
                {name:'p', color: '#'+Math.floor(Math.random()*16777215).toString(16) }, 
         

          ],
          links: [
            {source:1, target:2},
            {source:0, target:1},
                {source:0, target:2},
                {source:2, target:16},

                {source:16, target:15},
                {source:14, target:15},

                {source:14, target:16},
                {source:1, target:15},

            {source:4, target:5},
            {source:3, target:4},
            {source:3, target:5},
                {source:13, target:3},
                {source:13, target:5},

                {source:8, target:9},
                {source:9, target:10},
                {source:8, target:10},
                {source:10, target:11},
                {source:11, target:12},
            {source:12, target:10}

     
          ]
        }
      ,
      cola
    );
}

