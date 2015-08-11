/*
  Cogumelo Timedebugger allows to debug js times without using any console.
*/
TimeDebuger = function( opts ) {
  var that = this;

  that.options = new Object({
    debug:false
  });
  $.extend(true, that.options, opts);


  that.reset = function(){
    if(that.options.debug == true)
      that.debug_started_at = that.getdate();
  }

  that.separator = function(){
    if(that.options.debug == true)
      $(that.debug_clusteringTimes).prepend("<br>");
  }

  that.log = function(desc) {
    var that = this;
    if(that.options.debug == true){
      $(that.debug_clusteringTimes).prepend("<b>"+desc+": </b>"+ that.checktime()+"ms<br>");
      that.reset();
    }
  }

  that.debug_set_initial_time = function() {
    var that = this;
    if(that.options.debug == true){
      $(that.debug_mainTimes).html("<b>Time ready, go ahead!: </b>"+ that.checktime()+"ms");
      that.reset();
    }
  }

  that.getdate = function() {
    return new Date().getTime() ;
  }

  that.checktime = function() {
    var that = this;
    return that.getdate() - that.debug_started_at;
  }









  if(that.options.debug == true) {

    that.debug_debugDiv = document.createElement('div');
    that.debug_debugTitle = document.createElement('h3');
    that.debug_mainTimes = document.createElement('div');
    that.debug_clusteringTimes = document.createElement('div');

    $(that.debug_debugTitle).html("Is time for deguging times!");

    $(that.debug_debugDiv).append(that.debug_debugTitle);
    $(that.debug_debugDiv).append(that.debug_mainTimes);
    $(that.debug_debugDiv).append(that.debug_clusteringTimes);
    $("body").append(that.debug_debugDiv);



    $(that.debug_debugDiv).addClass("marker_clusterer_debug");
    $(that.debug_mainTimes).addClass("marker_clusterer_debug");


    $(that.debug_debugDiv).css('width','380px');
    $(that.debug_debugDiv).css('max-height','400px');
    $(that.debug_debugDiv).css('min-height', '100px');
    $(that.debug_debugDiv).css('position','absolute');
    $(that.debug_debugDiv).css('right','0px');
    $(that.debug_debugDiv).css('bottom','0px');
    $(that.debug_debugDiv).css('z-index','1110');
    $(that.debug_debugDiv).css('padding','10px');
    $(that.debug_debugDiv).css('font-size','0.7em');
    $(that.debug_debugDiv).css('overflow','auto');
    $(that.debug_debugDiv).find('div').css('margin-top', '4px');

    $(that.debug_debugDiv).css('background-color','white');

    that.debug_started_at = false;
    that.debug_started_at = that.getdate();

  }





  that.debug_set_initial_time();
}
