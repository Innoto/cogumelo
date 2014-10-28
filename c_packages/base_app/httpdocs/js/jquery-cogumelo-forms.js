
var CogumeloForms = [];


function setValidateForm( idForm, rules, messages ) {

  var $validateForm = $( '#'+idForm ).validate({

    // debug: true,

    //groups: { ungrupo: "input1 input2" },

    errorPlacement: function( place, element ) {
      console.log( 'errorPlacement:' );
      console.log( place, element );
      $msgContainer = $( '#JQVMC-'+place.attr('id')+', .JQVMC-'+place.attr('id') );
      if ( $msgContainer.length > 0 ) {
        $msgContainer.append( place );
      }
      else {
        place.insertAfter( element );
      }
    },

    errorClass: "formError",
    rules: rules,
    messages: messages,
    submitHandler:
      function ( form ) {
        $( form ).find( '[type="submit"]' ).attr("disabled", "disabled");
        $.ajax( {
           contentType: 'application/json', processData: false,
           data: JSON.stringify( $( form ).serializeFormToObject() ),
           type: 'POST', url: $( form ).attr( 'action' ),
           dataType : 'json'
        } )
        .done( function ( response ) {
          console.log( response );
          if( response.success == 'success' ) {
            alert( 'Form Submit OK' );
          }
          else {
            console.log( 'ERROR' );
            for(var i in response.jvErrors) {
              errObj = response.jvErrors[i];
              console.log( errObj );

              if( errObj[ 'fieldName' ] !== false ) {
                if( errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] === '' ) {
                  $defMess = $validateForm.defaultMessage( errObj['fieldName'], errObj['msgRule'] );
                  if( typeof $defMess !== "string" ) {
                    $defMess = $defMess( errObj['ruleParams'] );
                  }
                  errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] = $defMess;
                }
                console.log( errObj[ 'JVshowErrors' ] );
                $validateForm.showErrors( errObj[ 'JVshowErrors' ] );
                console.log( 'Msg cargado...' );
              }
              else {
                console.log( errObj[ 'JVshowErrors' ] );
                showErrorsValidateForm( errObj[ 'JVshowErrors' ][ 'msgClass' ], errObj[ 'JVshowErrors' ][ 'msgText'], $( form ) );
                console.log( 'Msg cargado...' );
              }

            }
            // if( response.formError !== '' ) $validateForm.showErrors( {"submit": response.formError} );
          }
          $( form ).find( '[type="submit"]' ).removeAttr("disabled");
        } );
        return false; // required to block normal submit since you used ajax
      }
  });
  console.log( $validateForm );

  if( $( '#'+idForm+' input:file' ).length > 0 ) {
    bindFormInputFiles( idForm );
  }

  CogumeloForms[ CogumeloForms.length ] = { idForm: idForm, validateForm: $validateForm };

  return $validateForm
} // function


function getValidateForm( idForm ) {
  $validateForm = null;
  for (var i = CogumeloForms.length - 1; i >= 0; i--) {
    if( CogumeloForms[i].idForm === idForm ) {
      $validateForm = CogumeloForms[i].validateForm;
      break;
    }
  };
  return $validateForm
}

function showErrorsValidateForm( msgClass, msgText, $form ) {
  // Solo se muestran los errores pero no se marcan los campos

  // Replantear!!!

  console.log( 'showErrorsValidateForm: '+msgClass+' , '+msgText );
  msgLabel = '<label class="formError">'+msgText+'</label>';
  $msgContainer = $( '#JQVMC-'+msgClass+', .JQVMC-'+msgClass );
  if ( $msgContainer.length > 0 ) {
    $msgContainer.append( msgLabel );
  }
  else {
    $form.append( msgLabel );
  }

}






/**
*** FICHEROS ***
**/


function bindFormInputFiles( idForm ) {
  console.log( 'bindFormInputFiles' );

  // Check for the various File API support.
  if( !(window.File && window.FileReader && window.FileList && window.Blob) ) {
    alert('Tu navegador no soporta alguna de las características necesarias para el envío de ficheros.');
  }

  document.getElementById('inputFicheiro').addEventListener('change', processInputFieldFile, false);

  /*
  // Setup the dnd listeners.
  var dropZone = document.getElementById('drop_zone');
  dropZone.addEventListener('dragover', handleDragOver, false);
  dropZone.addEventListener('drop', handleFileDrop, false);
  */
}



// Ficheros seleccionados con el boton
function processInputFieldFile( evt ) {
  console.log( 'processInputFieldFile' );
  console.log( evt );

  var files = evt.target.files; // FileList object

  var valid = checkInputFieldFile( files, evt.target.form.id, evt.target.name );

  var cgIntFrmId = $( '#' + evt.target.form.id ).attr('sg');

  if( valid ) {
    for (var i = 0, file; file = files[i]; i++) {
      uploadFile( file, evt.target.form.id, evt.target.name, cgIntFrmId );
    } // for files[i]
  }
}

/*
// Ficheros "soltados" sobre un area
function handleFileDrop(evt) {
  console.log( 'handleFileDrop' );
  console.log( evt );
  evt.stopPropagation();
  evt.preventDefault();
  var files = evt.dataTransfer.files; // FileList object.
  checkInputFieldFile( files, evt.target.form.id );
}
function handleDragOver(evt) {
  evt.stopPropagation();
  evt.preventDefault();
  evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
}
*/


function checkInputFieldFile( files, idForm, nameField ) {
  console.log( 'checkInputFieldFile' );

  var $validateForm = getValidateForm( idForm );
  var valRes = $validateForm.element( 'input[name='+nameField+']' );

  console.log( 'checkInputFieldFile - valRes: ', valRes );

  for (var i = 0, f; f = files[i]; i++) {
    console.log( f );

    // Only process image files.
    if (f.type.match('^image/.*')) {
      var reader = new FileReader();

      // Closure to capture the file information.
      reader.onload = (function cargado(theFile) {
        return function procesando(e) {
          console.log( 'Procesando e' );
          console.log( e );
          // Render thumbnail.
          var span = document.createElement('div');
          span.innerHTML = ['<strong>', escape(theFile.name), '</strong> (',
            theFile.type || 'n/a', ') - ',
            theFile.size, ' bytes, last modified: ',
            theFile.lastModifiedDate ? theFile.lastModifiedDate.toLocaleDateString() : 'n/a',
            '<br><img border="1" style="max-width:50px; max-height:50px;" src="', e.target.result,
            '" title="', escape(theFile.name), '"/>'].join('');
          document.getElementById('list').insertBefore(span, null);
        };
      })(f);

      // Read in the image file as a data URL.
      reader.readAsDataURL(f);
    }
    else {
      var span = document.createElement('div');
      span.innerHTML = ['<strong>', escape(f.name), '</strong> (',
        f.type || 'n/a', ') - ',
        f.size, ' bytes, last modified: ',
        f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a'].join('');
      document.getElementById('list').insertBefore(span, null);
    }


  } // for files[i]

  return valRes;
} // function procesarFiles




/* Video Tutorial: http://www.youtube.com/watch?v=EraNFJiY0Eg */

function uploadFile( file, idForm, nameField, cgIntFrmId ) {
  console.log( 'uploadFile: ', file );
//  var file = document.getElementById("inputFicheiro").files[0];

  var formdata = new FormData();
  formdata.append("ajaxFileUpload", file);
  formdata.append("idForm", idForm);
  formdata.append("nameField", nameField);
  formdata.append("cgIntFrmId", cgIntFrmId);

  var ajax = new XMLHttpRequest();
  ajax.upload.addEventListener("progress", progressHandler, false);
  ajax.addEventListener("load", completeHandler, false);
  ajax.addEventListener("error", errorHandler, false);
  ajax.addEventListener("abort", abortHandler, false);
  ajax.open("POST", "/ajax_file_uploadV2");
  ajax.send(formdata);

  ajax.onreadystatechange = function() {
    console.log( 'onreadystatechange: ', ajax );
  }

}

function progressHandler(event) {
  console.log( 'progressHandler' );
  console.log( event );
  document.getElementById("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
  var percent = (event.loaded / event.total) * 100;
  document.getElementById("progressBar").value = Math.round(percent);
  document.getElementById("status").innerHTML = Math.round(percent)+"% uploaded... please wait";
}

function completeHandler(event) {
  console.log( 'completeHandler' );
  console.log( event );
  document.getElementById("loaded_n_total").innerHTML = "";
  document.getElementById("progressBar").value = 0;
  document.getElementById("status").innerHTML = event.target.responseText;
}

function errorHandler(event) {
  console.log( 'errorHandler' );
  console.log( event );
  document.getElementById("status").innerHTML = "Upload Failed";
}

function abortHandler(event) {
  console.log( 'abortHandler' );
  console.log( event );
  document.getElementById("status").innerHTML = "Upload Aborted";
}



