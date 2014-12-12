
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

  var dat = [
    {name: 'FileVO', relationship: [], module:'file'},
    {name: 'UserVO', relationship: ['FileVO'], module:'user'},
    {name: 'UserRoleVO', relationship: ['UserVO', 'RoleVO'], module:'user'},
    {name: 'RoleVO', relationship: [], module:'user'},
    {name: 'RolePermissionVO', relationship: ['RoleVO', 'PermissionVO'], module:'user'},
    {name: 'PermissionVO', relationship: [], module:'user'},
    {name: 'becaVO', relationship: ['UserVO'], module:'becascanada'}
  ];


  var diagramDataObj = {nodes:[],links:[]};

  //links:[{source:12, target:10}];

  $.each(dat, function(i,e) {

    var randomColor = '#'+Math.floor(Math.random()*16777215).toString(16);

    // add scheme nodes
    diagramDataObj.nodes.push( { name: e.name, color: randomColor} );

    // add scheme relationship
    $.each( e.relationship, function( i2, e2 ){

      var relTo = 0;
      $.each(dat, function(i3,e3) { 
        if(e3.name == e2) {
          relTo = i3;
        }
      });

      console.log(e.name, relTo);

      //console.log( dat.indexOf( e2.name ) );
      diagramDataObj.links.push( { source: i, target:relTo } );
    });


console.log(diagramDataObj.links)

    // add to legend
    if( !$('#dbsql_container .erDiagram .legend').find('.'+e.module).length ) {
      $('#dbsql_container .erDiagram .legend').append('<div class="module ' + e.module + '"> <b>'+ e.module +'</b></div>');
    }

    $('#dbsql_container .erDiagram .legend').find('.module.' +e.module).append('<div><div class="colorlegend" style="background-color:'+randomColor+';"></div><div>'+e.name+'</div></div>');

  });



  drawERD(  
    '#svgDiv',
    diagramDataObj,
        cola
    );
}

