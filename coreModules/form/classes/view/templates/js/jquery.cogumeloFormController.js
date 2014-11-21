
var CogumeloForms = [];


function setFormInfo( idForm, $validateForm ) {
  CogumeloForms[ CogumeloForms.length ] = { idForm: idForm, validateForm: $validateForm };
}


function getFormInfo( idForm ) {
  $validateForm = null;
  for (var i = CogumeloForms.length - 1; i >= 0; i--) {
    if( CogumeloForms[i].idForm === idForm ) {
      $validateForm = CogumeloForms[i].validateForm;
      break;
    }
  };
  return $validateForm
}


function setValidateForm( idForm, rules, messages ) {

  var $validateForm = $( '#'+idForm ).validate({

    // debug: true,

    //groups: { ungrupo: "input1 input2" },

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

    errorClass: "formError",
    rules: rules,
    messages: messages,
    submitHandler:
      function ( form ) {
        console.log( 'Executando validate.submitHandler...' );
        $( form ).find( '[type="submit"]' ).attr("disabled", "disabled");
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
                showErrorsValidateForm( $( form ), errObj[ 'JVshowErrors' ][ 'msgText'], errObj[ 'JVshowErrors' ][ 'msgClass' ] );
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

  // Bind input file fields: Validate, send, show, ...
  if( $( '#'+idForm+' input:file' ).length > 0 ) {
    bindFormInputFiles( idForm );
  }

  // Save validate instance for this Form
  setFormInfo( idForm, $validateForm );

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

function bindFormInputFiles( idForm ) {
  console.log( 'bindFormInputFiles' );

  // Check for the various File API support.
  if( !window.File ) {
    // File - provides readonly information such as name, file size, mimetype
    alert('Tu navegador aún no soporta el API File para el envío de ficheros. Actualiza a versiones recientes...');
  }

  $( '#' + idForm + ' input:file' ).on( 'change', processInputFieldFile );
} // function bindFormInputFiles( idForm )


function processInputFieldFile( evnt ) {
  console.log( 'processInputFieldFile' );
  console.log( evnt );

  var files = evnt.target.files; // FileList object

  var valid = checkInputFieldFile( files, evnt.target.form.id, evnt.target.name );

  if( valid ) {
    var cgIntFrmId = $( '#' + evnt.target.form.id ).attr('sg');
    for (var i = 0, file; file = files[i]; i++) {
      uploadFile( file, evnt.target.form.id, evnt.target.name, cgIntFrmId );
    }
  }
} // function processInputFieldFile( evnt )



function checkInputFieldFile( files, idForm, fieldName ) {
  console.log( 'checkInputFieldFile' );

  var $validateForm = getFormInfo( idForm );
  var valRes = $validateForm.element( 'input[name=' + fieldName + ']' );
  console.log( 'checkInputFieldFile - valRes: ', valRes );

  // Mostrando informacion obtenida del navegador
  for( var i = 0, f; f = files[i]; i++ ) {
    console.log( f );
    $('#list').before( '<div>' + escape(f.name) + ' (' + f.type + ') ' + f.size + ' bytes</div>' );
  }

  return valRes;
} // function procesarFiles



function uploadFile( file, idForm, fieldName, cgIntFrmId ) {
  console.log( 'uploadFile: ', file );

  var formData = new FormData();
  formData.append("ajaxFileUpload", file);
  formData.append("idForm", idForm);
  formData.append("fieldName", fieldName);
  formData.append("cgIntFrmId", cgIntFrmId);

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
            console.log( 'progressHandler', evnt );
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
      console.log( 'beforeSendHandler', $jqXHR, $settings );
      // $( '#status' ).html( 'Upload Failed (' + $textStatus + ')' );
    },
    success: function successHandler( $jsonData, $textStatus, $jqXHR ) {
      console.log( 'Executando uploadFile.success...' );
      console.log( 'jsonData: ', $jsonData );
      console.log( 'jqXHR: ', $jqXHR );
      $( '#loaded_n_total' ).html( '' );
      $( '#progressBar' ).val( 0 );
      $( '#status' ).html( $jsonData.success );

      $validateForm = getFormInfo( $jsonData.moreInfo.idForm );

      console.log( $validateForm );
      if( $jsonData.result === 'ok' ) {

        $fileFieldWrap = $( '#' + $jsonData.moreInfo.idForm + ' .cgmMForm-field-' + $jsonData.moreInfo.fieldName );
        $fileField = $( '#' + $jsonData.moreInfo.idForm + ' input[name=' + $jsonData.moreInfo.fieldName + ']' );
        fileObj = $fileField['0'].files['0'];
        // console.log( 'fileField: ', $fileField );

        $fileFieldWrap.css( 'color', 'green' );
        $fileField.replaceWith( '<span class="fileUploadOK">"' + $jsonData.moreInfo.fileName + '" uploaded OK</span>' );

        $fileFieldWrap.append(
          $( '<spam>' )
            .attr( 'fieldName', $jsonData.moreInfo.fieldName )
            .addClass( 'formFileDelete' )
            .text( ' * BORRAR * ' )
            .on("click", deleteFormFileEvent )
        );

        // Only process image files.
        if( fileObj.type.match('image.*') && fileObj.size < 5000000 ) {
          loadImageTh( fileObj, $fileFieldWrap );
        }
      }
      else {
        console.log( 'ERROR' );
        for(var i in $jsonData.jvErrors) {
          errObj = $jsonData.jvErrors[i];
          console.log( errObj );

          if( errObj[ 'fieldName' ] !== false ) {
            if( errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] === false ) {
              $defMess = $validateForm.defaultMessage( errObj['fieldName'], errObj['ruleName'] );
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
            showErrorsValidateForm( $( form ), errObj[ 'JVshowErrors' ][ 'msgText'], errObj[ 'JVshowErrors' ][ 'msgClass' ] );
            console.log( 'Msg cargado...' );
          }

        }
        // if( $jsonData.formError !== '' ) $validateForm.showErrors( {"submit": $jsonData.formError} );
      }

    },
    error: function errorHandler( $jqXHR, $textStatus, $errorThrown ) { // textStatus: timeout, error, abort, or parsererror
      console.log( 'errorHandler', $jqXHR, $textStatus, $errorThrown );
      $( '#status' ).html( 'Upload Failed (' + $textStatus + ')' );
    }
  });

} // function uploadFile( file, idForm, fieldName, cgIntFrmId )



function loadImageTh( fileObj, $container ) {
  var imageReader = new FileReader();

  // Closure to capture the file information.
  imageReader.onload = (
    function cargado( fileLoaded ) {
      // console.log( 'cargado', fileLoaded );
      return(
        function procesando( evnt ) {
          // console.log( 'procesando', evnt );
          $container.append('<div class="imageTh"><img class="imageTh" border="1" ' +
            ' style="max-width:50px; max-height:50px;" src="' + evnt.target.result + '"/>');
        }
      );
    }
  )( fileObj );

  // Read in the image file as a data URL.
  imageReader.readAsDataURL( fileObj );
} // function loadImageTh( fileObj, $container )



function deleteFormFileEvent( evnt ) {
  console.log( 'deleteFormFileEvent' );
  console.log( evnt );
  //console.log( evnt.target.attr( 'fieldName' ) );

  /*
  var files = evnt.target.files; // FileList object

  var valid = checkInputFieldFile( files, evnt.target.form.id, evnt.target.name );

  if( valid ) {
    var cgIntFrmId = $( '#' + evnt.target.form.id ).attr('sg');
    for (var i = 0, file; file = files[i]; i++) {
      uploadFile( file, evnt.target.form.id, evnt.target.name, cgIntFrmId );
    }
  }
  */

} // function deleteFormFileEvent( evnt )



function deleteFormFile( idForm, fieldName, cgIntFrmId ) {
  console.log( 'deleteFile: ', file );

  var formData = new FormData();
  formData.append( 'execute', 'delete');
  formData.append( 'idForm', idForm);
  formData.append( 'fieldName', fieldName);
  formData.append( 'cgIntFrmId', cgIntFrmId);

  $.ajax( {
    url: '/cgml-form-file-upload', type: 'POST',
    data: formData, cache: false
  } )
  .done( function ( response ) {
    console.log( 'Executando deleteFormFile.done...' );
    console.log( response );
    if( response.result === 'ok' ) {
      var successActions = response.success;
      console.log( successActions )

      alert( 'Fichero borrado OK' );
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
          console.log( 'Msg cargado...' );
        }

      } // for
    }
  } );
} // function deleteFormFile( idForm, fieldName, cgIntFrmId )

