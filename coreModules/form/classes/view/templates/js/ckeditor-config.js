console.log('Cargando ckeditor-config.js de cogumelo form');

CKEDITOR.editorConfig = function( config ) {
  // http://docs.ckeditor.com/#!/api/CKEDITOR.config
  config.toolbarGroups = [
    // { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
    { name: 'links' },
    { name: 'insert' },
    { name: 'forms' },
    { name: 'tools' },
    { name: 'others' },
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'paragraph',   groups: [ 'align', 'list', 'indent', 'blocks' ] },
    { name: 'colors' },
    { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
  ];

  // Remove some buttons provided by the standard plugins, which are
  // not needed in the Standard(s) toolbar.
  config.removeButtons = 'Subscript,Superscript,Maximize,Scayt';

  // Set the most common block elements.
  config.format_tags = 'p;h1;h2;h3;h4;h5;pre';

  // Simplify the dialog windows.
  //config.removeDialogTabs = 'image:advanced;link:advanced';

  config.height = '150';

  config.removePlugins = 'elementspath, autogrow';
};
