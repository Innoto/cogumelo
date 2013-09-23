


var table = Class.create({

  server_data:false, 

  init : function(options) {
    this.options = $.extend( {
      table_div: $('body'),
      form_div: false,
      table_url: false,
      form_url:false,
      finish_load: function(this){}
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
      that.add_tabs();

      // add filters
      that.add_filters();

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
  add_tabs: function(tabs_box, id, name) {
      tabs_box = this.options.table_div.search('ul.tabs');
      if(data.tabs && tabs_box.html() != "") {
        $.each(data.tabs, function(i, e){
          tabs_box.append('<li key="'.i.'">"'.e.'"</li>');
        });
      }
    
  },

  //
  //  filters
  //
  add_filters: function() {

  },

  add_filter_search: function( id, text) {

  },

  add_filter_select: function(id, json_list) {

  },
  
  add_filter_checkbox_select: function(id, json_list) {

  }

});



