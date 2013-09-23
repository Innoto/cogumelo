


var table = Class.create({

  server_data:false, 

  init : function(options) {
    this.options = $.extend( {
      table_div: $('body'),
      form_div: false,
      table_url: false,
      form_url:false,
      finish_load: function(){}
    }, options || {})
  



  },


  create_structure: function() {

    // struct
    this.options.table_div.html(
      "<div class='table'>" +
        "<ul class='tabs'></ul>" +
        "<div class='filters'></ul>" +
        "<table></table>" + 
        "<div class='paginator'></div>" +
      "</div>"
    );

  },


  server_data: function(){
    that=this;
    // Call server!!
    $.ajax({
      url: this.options.table_url,
      dataType: 'JSON',
      async:false
    }).done( function(data){
     
      // dump server data
      that.server_data = data;

      // add tabs

      // add filters

      // add paginator

      // triger finish load
      that.finish_load();
      
    }).fail( function(){
        that.options.table_div.html('AJAX connection failed.');
    });
  }

  //
  //  Tabs
  //
  add_tab: function(id, name) {},

  //
  //  Widgets
  //

  add_widget_search: function( id, text) {

  },

  add_widget_select: function(id, json_list) {

  },
  
  add_widget_checkbox_select: function(id, json_list) {

  }

});



