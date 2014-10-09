


function setValidateForm( idForm, rules, messages ) {

  var $validateForm = $( '#'+idForm ).validate({

debug: true,

//groups: { ungrupo: "input1 input2" },

errorPlacement: function( place, element ) {
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

            };
            // if( response.formError !== '' ) $validateForm.showErrors( {"submit": response.formError} );
          }
          $( form ).find( '[type="submit"]' ).removeAttr("disabled");
        } );
        return false; // required to block normal submit since you used ajax
      }
  });

  console.log( $validateForm );
  return $validateForm
} // function


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