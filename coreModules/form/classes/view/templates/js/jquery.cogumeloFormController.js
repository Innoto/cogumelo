
/*
  Gestión de informacion en cliente
*/

var cogumeloFormControllerFormsInfo = [];

function getFormInfoIndex( idForm ) {
  var index = false;
  for( var i = cogumeloFormControllerFormsInfo.length - 1; i >= 0; i-- ) {
    if( cogumeloFormControllerFormsInfo[i].idForm === idForm ) {
      index = i;
      break;
    }
  };
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
  var value = false;
  var index = getFormInfoIndex( idForm );

  if( index !== false ) {
    value = cogumeloFormControllerFormsInfo[ index ][ key ];
  }

  return value;
}

/*
  Gestión de informacion en cliente (FIN)
*/



function setValidateForm( idForm, rules, messages ) {

  var $validateForm = $( '#'+idForm ).validate({

    // debug: true,

    //groups: { ungrupo: 'input1 input2' },

    errorPlacement: function( place, element ) {
      console.log( 'Executando validate.errorPlacement:' );
      console.log( place, element );
      $msgContainer = $( '#JQVMC-'+place.attr('id')+', .JQVMC-'+place.attr('id') );
      if ( $msgContainer.length > 0 ) {
        $msgContainer.append( place );
      }
      else {
        place.insertAfter( element );
      }
    },

    errorClass: 'formError',
    rules: rules,
    messages: messages,
    submitHandler:
      function ( form ) {
        console.log( 'Executando validate.submitHandler...' );
        $( form ).find( '[type="submit"]' ).attr('disabled', 'disabled');
        $.ajax( {
           contentType: 'application/json', processData: false,
           data: JSON.stringify( $( form ).serializeFormToObject() ),
           type: 'POST', url: $( form ).attr( 'action' ),
           dataType : 'json'
        } )
        .done( function ( response ) {
          console.log( 'Executando validate.submitHandler.done...' );
          console.log( response );
          if( response.result === 'ok' ) {
            var successActions = response.success;
            console.log( successActions )
            if ( successActions[ 'accept' ] ) {
              alert( successActions[ 'accept' ] );
            }
            if ( successActions[ 'redirect' ] ) {
              // Usando replace no permite volver a la pagina del form
              window.location.replace( successActions[ 'redirect' ] );
            }
            // alert( 'Form Submit OK' );
          }
          else {
            console.log( 'ERROR' );
            for(var i in response.jvErrors) {
              errObj = response.jvErrors[i];
              console.log( errObj );

              if( errObj[ 'fieldName' ] !== false ) {
                if( errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] === false ) {
                  $defMess = $validateForm.defaultMessage( errObj['fieldName'], errObj['ruleName'] );
                  if( typeof $defMess !== 'string' ) {
                    $defMess = $defMess( errObj['ruleParams'] );
                  }
                  errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] = $defMess;
                }
                console.log( errObj[ 'JVshowErrors' ] );
                $validateForm.showErrors( errObj[ 'JVshowErrors' ] );
              }
              else {
                console.log( errObj[ 'JVshowErrors' ] );
                showErrorsValidateForm( $( form ), errObj[ 'JVshowErrors' ][ 'msgText'], errObj[ 'JVshowErrors' ][ 'msgClass' ] );
              }

            }
            // if( response.formError !== '' ) $validateForm.showErrors( {'submit': response.formError} );
          }
          $( form ).find( '[type="submit"]' ).removeAttr('disabled');
        } );
        return false; // required to block normal submit since you used ajax
      }
  });

  // Bind input file fields: Validate, send, show, ...
  if( $( '#'+idForm+' input:file' ).length > 0 ) {
    bindFormInputFileFields( idForm );
  }

  // Save validate instance for this Form
  setFormInfo( idForm, 'validateForm', $validateForm );

  return $validateForm
} // function


function showErrorsValidateForm( $form, msgText, msgClass ) {
  // Solo se muestran los errores pero no se marcan los campos

  // Replantear!!!

  console.log( 'showErrorsValidateForm: '+msgClass+' , '+msgText );
  msgLabel = '<label class="formError">'+msgText+'</label>';
  $msgContainer = false;
  if( msgClass !== false ) {
    $msgContainer = $( '#JQVMC-'+msgClass+', .JQVMC-'+msgClass );
  }
  if( $msgContainer !== false && $msgContainer.length > 0 ) {
    $msgContainer.append( msgLabel );
  }
  else {
    $form.append( msgLabel );
  }
}





/**
*** FICHEROS ***
**/

function bindFormInputFileFields( idForm ) {

  if( !window.File ) {
    // File - provides readonly information such as name, file size, mimetype
    alert('Tu navegador aún no soporta el API File para el envío de ficheros. Actualiza a versiones recientes...');
  }

  $( '#' + idForm + ' input:file' ).on( 'change', processInputFileField );
} // function bindFormInputFileFields( idForm )


function processInputFileField( evnt ) {
  var files = evnt.target.files; // FileList object
  var valid = checkInputFileField( files, evnt.target.form.id, evnt.target.name );

  if( valid ) {
    var cgIntFrmId = $( '#' + evnt.target.form.id ).attr('sg');
    for (var i = 0, file; file = files[i]; i++) {
      uploadFile( file, evnt.target.form.id, evnt.target.name, cgIntFrmId );
    }
  }
} // function processInputFileField( evnt )



function checkInputFileField( files, idForm, fieldName ) {
  var $validateForm = getFormInfo( idForm, 'validateForm' );
  var valRes = $validateForm.element( 'input[name=' + fieldName + ']' );

  // Mostrando informacion obtenida del navegador
  for( var i = 0, f; f = files[i]; i++ ) {
    $('#list').before( '<div>' + escape(f.name) + ' (' + f.type + ') ' + f.size + ' bytes</div>' );
  }

  return valRes;
} // function procesarFiles



function uploadFile( file, idForm, fieldName, cgIntFrmId ) {
  console.log( 'uploadFile: ', file );

  var formData = new FormData();
  formData.append( 'ajaxFileUpload', file );
  formData.append( 'idForm', idForm );
  formData.append( 'fieldName', fieldName );
  formData.append( 'cgIntFrmId', cgIntFrmId );

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
            $( '#progressBar' ).val( percent );
            $( '#status' ).html( percent + '% uploaded... please wait' );
            $( '#loaded_n_total' ).html( 'Uploaded ' + evnt.loaded + ' bytes of ' + evnt.total );
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
      $( '#loaded_n_total' ).html( '' );
      $( '#progressBar' ).val( 0 );
      $( '#status' ).html( $jsonData.success );

      if( $jsonData.result === 'ok' ) {

        fileFieldToOk( $jsonData.moreInfo.idForm, $jsonData.moreInfo.fieldName );

      }
      else {
        console.log( 'uploadFile ERROR' );

        $validateForm = getFormInfo( $jsonData.moreInfo.idForm, 'validateForm' );
        console.log( $validateForm );

        for(var i in $jsonData.jvErrors) {
          errObj = $jsonData.jvErrors[i];
          console.log( errObj );

          if( errObj[ 'fieldName' ] !== false ) {
            if( errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] === false ) {
              $defMess = $validateForm.defaultMessage( errObj['fieldName'], errObj['ruleName'] );
              if( typeof $defMess !== 'string' ) {
                $defMess = $defMess( errObj['ruleParams'] );
              }
              errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] = $defMess;
            }
            console.log( errObj[ 'JVshowErrors' ] );
            $validateForm.showErrors( errObj[ 'JVshowErrors' ] );
          }
          else {
            console.log( errObj[ 'JVshowErrors' ] );
            showErrorsValidateForm( $( form ), errObj[ 'JVshowErrors' ][ 'msgText'], errObj[ 'JVshowErrors' ][ 'msgClass' ] );
          }

        }
        // if( $jsonData.formError !== '' ) $validateForm.showErrors( {'submit': $jsonData.formError} );
      }

    },
    error: function errorHandler( $jqXHR, $textStatus, $errorThrown ) { // textStatus: timeout, error, abort, or parsererror
      console.log( 'uploadFile errorHandler', $jqXHR, $textStatus, $errorThrown );
      $( '#status' ).html( 'Upload Failed (' + $textStatus + ')' );
    }
  });
} // function uploadFile( file, idForm, fieldName, cgIntFrmId )



function deleteFormFileEvent( evnt ) {
  $fileField = $( evnt.target );
  $form = $fileField.parents( 'form' );
  var idForm = $form.attr( 'id' );
  var fieldName = $fileField.attr( 'fieldName' );
  var cgIntFrmId = $form.attr( 'sg' );

  deleteFormFile( idForm, fieldName, cgIntFrmId );
} // function deleteFormFileEvent( evnt )



function deleteFormFile( idForm, fieldName, cgIntFrmId ) {
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

        if( errObj[ 'fieldName' ] !== false ) {



        }
        else {
          console.log( errObj[ 'JVshowErrors' ] );
          showErrorsValidateForm( $( form ), errObj[ 'JVshowErrors' ][ 'msgText'], errObj[ 'JVshowErrors' ][ 'msgClass' ] );
        }

      } // for
    }
  } );
} // function deleteFormFile( idForm, fieldName, cgIntFrmId )



function fileFieldToOk( idForm, fieldName ) {
  $fileFieldWrap = $( '#' + idForm + ' .cgmMForm-field-' + fieldName );
  $fileField = $( '#' + idForm + ' input[name=' + fieldName + ']' );
  fileObj = $fileField['0'].files['0'];

  $fileField.attr( 'readonly', 'readonly' );
  $fileField.prop( 'disabled', true );
  $fileField.hide();

  $fileFieldWrap.append( '<span class="fileUploadOK msgText">"' + fileObj.name + '" uploaded OK</span>' );
  $fileFieldWrap.append(
    $( '<div>' )
      .attr( 'fieldName', fieldName )
      .addClass( 'fileUploadOK formFileDelete' )
      .text( ' * BORRAR * ' )
      .on('click', deleteFormFileEvent )
  );
  // Only process image files.
  if( fileObj.type.match('image.*') && fileObj.size < 5000000 ) {
    loadImageTh( fileObj, $fileFieldWrap );
  }
}


function fileFieldToInput( idForm, fieldName ) {
  $fileField = $( '#' + idForm + ' input[name=' + fieldName + ']' );

  $( '#' + idForm + ' .cgmMForm-field-' + fieldName + ' .fileUploadOK').remove();

  $fileField.removeAttr( 'readonly' );
  $fileField.removeProp( 'disabled' );
  $fileField.val( null );
  $fileField.show();
}


function loadImageTh( fileObj, $container ) {
  var imageReader = new FileReader();
  // Closure to capture the file information.
  imageReader.onload = (
    function cargado( fileLoaded ) {
      return(
        function procesando( evnt ) {
          $container.append('<div class="fileUploadOK imageTh"><img class="imageTh" border="1" ' +
            ' style="max-width:50px; max-height:50px;" src="' + evnt.target.result + '"/></div>');
        }
      );
    }
  )( fileObj );

  // Read in the image file as a data URL.
  imageReader.readAsDataURL( fileObj );
} // function loadImageTh( fileObj, $container )

