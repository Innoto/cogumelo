var cogumelo = cogumelo || {};

cogumelo.log = function() {
  // console.log( 'logs_consoleJs', cogumelo.publicConf.logs_consoleJs );
  if( cogumelo.publicConf.logs_consoleJs === true ) {
    console.log.apply( this, arguments );
  }
};
