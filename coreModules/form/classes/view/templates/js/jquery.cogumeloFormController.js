
/*
  Gestión de informacion en cliente
*/

var cogumeloFormControllerFormsInfo = [];
var langForm = false;


function getFormInfoIndex( idForm ) {
  var index = false;
  for( var i = cogumeloFormControllerFormsInfo.length - 1; i >= 0; i-- ) {
    if( cogumeloFormControllerFormsInfo[i].idForm === idForm ) {
      index = i;
      break;
    }
  }

  return index;
}


function setFormInfo( idForm, key, value ) {
  var index = getFormInfoIndex( idForm );
  if( index === false ) {
    index = cogumeloFormControllerFormsInfo.length;
    cogumeloFormControllerFormsInfo[ index ] = { idForm: idForm };
  }
  cogumeloFormControllerFormsInfo[ index ][ key ] = value;
}


function getFormInfo( idForm, key ) {
  var result = null;

  var index = getFormInfoIndex( idForm );

  if( index !== false ) {
    result = cogumeloFormControllerFormsInfo[ index ][ key ];
  }

  return result;
}


function createFilesTitleField( idForm ) {
  console.log( 'createFilesTitleField( '+idForm+' )' );
  $( 'input:file[form="'+idForm+'"]' ).after( function() {
    console.log( this );

    fileField = this;
    langs = ( typeof( langAvailable ) == 'object' ) ? langAvailable : [''];
    html = '';

    $.each( langs, function( i, lang ) {
      name = ( lang !== '' ) ? fileField.name+'_'+lang : fileField.name;
      filefielddata = ( lang !== '' ) ? 'fm_title_'+lang : 'fm_title';
      classLang = ( lang !== '' ) ? 'js-tr js-tr-'+lang+' ' : '';
      html += '<div class="cgmMForm-wrap cgmMForm-field-titleFileField_'+name+'">'+"\n"+
        '<label class="cgmMForm'+classLang+'">Alt-Title</label>'+"\n"+
        '<input name="titleFileField_'+name+'" value="'+$( fileField ).data( filefielddata )+'" '+
        'data-ffid="'+idForm+'" data-ffname="'+fileField.name+'" data-ffdata="'+filefielddata+'" '+
        'form="fileFields_'+idForm+'" class="cgmMForm-field cgmMForm-field-titleFileField '+classLang+'" type="text">'+"\n"+
        '</div>'+"\n";
    });

    return html;
  });

  $( 'input.cgmMForm-field-titleFileField' ).on( 'change', function() {
    $titleFileField = $( this );
    $titleData = $titleFileField.data();
    $fileField = $( 'input[form="'+$titleData.ffid+'"][name="'+$titleData.ffname+'"]' );
    $fileField.attr( 'data-'+$titleData.ffdata, $titleFileField.val() );
    $fileField.data( $titleData.ffdata, $titleFileField.val() );
    // Doble escritura para asegurar porque funcionan distinto
  });
}


function bindForm( idForm ) {
  console.log( 'bindForm( '+idForm+' )' );
  $inputFileFields = $( 'input:file[form="'+idForm+'"]' );
  if( $inputFileFields.length ) {
    if( !window.File ) {
      // File - provides readonly information such as name, file size, mimetype
      alert('Tu navegador aún no tiene soporte HTML5 para el envío de ficheros. Actualiza a versiones recientes...');
    }
    $inputFileFields.on( 'change', processInputFileField );
  }

  $( '.addGroupElement[data-form_id="'+idForm+'"]' ).on( 'click', addGroupElement ).css( 'cursor', 'pointer' );
  $( '.removeGroupElement[data-form_id="'+idForm+'"]' ).on( 'click', removeGroupElement ).css( 'cursor', 'pointer' );
}


function unbindForm( idForm ) {
  console.log( 'unbindForm( '+idForm+' )' );
  $( 'input:file[form="'+idForm+'"]' ).off( 'change' );
  $( '.addGroupElement[data-form_id="'+idForm+'"]' ).off( 'click' );
  $( '.removeGroupElement[data-form_id="'+idForm+'"]' ).off( 'click' );
}

/*
  Gestión de informacion en cliente (FIN)
*/



function setValidateForm( idForm, rules, messages ) {

  $.validator.setDefaults({
    errorPlacement: function(error, element) {
      console.log( 'Executando validate.errorPlacement:' );
      console.log( error, element );
      //console.log( 'Busco #JQVMC-'+$( error[0] ).attr('id')+', .JQVMC-'+$( error[0] ).attr('id') );
      $msgContainer = $( '#JQVMC-'+$( error[0] ).attr('id')+', .JQVMC-'+$( error[0] ).attr('id') );
      if ( $msgContainer.length > 0 ) {
        $msgContainer.append( error );
      }
      else {
        error.insertAfter( element );
      }
    }
  });


  console.log( 'VALIDATE: ', $( '#'+idForm ) );
  var $validateForm = $( '#'+idForm ).validate({
    // debug: true,
    errorClass: 'formError',
    rules: rules,
    messages: messages,
    submitHandler: function ( form ) {
      console.log( 'Executando validate.submitHandler...' );
      $( form ).find( '[type="submit"]' ).attr('disabled', 'disabled');
      $( form ).find( '.submitRun' ).show();
      $.ajax( {
        contentType: 'application/json', processData: false,
        data: JSON.stringify( $( form ).serializeFormToObject() ),
        type: 'POST', url: $( form ).attr( 'action' ),
        dataType : 'json'
      } )
      .done( function ( response ) {
        console.log( 'Executando validate.submitHandler.done...' );
        //console.log( response );
        if( response.result === 'ok' ) {
          // alert( 'Form Submit OK' );
          console.log( 'Form Done: OK' );
          formDoneOk( form, response );
        }
        else {
          console.log( 'Form Done: ERROR' );
          formDoneError( form, response );
        }
        $( form ).find( '[type="submit"]' ).removeAttr('disabled');
        $( form ).find( '.submitRun' ).hide();
      } ); // /.done
      return false; // required to block normal submit since you used ajax
    }
  }); // $validateForm =
  console.log( 'VALIDATE FEITO' );

  // Bind file fields and group actions...
  bindForm( idForm );

  // Save validate instance for this Form
  setFormInfo( idForm, 'validateForm', $validateForm );

  createFilesTitleField( idForm );

  // Si hay idiomas, buscamos campos multi-idioma en el form y los procesamos
  createSwitchFormLang( idForm );

  return $validateForm;
} // function setValidateForm( idForm, rules, messages )


function formDoneOk( form, response ) {
  console.log( 'formDoneOk' );
  console.log( response );

  var $validateForm = getFormInfo( $( form ).attr( 'id' ), 'validateForm' );

  var successActions = response.success;
  if ( successActions.jsEval ) {
    eval( successActions.jsEval );
  }
  if ( successActions.accept ) {
    alert( successActions.accept );
  }
  if ( successActions.redirect ) {
    // Usando replace no permite volver a la pagina del form
    window.location.replace( successActions.redirect );
  }
  if ( successActions.reload ) {
    window.location.reload();
  }
  if ( successActions.resetForm ) {
    $( form )[0].reset();
  }
  // alert( 'Form Submit OK' );
}


function formDoneError( form, response ) {
  console.log( 'formDoneError' );
  console.log( response );

  var $validateForm = getFormInfo( $( form ).attr( 'id' ), 'validateForm' );

  for(var i in response.jvErrors) {
    errObj = response.jvErrors[i];
    console.log( errObj );

    if( errObj.fieldName !== false ) {
      if( errObj.JVshowErrors[ errObj.fieldName ] === false ) {
        $defMess = $validateForm.defaultMessage( errObj.fieldName, errObj.ruleName );
        if( typeof $defMess !== 'string' ) {
          $defMess = $defMess( errObj.ruleParams );
        }
        errObj.JVshowErrors[ errObj.fieldName ] = $defMess;
      }
      console.log( 'showErrors: ' + errObj.JVshowErrors );
      $validateForm.showErrors( errObj.JVshowErrors );
    }
    else {
      console.log( errObj.JVshowErrors );
      showErrorsValidateForm( $( form ), errObj.JVshowErrors.msgText, errObj.JVshowErrors.msgClass );
    }
  } // for(var i in response.jvErrors)

  // if( response.formError !== '' ) $validateForm.showErrors( {'submit': response.formError} );
}


function showErrorsValidateForm( $form, msgText, msgClass ) {
  // Solo se muestran los errores pero no se marcan los campos

  // Replantear!!!

  console.log( 'showErrorsValidateForm: '+msgClass+' , '+msgText );
  msgLabel = '<label class="formError">'+msgText+'</label>';
  $msgContainer = false;
  if( msgClass !== false ) {
    $msgContainer = $( '.JQVMC-'+msgClass );
  }
  if( $msgContainer !== false && $msgContainer.length > 0 ) {
    $msgContainer.append( msgLabel );
  }
  else {
    $form.append( msgLabel );
  }
}



/*
***  FICHEROS  ***
*/

function processInputFileField( evnt ) {
  var files = evnt.target.files; // FileList object
  var valid = checkInputFileField( files, evnt.target.form.id, evnt.target.name );

  if( valid ) {
    var cgIntFrmId = $( '#' + evnt.target.form.id ).attr( 'data-token_id' );
    for (var i = 0, file; (file = files[i]); i++) {
      uploadFile( file, evnt.target.form.id, evnt.target.name, cgIntFrmId );
    }
  }
} // function processInputFileField( evnt )


function checkInputFileField( files, idForm, fieldName ) {
  console.log( 'checkInputFileField()' );
  console.log( files );
  console.log( fieldName );
  var $validateForm = getFormInfo( idForm, 'validateForm' );

  $fileField = $( 'input[name="' + fieldName + '"][form="' + idForm + '"]' );
  $( '#' + $fileField.attr('id') + '-error' ).remove();

  var valRes = $validateForm.element( 'input[name="' + fieldName + '"][form="' + idForm + '"]' );

  // Mostrando informacion obtenida del navegador
  /*
  for( var i = 0, f; (f = files[i]); i++ ) {
    $( '#list' ).before( '<div>' + escape(f.name) + ' (' + f.type + ') ' + f.size + ' bytes</div>' );
  }
  */

  return valRes;
} // function checkInputFileField( files, idForm, fieldName )


function uploadFile( file, idForm, fieldName, cgIntFrmId ) {
  console.log( 'uploadFile: ', file );

  var formData = new FormData();
  formData.append( 'ajaxFileUpload', file );
  formData.append( 'idForm', idForm );
  formData.append( 'fieldName', fieldName );
  formData.append( 'cgIntFrmId', cgIntFrmId );

  $( '.'+fieldName+'-info[data-form_id="'+idForm+'"]' ).show();

  $.ajax({
    url: '/cgml-form-file-upload', type: 'POST',
    // Form data
    data: formData,
    //Options to tell jQuery not to process data or worry about content-type.
    cache: false, contentType: false, processData: false,
    // Custom XMLHttpRequest
    xhr: function() {
      var myXhr = $.ajaxSettings.xhr();
      if(myXhr.upload){ // Check if upload property exists for handling the progress of the upload
        myXhr.upload.addEventListener(
          'progress',
          function progressHandler( evnt ) {
            var percent = Math.round( (evnt.loaded / evnt.total) * 100 );

            // TODO: Poñer idForm e fieldName
            $( '.contact-file-info .wrap .progressBar' ).val( percent );
            $( '.contact-file-info .wrap .status' ).html( 'Cargando el fichero...' );

            //$( '#progressBar' ).val( percent );
            //$( '#status' ).html( percent + '% uploaded... please wait' );
            //$( '#loaded_n_total' ).html( 'Uploaded ' + evnt.loaded + ' bytes of ' + evnt.total );
          },
          false
        );
      }
      return myXhr;
    },
    beforeSend: function beforeSendHandler( $jqXHR, $settings ) {
      // $( '#status' ).html( 'Upload Failed (' + $textStatus + ')' );
    },
    success: function successHandler( $jsonData, $textStatus, $jqXHR ) {
      var idForm = $jsonData.moreInfo.idForm;
      var fieldName = $jsonData.moreInfo.fieldName;
      $( '.'+fieldName+'-info[data-form_id="'+idForm+'"] .wrap .progressBar' ).hide();

      if( $jsonData.result === 'ok' ) {
        fileFieldToOk( idForm, fieldName, $jsonData.moreInfo.fileName, false );
      }
      else {
        console.log( 'uploadFile ERROR' );
        $( '.'+fieldName+'-info[data-form_id="'+idForm+'"] .wrap .status' ).html( 'Error cargando el fichero.' );

        $validateForm = getFormInfo( idForm, 'validateForm' );
        console.log( $validateForm );

        for(var i in $jsonData.jvErrors) {
          errObj = $jsonData.jvErrors[i];
          console.log( errObj );

          if( errObj.fieldName !== false ) {
            if( errObj.JVshowErrors[ errObj.fieldName ] === false ) {
              $defMess = $validateForm.defaultMessage( errObj.fieldName, errObj.ruleName );
              if( typeof $defMess !== 'string' ) {
                $defMess = $defMess( errObj.ruleParams );
              }
              errObj.JVshowErrors[ errObj.fieldName ] = $defMess;
            }
            console.log( errObj.JVshowErrors );
            $validateForm.showErrors( errObj.JVshowErrors );
          }
          else {
            console.log( errObj.JVshowErrors );
            showErrorsValidateForm( $( form ), errObj.JVshowErrors.msgText, errObj.JVshowErrors.msgClass );
          }

        }
        // if( $jsonData.formError !== '' ) $validateForm.showErrors( {'submit': $jsonData.formError} );
      }

    },
    error: function errorHandler( $jqXHR, $textStatus, $errorThrown ) { // textStatus: timeout, error, abort, or parsererror
      console.log( 'uploadFile errorHandler', $jqXHR, $textStatus, $errorThrown );
      $( '.'+fieldName+'-info[data-form_id="'+idForm+'"] .status' ).html( 'Upload Failed (' + $textStatus + ')' );
    }
  });
} // function uploadFile( file, idForm, fieldName, cgIntFrmId )


function deleteFormFileEvent( evnt ) {
  console.log( 'deleteFormFileEvent: ', evnt );
  $fileField = $( evnt.target );
  var idForm = $fileField.attr( 'data-form_id' );
  var fieldName = $fileField.attr( 'data-fieldname' );
  var cgIntFrmId = $( '#' + idForm ).attr( 'data-token_id' );

  deleteFormFile( idForm, fieldName, cgIntFrmId );
} // function deleteFormFileEvent( evnt )


function deleteFormFile( idForm, fieldName, cgIntFrmId ) {
  console.log( 'deleteFormFile: ', idForm, fieldName, cgIntFrmId );
  var formData = new FormData();
  formData.append( 'execute', 'delete' );
  formData.append( 'idForm', idForm );
  formData.append( 'fieldName', fieldName );
  formData.append( 'cgIntFrmId', cgIntFrmId );

  $.ajax( {
    url: '/cgml-form-file-upload', type: 'POST',
    data: formData,
    //Options to tell jQuery not to process data or worry about content-type.
    cache: false, contentType: false, processData: false
  } )
  .done( function ( response ) {
    console.log( 'Executando deleteFormFile.done...' );
    console.log( response );
    if( response.result === 'ok' ) {

      fileFieldToInput( idForm, fieldName );

    }
    else {
      console.log( 'deleteFormFile.done...ERROR' );
      for(var i in response.jvErrors) {
        errObj = response.jvErrors[i];
        console.log( errObj );

        if( errObj.fieldName !== false ) {



        }
        else {
          console.log( errObj.JVshowErrors );
          showErrorsValidateForm( $( form ), errObj.JVshowErrors.msgText, errObj.JVshowErrors.msgClass );
        }

      } // for
    }
  } );
} // function deleteFormFile( idForm, fieldName, cgIntFrmId )


function fileFieldToOk( idForm, fieldName, fileName, fileModId ) {
  console.log( 'fileFieldToOk( '+idForm+', '+fieldName+', '+fileName+', '+fileModId+' )' );
  $fileField = $( 'input[name=' + fieldName + '][form="'+idForm+'"]' );
  $fileFieldWrap = $fileField.parents().find( '.cgmMForm-field-' + fieldName );
  // fileObj = $fileField[0].files[0];

  console.log( $fileField, $fileFieldWrap );

  $fileField.attr( 'readonly', 'readonly' );
  $fileField.prop( 'disabled', true );
  $fileField.hide();

  //$( '#'+fieldName+'-error[data-form_id="'+idForm+'"]' ).hide();
  $( '#' + $fileField.attr('id') + '-error' ).remove();


  $fileFieldInfo = $( '<div>' ).addClass( 'fileFieldInfo fileUploadOK formFileDelete' )
    .attr( { "data-fieldname": fieldName, "data-form_id": idForm } );

  // Element to send delete order
  $fileFieldInfo.append( $( '<i>' ).addClass( 'formFileDelete fa fa-trash' )
    .attr( { "data-fieldname": fieldName, "data-form_id": idForm } )
    .on('click', deleteFormFileEvent )
  );

  if( fileModId === false ) {
    $fileFieldInfo.append( '<div class="msgInfo">Información, Icono e esquina coa papelera</div>' );
    $fileFieldInfo.append( '<span class="msgText">"' + fileName + '" uploaded OK</span>' );
  }
  else {
    $fileFieldInfo.append( '<img class="tnImage" src="/cgmlformfilews/' + fileModId + '"></img>' );
    $fileFieldInfo.append( '<span class="msgText">"' + fileName + '"</span>' );
  }

  $fileFieldWrap.append( $fileFieldInfo );

  /*
  // Only process image files.
  if( fileObj.type.match('image.*') && fileObj.size < 5000000 ) {
    loadImageTh( fileObj, $fileFieldWrap );
  }
  */
}


function fileFieldToInput( idForm, fieldName ) {
  console.log( 'fileFieldToInput: ', idForm, fieldName );
  $fileField = $( 'input[name="' + fieldName + '"][form="'+idForm+'"]' );
  console.log( $fileField );
  $fileFieldWrap = $fileField.parents().find( '.cgmMForm-field-' + fieldName );

  $fileFieldWrap.find( '.fileUploadOK' ).remove();

  $fileField.removeAttr( 'readonly' );
  $fileField.prop( 'disabled', false ); //$fileField.removeProp( 'disabled' );
  $fileField.val( null );
  $fileField.show();
}


function loadImageTh( fileObj, $fileFieldWrap ) {
  var imageReader = new FileReader();
  // Closure to capture the file information.
  imageReader.onload = (
    function cargado( fileLoaded ) {
      return(
        function procesando( evnt ) {
          $fileFieldWrap.append('<div class="imageTh"><img class="imageTh" border="1" ' +
            ' style="max-width:50px; max-height:50px;" src="' + evnt.target.result + '"/></div>');
        }
      );
    }
  )( fileObj );

  // Read in the image file as a data URL.
  imageReader.readAsDataURL( fileObj );
} // function loadImageTh( fileObj, $fileFieldWrap )





/*
***  Agrupaciones de campos  ***
*/

function addGroupElement( evnt ) {
  console.log( 'addGroupElement:' );
  console.log( evnt );

  var myForm = evnt.target.closest("form");
  var idForm = $( myForm ).attr('id');
  var cgIntFrmId = $( myForm ).attr('data-token_id');
  var groupName = $( evnt.target ).attr('groupName');


  var formData = new FormData();
  formData.append( 'execute', 'getGroupElement' );
  formData.append( 'idForm', idForm );
  formData.append( 'cgIntFrmId', cgIntFrmId );
  formData.append( 'groupName', groupName );

  console.log( idForm );
  console.log( cgIntFrmId );
  console.log( groupName );

  // Desactivamos los bins del form durante el proceso
  unbindForm( idForm );

  $.ajax({
    url: '/cgml-form-group-element', type: 'POST',
    // Form data
    data: formData,
    //Options to tell jQuery not to process data or worry about content-type.
    cache: false, contentType: false, processData: false,
    // Custom XMLHttpRequest
    success: function successHandler( $jsonData, $textStatus, $jqXHR ) {

      console.log( 'getGroupElement success:' );
      console.log( $jsonData );

      var idForm = $jsonData.moreInfo.idForm;
      var groupName = $jsonData.moreInfo.groupName;

      $( '#' + idForm + ' .JQVMC-group-' + groupName + ' .formError' ).remove();

      if( $jsonData.result === 'ok' ) {
        console.log( 'getGroupElement OK' );
        console.log( 'idForm: ' + idForm + ' groupName: ' + groupName );

        $( $jsonData.moreInfo.htmlGroupElement ).insertBefore(
          '#' + idForm + ' .cgmMForm-group-' + groupName + ' .addGroupElement'
        );

        $.each( $jsonData.moreInfo.validationRules, function( fieldName, fieldRules ) {
          console.log( 'fieldName: ' + fieldName + ' fieldRules: ', fieldRules );
          console.log( 'ELEM: #' + idForm + ' .cgmMForm-field.cgmMForm-field-' + fieldName );
          $( '#' + idForm + ' .cgmMForm-field.cgmMForm-field-' + fieldName ).rules( 'add', fieldRules );
        });

        console.log( 'getGroupElement OK Fin' );
      }
      else {
        console.log( 'getGroupElement ERROR' );
        $validateForm = getFormInfo( idForm, 'validateForm' );
        console.log( $validateForm );
        errObj = $jsonData.jvErrors[0];
        console.log( errObj.JVshowErrors );
        showErrorsValidateForm( $( '#'+idForm ), errObj.JVshowErrors[0], 'group-' + groupName );
      }

      // Activamos los bins del form despues del proceso
      bindForm( idForm );

      console.log( 'getGroupElement success: Fin' );
    },
    error: function errorHandler( $jqXHR, $textStatus, $errorThrown ) { // textStatus: timeout, error, abort, or parsererror
      console.log( 'uploadFile errorHandler', $jqXHR, $textStatus, $errorThrown );
      $( '#status' ).html( 'ERROR: (' + $textStatus + ')' );

      // Activamos los bins del form despues del proceso
      bindForm( idForm );
    }
  });
} // function addGroupElement( evnt )


function removeGroupElement( evnt ) {
  console.log( 'removeGroupElement:' );
  console.log( evnt );

  var myForm = evnt.target.closest("form");
  var idForm = $( myForm ).attr('id');
  var cgIntFrmId = $( myForm ).attr('data-token_id');
  var groupName = $( evnt.target ).attr('groupName');
  var groupIdElem = $( evnt.target ).attr('groupIdElem');
  console.log( idForm );
  console.log( cgIntFrmId );
  console.log( groupName );
  console.log( groupIdElem );

  var formData = new FormData();
  formData.append( 'execute', 'removeGroupElement' );
  formData.append( 'idForm', idForm );
  formData.append( 'cgIntFrmId', cgIntFrmId );
  formData.append( 'groupName', groupName );
  formData.append( 'groupIdElem', groupIdElem );

  // Desactivamos los bins del form durante el proceso
  unbindForm( idForm );

  $.ajax({
    url: '/cgml-form-group-element', type: 'POST',
    // Form data
    data: formData,
    //Options to tell jQuery not to process data or worry about content-type.
    cache: false, contentType: false, processData: false,
    // Custom XMLHttpRequest
    success: function successHandler( $jsonData, $textStatus, $jqXHR ) {

      console.log( 'removeGroupElement success:' );
      console.log( $jsonData );

      var idForm = $jsonData.moreInfo.idForm;
      var groupName = $jsonData.moreInfo.groupName;

      $( '#' + idForm + ' .JQVMC-group-' + groupName + ' .formError' ).remove();

      if( $jsonData.result === 'ok' ) {
        console.log( 'removeGroupElement OK' );
        console.log( idForm, groupName, $jsonData.moreInfo.groupIdElem );
        console.log( '#' + idForm + ' .cgmMForm-groupElem_C_' + $jsonData.moreInfo.groupIdElem );
        $( '#' + idForm + ' .cgmMForm-groupElem_C_' + $jsonData.moreInfo.groupIdElem ).remove();
      }
      else {
        console.log( 'removeGroupElement ERROR' );
        $validateForm = getFormInfo( idForm, 'validateForm' );
        console.log( $validateForm );
        errObj = $jsonData.jvErrors[0];
        console.log( errObj.JVshowErrors );
        showErrorsValidateForm( $( '#'+idForm ), errObj.JVshowErrors[0], 'group-' + groupName );
      }

      // Activamos los bins del form despues del proceso
      bindForm( idForm );

      console.log( 'removeGroupElement success: Fin' );
    },
    error: function errorHandler( $jqXHR, $textStatus, $errorThrown ) { // textStatus: timeout, error, abort, or parsererror
      console.log( 'uploadFile errorHandler', $jqXHR, $textStatus, $errorThrown );
      $( '#status' ).html( 'ERROR: (' + $textStatus + ')' );

      // Activamos los bins del form despues del proceso
      bindForm( idForm );
    }
  });
} // function removeGroupElement( evnt )



function activateHtmlEditor( idForm ) {
  console.log( 'activateHtmlEditor: ' + idForm );
  console.log( idForm );

  $( 'textarea.cgmMForm-htmlEditor[form="'+idForm+'"]' ).each(
    function( index ) {
      var idName = $( this ).attr( 'id' );
      var CKcontent = CKEDITOR.replace( idName, {
        customConfig: '/cgml-form-htmleditor-config.js'
      } );
      CKcontent.on( 'change', function ( ev ) { document.getElementById( idName ).innerHTML = CKcontent.getData(); } );
    }
  );
}



function switchFormLang( idForm, lang ) {
  console.log( 'switchFormLang: '+lang );
  langForm = lang;
  $( '[form="'+idForm+'"].js-tr, [data-form_id="'+idForm+'"].js-tr, '+
    ' [form="fileFields_'+idForm+'"].js-tr, [data-form_id="fileFields_'+idForm+'"].js-tr' )
    .parent().hide();
  $( '[form="'+idForm+'"].js-tr.js-tr-'+lang+', [data-form_id="'+idForm+'"].js-tr.js-tr-'+lang+', '+
    ' [form="fileFields_'+idForm+'"].js-tr.js-tr-'+lang+', [data-form_id="fileFields_'+idForm+'"].js-tr.js-tr-'+lang )
    .parent().show();
  $( 'ul[data-form_id="'+idForm+'"].langSwitch li' ).removeClass( 'langActive' );
  $( 'ul[data-form_id="'+idForm+'"].langSwitch li.langSwitch-'+lang ).addClass( 'langActive' );
}

function createSwitchFormLang( idForm ) {
  console.log( 'createSwitchFormLang' );

  if( typeof( langAvailable ) == 'object' ) {
    var htmlLangSwitch = '';
    htmlLangSwitch += '<div class="langSwitch-wrap">';
    htmlLangSwitch += '<ul class="langSwitch" data-form_id="'+idForm+'">';
    $.each( langAvailable, function( index, lang ) {
      htmlLangSwitch += '<li class="langSwitch-'+lang+'" data-lang="'+lang+'">'+lang;
    });
    htmlLangSwitch += '</ul>';
    htmlLangSwitch += '<span class="langSwitchIcon"><i class="fa fa-flag fa-fw"></i></span>';
    htmlLangSwitch += '</div>';

    $( '[form="'+idForm+'"].cgmMForm-field.js-tr.js-tr-' + langDefault ).parent().before( htmlLangSwitch );
    $( '[form="fileFields_'+idForm+'"].cgmMForm-field.js-tr.js-tr-' + langDefault ).parent().before( htmlLangSwitch );

    switchFormLang( idForm, langDefault );

    $( 'ul[data-form_id="'+idForm+'"].langSwitch li' ).on( "click", function() {
      newLang = $( this ).data( 'lang' );
      if( newLang != langForm ) {
        switchFormLang( idForm, newLang );
      }
    });
  }
}


/*** Form lang select - End ***/
