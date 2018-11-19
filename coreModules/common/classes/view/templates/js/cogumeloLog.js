var cogumelo = cogumelo || {};

cogumelo.log = function() {
  // console.log( 'logs_consoleJs', cogumelo.publicConf.logs_consoleJs );
  // console.log( 'mod_mediaserver_productionMode', cogumelo.publicConf.mod_mediaserver_productionMode );
  if( cogumelo.publicConf.logs_consoleJs === true || cogumelo.publicConf.mod_mediaserver_productionMode === false ) {
    console.log.apply( this, arguments );
  }
};
