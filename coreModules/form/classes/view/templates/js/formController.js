/**
 *  Gestión de formularios en cliente
 */
var cogumelo = cogumelo || {};



cogumelo.formControllerInfo = cogumelo.formControllerInfo || new Object({

  formsInfo: {}, // GLOBAL: cogumelo.formControllerClass.formsInfo

  setFormInfo: function setFormInfo( idForm, key, value ) {
    console.log( '* setFormInfo( ',idForm, key, value );

    if( typeof cogumelo.formControllerInfo.formsInfo[idForm] === 'undefined' ) {
      cogumelo.formControllerInfo.formsInfo[idForm] = { idForm: idForm };
    }

    cogumelo.formControllerInfo.formsInfo[idForm][key] = value;
  },

  getFormInfo: function getFormInfo( idForm, key ) {
    console.log( '* getFormInfo( ',idForm, key );
    var result = null;

    // var index = getFormInfoIndex( idForm );

    if( typeof cogumelo.formControllerInfo.formsInfo[idForm] !== 'undefined' ) {
      if( typeof key === 'undefined' ) {
        result = this.formsInfo[idForm];
      }
      else if( typeof cogumelo.formControllerInfo.formsInfo[idForm][key] !== 'undefined' ) {
        result = this.formsInfo[idForm][key];
      }
    }

    return result;
  }
}); // cogumelo.formControllerInfo



cogumelo.formControllerClass = cogumelo.formControllerClass || function( idFormParam, options ) {
  console.log( '* formControllerClass: ', idFormParam, options );

  var that = this;

  that.idForm = idFormParam;
  that.jqForm = $( '#'+that.idForm );
  that.cgIntFrmId = that.jqForm.attr('data-token_id');


  that.submitElementName = false;
  that.submitActionName = false;
  that.submitActionValue = false;

  that.validateObj = null; // jQuery Validator instance

  that.keepAliveTimer = null; // Para lanzar formKeepAlive cuando pasa el 90% del tiempo session_lifetime

  that.fileGroup = [];

  that.langAvailableIds = ( typeof( cogumelo.publicConf.langAvailableIds ) === 'object' ) ? cogumelo.publicConf.langAvailableIds : [''];
  that.langDefault = cogumelo.publicConf.langDefault;
  that.langSession = cogumelo.publicConf.C_LANG;

  that.langSwitchActive = false;

  that.formDefOpts = new Object({
    keepAliveTime: 0, // minutos
    marginTop: 150, // Default marginTop (scroll functions)
    htmlEditor: false,
    enterSubmit: false, // Submit on Enter
  });

  that.formOpts = $.extend( true, {}, that.formDefOpts, options );


  // Save this controller instance
  cogumelo.formControllerInfo.setFormInfo( that.idForm, 'controller', that );



  that.setValidateForm = function setValidateForm( rules, messages ) {

    console.log( '* setValidateForm VALIDATE: ', that.idForm, $( '#'+that.idForm ) );

    that.validateObj = that.jqForm.validate({
      // debug: true,
      errorPlacement: function( error, element ) {
        console.log( '* JQV errorPlacement:', that.idForm, error, element );
        var $msgContainer = $( '#JQVMC-'+$( error[0] ).attr('id')+', .JQVMC-'+$( error[0] ).attr('id') );
        if( $msgContainer.length > 0 ) {
          $msgContainer.append( error );
        }
        else {
          error.insertAfter( element );
        }
      },
      showErrors: function( errorMap, errorList ) {
        console.log( '* JQV showErrors:', that.idForm, errorMap, errorList );
        // Lanzamos el metodo original
        this.defaultShowErrors();
      },
      invalidHandler: function( evnt, validator ) {
        console.log( '* JQV invalidHandler:', that.idForm, evnt, validator );
        if( validator.numberOfInvalids() ) {
          failFields = new Object({});
          jQuery.each( validator.errorList, function( index, value ) {
            failFields[index] = value.element;
          });

          // var idForm = $( failFields[0] ).attr('form');
          that.reprocessFormErrors( failFields );
        }
      },
      errorClass: 'formError',
      ignore: '.noValidate',
      lang: that.langSession,
      rules: rules,
      messages: messages,
      submitHandler: function ( form, evnt ) {
        // Controlamos que el submit se realice desde un elemento de submit
        $form = $( form );
        $submitElement = false;

        if( that.submitElementName ) {
          // Se ha pulsado en alguno de los elementos de submit
          console.log( '* submitElementName: '+that.submitElementName );
          $submitElement = $( '[form="'+that.idForm+'"][name="'+that.submitElementName+'"]' );
          that.submitElementName = false;
        }
        else {
          if( that.formOpts.enterSubmit ) {
            $submitElement = $( '[form="'+that.idForm+'"][type="submit"]' );
            console.log( '* submitElement Enter: ', $submitElement );
            // Tiene que ser 1 elemento
            if( $submitElement.length !== 1 ) {
              $submitElement = false;
            }
          }
        }


        if( $submitElement ) {

          that.submitActionName = $submitElement.attr('name')
          that.submitActionValue = $submitElement.attr('value')

          if( $submitElement.attr('data-confirm-text') ) {
            // Se ha indicado que hay que solicitar confirmacion antes del envio.
            cogumelo.clientMsg.confirm(
              $submitElement.attr('data-confirm-text'),
              function( resp ) {
                if( resp ) {
                  that.sendValidatedForm( form );
                }
              },
              { 'title': __('Confirm') }
            );

            // if( confirm( $submitElement.attr('data-confirm-text') ) ) {
            //   that.sendValidatedForm( form );
            // }
          }
          else {
            that.sendValidatedForm( form );
          }
        }
        else {
          // Se ha lanzado sin pulsar en alguno de los elementos de submit
          console.log( '* Cogumelo Form: Not submit element');
        }

        return false; // required to block normal submit since you used ajax
      }
    });

    //
    // JQUERY VALIDATE HACK !!! (Start)
    //
    that.validateObj.findByName = function( name ) {
      console.log( '* JQV cgmlHACK findByName: ', that.idForm, name );
      var $form = $( this.currentForm );
      var $elem = $form.find( '[name="' + name + '"]' );
      if( $elem.length !== 1 ) {
        $elem = $( '[form="'+$form[0].id+'"][name="'+name+'"]' );
      }
      // console.log( '* JQV cgmlHACK findByName ret: ', $elem );
      return $elem;
    };
    that.validateObj.idOrName = function( element ) {
      console.log( '* JQV cgmlHACK idOrName: ', that.idForm, element );
      var resp = this.groups[ element.name ] || ( this.checkable( element ) ? element.name : element.id || element.name );
      // console.log( '* JQV cgmlHACK idOrName ret: ', resp );
      return resp;
    };
    //
    // JQUERY VALIDATE HACK !!! (End)
    //

    console.log( '* VALIDATE PREPARADO: ', that.validateObj );

    // Bind file fields and group actions...
    that.bindForm();

    that.mixEvents();

    that.createFilesTitleField();

    // Si hay idiomas, buscamos campos multi-idioma en el form y los procesamos
    that.createSwitchFormLang();

    if( that.formOpts.htmlEditor ) {
      that.activateHtmlEditor();
      that.activateHtmlEditorBig();
    }

    return that.validateObj;
  }; // function setValidateForm( that.idForm, rules, messages )


  that.sendValidatedForm = function sendValidatedForm( form ) {
    console.log( '* Executando sendValidatedForm...', that.idForm );

    serializeFormObj = $( form ).serializeFormToObject();
    formProbas = serializeFormObj;

    console.log( 'serializeFormObj', serializeFormObj );
    console.log( 'submitAction', that.submitActionName, that.submitActionValue );
    serializeFormObj[that.submitActionName]['value'] = that.submitActionValue;


    $( form ).find( '[type="submit"]' ).attr('disabled', 'disabled');
    $( form ).find( '.submitRun' ).show();

    $.ajax( {
      contentType: 'application/json', processData: false,
      data: JSON.stringify( serializeFormObj ),
      type: 'POST', url: $( form ).attr( 'data-form-action' ),
      dataType : 'json'
    } )
    .done( function ( response ) {
      console.log( '* Executando validate.submitHandler.done...', response );
      if( response.result === 'ok' ) {
        // alert( 'Form Submit OK' );
        console.log( '* Form Done: OK' );
        that.formDoneOk( form, response );
      }
      else {
        console.log( '* Form Done: ERROR',response );
        that.formDoneError( form, response );
      }
      $( form ).find( '[type="submit"]' ).removeAttr('disabled');
      $( form ).find( '.submitRun' ).hide();
    } ); // /.done

    that.submitActionName = false;
    that.submitActionValue = false;
  }; // that.sendValidatedForm


  that.formDoneOk = function formDoneOk( form, response ) {
    console.log( '* formDoneOk',that.idForm, response );

    var successActions = response.success;
    if( successActions.onSubmitOk ) {
      eval( successActions.onSubmitOk+'( that.idForm );' );
    }
    if( successActions.jsEval ) {
      eval( successActions.jsEval );
    }
    if( successActions.notify ) {
      cogumelo.clientMsg.notify(
        successActions.notify,
        { notifyType: 'success', size: 'normal', 'title': __('Success') }
      );
    }
    if( successActions.accept ) {
      cogumelo.clientMsg.alert( successActions.accept );
      // alert( successActions.accept );
    }
    if( successActions.redirect ) {
      // Usando replace no permite volver a la pagina del form
      window.location.replace( successActions.redirect );
    }
    if( successActions.reload ) {
      window.location.reload();
    }
    if( successActions.resetForm ) {
      $( form )[0].reset();
      console.log( 'IMPORTANTE: En resetForm falta borrar los campos FILE porque no lo hace el reset!!!' );
    }
    // alert( 'Form Submit OK' );
  }; // that.formDoneOk

  that.formDoneError = function formDoneError( form, response ) {
    console.log( '* formDoneError',that.idForm, response );

    var successActions = response.success;
    if ( successActions.onSubmitError ) {
      eval( successActions.onSubmitError+'( that.idForm );' );
    }

    if( response.result === 'errorSession' ) {
      // No se ha podido recuperar el form en el servidor porque ha caducado
      // console.log( 'formDoneError: errorSession' );
      that.showErrorsValidateForm( __('Form session expired. Reload'), 'formError' );

      // Ofrecemos la opcion de recargar para qe funcione
      cogumelo.clientMsg.confirm(
        __('Reload to get valid From?'),
        function( resp ) {
          if( resp ) {
            window.location.reload();
          }
        },
        { 'title': __('Confirm') }
      );
      // if( confirm( __('Reload to get valid From?') ) ) {
      //   window.location.reload();
      // }
    }

    for( var i in response.jvErrors ) {
      var errObj = response.jvErrors[i];
      // console.log( errObj );

      if( errObj.fieldName !== false ) {
        if( errObj.JVshowErrors[ errObj.fieldName ] === false ) {
          var $defMess = that.validateObj.defaultMessage( errObj.fieldName, errObj.ruleName );
          if( typeof $defMess !== 'string' ) {
            $defMess = $defMess( errObj.ruleParams );
          }
          errObj.JVshowErrors[ errObj.fieldName ] = $defMess;
        }
        console.log( '* showErrors ('+ errObj.fieldName +'): ', errObj.JVshowErrors );
        that.validateObj.showErrors( errObj.JVshowErrors );
      }
      else {
        console.log( '* showErrors: ', errObj.JVshowErrors );
        that.showErrorsValidateForm( errObj.JVshowErrors.msgText, errObj.JVshowErrors.msgClass );
      }
    } // for(var i in response.jvErrors)



    that.reprocessFormErrors();


    // if( response.formError !== '' ) that.validateObj.showErrors( {'submit': response.formError} );
    // console.log( 'formDoneError (FIN)' );
  }; // that.formDoneError


  that.mixEvents = function mixEvents() {
    console.log( '* mixEvents '+that.idForm );

    $formSubmitFields = $( '[form="'+that.idForm+'"][type="submit"]' );
    $formSubmitFields.on({
      'mouseenter' : that.setSubmitElement,
      'focusin'    : that.setSubmitElement,
      'mouseleave' : that.unsetSubmitElement,
      'focusout'   : that.unsetSubmitElement
    });


    // Lanzamos formKeepAlive cuando pasa keepAliveTime (minutos)
    if( that.formOpts.keepAliveTime === true && cogumelo.publicConf.session_lifetime > 0 ) {
      // 25% menos que el tiempo de sesion y en minutos
      that.formOpts.keepAliveTime = parseInt( cogumelo.publicConf.session_lifetime/80 );
    }
    if( that.formOpts.keepAliveTime > 0 ) {
      that.keepAliveTimer = setInterval( that.formKeepAlive, that.formOpts.keepAliveTime*60*1000 );
    }
  }; // that.mixEvents

  that.bindForm = function bindForm() {
    console.log( '* bindForm '+that.idForm );

    var $inputFileFields = $( 'input:file[form="'+that.idForm+'"]' );
    if( $inputFileFields.length ) {
      if( !window.File ) {
        // File - provides readonly information such as name, file size, mimetype

        cogumelo.clientMsg.alert( __('Your browser does not have HTML5 support for send files. Upgrade to recent versions...') );
        // alert( __('Your browser does not have HTML5 support for send files. Upgrade to recent versions...') );
      }
      $inputFileFields.on( 'change', that.inputFileFieldChange );
      $inputFileFields.each(
        function() {
          var fieldName = $( this ).attr( 'name' );
          that.createFileFieldDropZone( fieldName );

          if( $( this ).attr('multiple') ) {
            that.fileFieldGroupWidget( fieldName );
          }
        }
      );
    }

    $( '.addGroupElement[data-form_id="'+that.idForm+'"]' ).on( 'click', that.addGroupElement ).css( 'cursor', 'pointer' );
    $( '.removeGroupElement[data-form_id="'+that.idForm+'"]' ).on( 'click', that.removeGroupElement ).css( 'cursor', 'pointer' );
  }; // that.bindForm

  that.unbindForm = function unbindForm() {
    console.log( '* unbindForm '+that.idForm );
    $( 'input:file[form="'+that.idForm+'"]' ).off( 'change' );
    $( '.addGroupElement[data-form_id="'+that.idForm+'"]' ).off( 'click' );
    $( '.removeGroupElement[data-form_id="'+that.idForm+'"]' ).off( 'click' );
  }; // that.unbindForm


  that.setSubmitElement = function setSubmitElement( evnt ) {
    console.log( '* setSubmitElement: ', that.idForm, $( evnt.target ).attr('name') );
    $elem = $( evnt.target );
    // $( '#'+$elem.attr('form') ).attr('data-submit-element-name', $elem.attr('name') );
    that.submitElementName = $elem.attr('name');
  }; // that.setSubmitElement

  that.unsetSubmitElement = function unsetSubmitElement( evnt ) {
    console.log( '* unsetSubmitElement: ', that.idForm /*, evnt*/ );
    // $elem = $( evnt.target );
    // $( '#'+$elem.attr('form') ).removeAttr('data-submit-element-name');
    that.submitElementName = false;
  }; // that.unsetSubmitElement


  that.formKeepAlive = function formKeepAlive() {
    console.log( '* formKeepAlive '+that.idForm );

    // var cgIntFrmId = $( '#' + that.idForm ).attr( 'data-token_id' );

    var formData = new FormData();
    formData.append( 'execute', 'keepAlive' );
    formData.append( 'idForm', that.idForm );
    formData.append( 'cgIntFrmId', that.cgIntFrmId );

    console.log( '* formData', formData );

    $.ajax({
      url: '/cgml-form-command', type: 'POST',
      data: formData, cache: false, contentType: false, processData: false,
      success: function successHandler( $jsonData, $textStatus, $jqXHR ) {
        console.log( '* formKeepAlive $jsonData --- ', $jsonData );
        // var idForm = ($jsonData.moreInfo.idForm) ? $jsonData.moreInfo.idForm : false;
        if( $jsonData.result === 'ok' ) {
          console.log( '* formKeepAlive OK --- ',that.idForm );
        }
        else {
          console.log( '* formKeepAlive ERROR',$jsonData );
        }
      }
    });
  }; // that.formKeepAlive


  that.activateHtmlEditor = function activateHtmlEditor() {
    console.log( '* activateHtmlEditor: ', that.idForm );

    $( 'textarea.cgmMForm-htmlEditor[form="'+that.idForm+'"]' ).each(
      function( index ) {
        var textarea = this;
        var CKcontent = CKEDITOR.replace( textarea, {
          customConfig: '/cgml-form-htmleditor-config.js'
        } );
        CKcontent.on( 'change', function ( ev ) { $( textarea ).html(CKcontent.getData()); });
      }
    );
  }; // that.activateHtmlEditor

  that.activateHtmlEditorBig = function activateHtmlEditorBig() {
    console.log( '* activateHtmlEditorBig: ', that.idForm );

    $( 'textarea.cgmMForm-htmlEditorBig[form="'+that.idForm+'"]' ).each(
      function( index ) {
        // var textarea = this;
        $fieldWrap = $(this).closest('.cgmMForm-wrap');
        $ta = $fieldWrap.find('textarea');
        $fieldWrap.append(
          '<div class="fieldGrapesJSContent" style="border: 1px solid #CECECE;padding: 5px;height: 200px;overflow: auto;">'+
          $ta.text() + '</div>'
        );
        $ta.css({ height: 0, border: 0, padding: 0, margin: 0 });

        $fieldWrap.find('label').on( 'click',function() {
          console.log( 'Abrir', this );

          $el = $(this).closest('.cgmMForm-wrap').find('textarea');

          htmlEditContent = '<div id="editorGrapesJSContent">' + $el.text() + '</div>';

          $editorWrap = $('#editorGrapesJSWrapper');
          if( !$editorWrap.length ) {
            $('body').prepend('<div id="editorGrapesJSWrapper"></div>')
            $editorWrap = $('#editorGrapesJSWrapper');
          }

          close = '<p class="editorGrapesJSClose" style="text-align: center;" '+
            'data-form="'+$el.attr('form')+'" data-field="'+$el.attr('name')+'">Close</p>'

          $('#wrapper').hide();
          $editorWrap.html( close+"\n"+htmlEditContent );

          $('#editorGrapesJSWrapper .editorGrapesJSClose').on( 'click', function() {
            console.log( 'Cerrar', this );
            $ev = $(this);
            newContent = "<style>\n"+formGrapesJS.getCss()+"</style>\n"+formGrapesJS.getHtml();
            formGrapesJS = false;

            $('#editorGrapesJSWrapper').html('');
            $('#wrapper').show();
            console.log('[form="'+$ev.data('form')+'"][name="'+$ev.data('field')+'"]');
            $ta = $('[form="'+$ev.data('form')+'"][name="'+$ev.data('field')+'"]');
            console.log( $ta );
            $ta.text( newContent );
            $ta.closest('.cgmMForm-wrap').find('.fieldGrapesJSContent').html( newContent );
          });

          // cogumelo.clientMsg.window( htmlEditContent );

          // modalEditorGrapesJS = geozzy.generateModal( {
          //   classCss: 'modalEditorGrapesJS',
          //   // htmlBody: htmlEditContent,
          //   size: 'md',
          //   successCallback: function() {
          //     console.log( 'successCallback', this, $el );
          //     $el.text( formGrapesJS.getHtml()+"\n<style>\n"+formGrapesJS.getCss()+"</style>" );
          //   }
          // });
          // $('.modalEditorGrapesJS .modal-dialog').css( 'width', '80%' ).html(htmlEditContent);


          // /vendor/bower/grapesjs-blocks-flexbox-master/dist/grapesjs-blocks-flexbox.min.js
          formGrapesJS = grapesjs.init({
            fromElement: true,
            container : '#editorGrapesJSContent',

            // plugins: ['gjs-blocks-flexbox'],
            // pluginsOpts: {
            //   'gjs-blocks-flexbox': {
            //     // options
            //   }
            // }

            // panels: {
            //   defaults: [{
            //     id: 'commands',
            //   }],
            // },
          });



          // console.log( "formGrapesJS = grapesjs.init({ height: '"+(window.innerHeight*0.7)+"px', fromElement: true, container : '#editorGrapesJSContent' });");

        });
      }
    );
  }; // that.activateHtmlEditorBig


  that.switchFormLang = function switchFormLang( lang ) {
    console.log( '* switchFormLang: ',that.idForm,lang );

    that.langSwitchActive = lang;
    $( '[form="'+that.idForm+'"].js-tr-sw, [data-form_id="'+that.idForm+'"].js-tr-sw, '+
      ' .cgmMForm-fileFields-'+that.idForm+' input.js-tr-sw' )
      .parent().hide();
    $( '[form="'+that.idForm+'"].js-tr-sw.js-tr-'+lang+', [data-form_id="'+that.idForm+'"].js-tr-sw.js-tr-'+lang+', '+
      ' .cgmMForm-fileFields-'+that.idForm+' input.js-tr-sw.js-tr-'+lang )
      .parent().show(); //.removeAttr('display');
    $( 'ul[data-form_id="'+that.idForm+'"].langSwitch li' ).removeClass( 'langActive' );
    $( 'ul[data-form_id="'+that.idForm+'"].langSwitch li.langSwitch-'+lang ).addClass( 'langActive' );
  }; // that.switchFormLang

  that.createSwitchFormLang = function createSwitchFormLang() {
    console.log( '* createSwitchFormLang',that.idForm );

    var htmlLangSwitch = '';
    htmlLangSwitch += '<div class="langSwitch-wrap">';
    htmlLangSwitch += '<ul class="langSwitch" data-form_id="'+that.idForm+'">';
    $.each( that.langAvailableIds, function( index, lang ) {
      htmlLangSwitch += '<li class="langSwitch-'+lang+'" data-lang="'+lang+'">'+lang;
    });
    htmlLangSwitch += '</ul>';
    htmlLangSwitch += '<span class="langSwitchIcon"><i class="fa fa-globe fa-fw"></i></span>';
    htmlLangSwitch += '</div>';

    $langSwitch = $( htmlLangSwitch );
    $( '[form="'+that.idForm+'"].cgmMForm-field.js-tr-sw.js-tr-' + that.langDefault + ':not("input:file")' ).each(function(){
      var field = $( this );
      var fieldName = field.attr( 'name' );
      field.parent().before( $langSwitch.clone().addClass("langSwitch-" + fieldName) );
    });

    $( '.cgmMForm-fileFields-'+that.idForm+' .cgmMForm-field.js-tr-sw.js-tr-' + that.langDefault + ':not("input:file")' ).each(function(){
      var field = $( this );
      var fieldName = field.attr( 'name' );
      field.parent().before( $langSwitch.clone().addClass("langSwitch-" + fieldName) );
    });

    $langSwitchFile = $( htmlLangSwitch ).addClass('langSwitch-file');
    $( '[type=file][form="'+that.idForm+'"].cgmMForm-field.js-tr-sw.js-tr-' + that.langDefault ).each(function(){
      var field = $( this );
      var fieldName = field.attr( 'name' );
      field.parent().before( $langSwitchFile.clone().addClass("langSwitch-" + fieldName) );
    });

    $( '[type=file].cgmMForm-fileFields-'+that.idForm+' .cgmMForm-field.js-tr-sw.js-tr-' + that.langDefault ).each(function(){
      var field = $( this );
      var fieldName = field.attr( 'name' );
      field.parent().before( $langSwitchFile.clone().addClass("langSwitch-" + fieldName) );
    });


    that.switchFormLang( that.langDefault );

    $( 'ul[data-form_id="'+that.idForm+'"].langSwitch li' ).on( 'click', function() {
      var newLang = $( this ).data( 'lang' );
      if( newLang !== that.langSwitchActive ) {
        that.switchFormLang( newLang );
      }
    });
  }; // that.createSwitchFormLang


  that.createFilesTitleField = function createFilesTitleField() {
    console.log( '* createFilesTitleField',that.idForm );

    var $inputFileFields = $( 'input:file[form="'+that.idForm+'"]' ).not( '[multiple]' );
    $inputFileFields.after( function() {
      // console.log( 'createFilesTitleField after ', this );

      var fileField = this;
      var html = '<div class="cgmMForm-wrap cgmMForm-'+that.idForm+' cgmMForm-fileFields-'+that.idForm+
        ' cgmMForm-titleFileField cgmMForm-titleFileField_'+fileField.name+'" style="display:none">'+"\n";

      $.each( that.langAvailableIds, function( i, lang ) {
        var name = ( lang !== '' ) ? fileField.name+'_'+lang : fileField.name;
        var filefielddata = ( lang !== '' ) ? 'fm_title_'+lang : 'fm_title';
        var classLang = ( lang !== '' ) ? ' js-tr-sw js-tr-'+lang : '';
        var titleValue = ( $( fileField ).data( filefielddata ) ) ? $( fileField ).data( filefielddata ) : '';
        html += '<div class="cgmMForm-wrap cgmMForm-field-titleFileField_'+name+'">'+"\n"+
          '<label class="cgmMForm'+classLang+'">Alt-Title</label>'+"\n"+
          '<input name="titleFileField_'+name+'" value="'+titleValue+'" '+
          'data-ffid="'+that.idForm+'" data-ffname="'+fileField.name+'" data-ffdata="'+filefielddata+'" '+
          'class="noValidate cgmMForm-field cgmMForm-field-titleFileField'+classLang+'" type="text">'+"\n"+
          '</div>'+"\n";
        // console.log( 'createFilesTitleField each lang '+lang );
      });

      html += '</div>'+"\n";

      return html;
    });

    $( 'input.cgmMForm-field-titleFileField' ).on( 'change', function() {
      // console.log( 'titleFileField change en ', this );
      var $titleFileField = $( this );
      var $titleData = $titleFileField.data();
      var $fileField = $( 'input[form="'+$titleData.ffid+'"][name="'+$titleData.ffname+'"]' );
      $fileField.attr( 'data-'+$titleData.ffdata, $titleFileField.val() );
      $fileField.data( $titleData.ffdata, $titleFileField.val() );
      // Doble escritura para asegurar porque funcionan distinto
    });
  }; // that.createFilesTitleField

  that.hideFileTitleField = function hideFileTitleField( fieldName ) {
    console.log( '* hideFileTitleField ',that.idForm, fieldName );

    var $fileField = $( 'input[form="'+that.idForm+'"][name="'+fieldName+'"]' );
    // Clear data-fm_title
    $.each( that.langAvailableIds, function( i, lang ) {
      var filefielddata = ( lang !== '' ) ? 'fm_title_'+lang : 'fm_title';
      $fileField.attr( 'data-'+filefielddata, '' );
      $fileField.data( filefielddata, '' );
    });
    // Hide wrap
    var $wrap = $( '.cgmMForm-'+that.idForm+'.cgmMForm-titleFileField_'+fieldName );
    $wrap.hide();
    // Clear values
    $wrap.find( ' input' ).val('');
  }; // that.hideFileTitleField


  that.reprocessFormErrors = function reprocessFormErrors( failFields ) {
    console.log( '* reprocessFormErrors', that.idForm, failFields );

    var topErrScroll = 999999;
    var numErrors = 0;
    var formMarginTop = that.marginTop;

    if( typeof failFields === 'undefined' ) {
      failFields = $( '.formError[form="' + that.idForm + '"]' );
    }
    // console.log( 'reprocessFormErrors failFields', failFields );

    // $( '.formError[form="' + that.idForm + '"]' ).each( function() {
    jQuery.each( failFields, function( index, value ) {
      numErrors++;
      $field = $( value );
      $wrap = $( '.cgmMForm-wrap.cgmMForm-field-'+$field.attr('name') );
      if( $wrap.length > 0 ) {
        topElem = $wrap.offset().top;
        // console.log( 'reprocessFormErrors WRAP ', topElem, $field.attr('name') );
      }
      else {
        topElem = $field.offset().top;
        // console.log( 'reprocessFormErrors FIELD ', topElem, $field.attr('name') );
      }

      if( topElem && topErrScroll > topElem ) {
        topErrScroll = topElem;
      }
    });


    if( topErrScroll != 999999 ) {
      if( formMarginTop !== null && formMarginTop !== undefined ) {
        topErrScroll -= formMarginTop;
      }
      // console.log( 'JQV topErrScroll:', formMarginTop, topErrScroll );
      $( 'html, body' ).animate( { scrollTop: topErrScroll }, 500 );
    }

    that.notifyFormErrors( numErrors );
  }; // that.reprocessFormErrors

  that.notifyFormErrors = function notifyFormErrors( numErrors ) {
    console.log( '* There are errors in the form', numErrors );

    cogumelo.clientMsg.notify(
      __('There are errors in the form'), // + ' ('+numErrors+')',
      { notifyType: 'warning', size: 'normal', 'title': __('Warning') }
    );
  }; // that.notifyFormErrors

  that.showErrorsValidateForm = function showErrorsValidateForm( msgText, msgClass ) {
    console.log( '* showErrorsValidateForm: ', that.idForm, msgClass, msgText );

    // Solo se muestran los errores pero no se marcan los campos

    // Replantear!!!

    var msgLabel = '<label class="formError" form="'+that.idForm+'">'+msgText+'</label>';
    var $msgContainer = false;
    if( msgClass !== false ) {
      $msgContainer = $( '.JQVMC-'+msgClass );
    }
    if( $msgContainer !== false && $msgContainer.length > 0 ) {
      $msgContainer.append( msgLabel );
    }
    else {
      that.jqForm.append( msgLabel );
    }
  }; // that.showErrorsValidateForm



  /*
  ***  FICHEROS  ***
  */
  that.inputFileFieldChange = function inputFileFieldChange( evnt ) {
    console.log( '* inputFileFieldChange:', that.idForm, evnt );

    // $fileField = $( evnt.target );
    that.processFilesInputFileField( evnt.target.files, evnt.target.name );
  }; // that.inputFileFieldChange

  that.processFilesInputFileField = function processFilesInputFileField( formFileObjs, fieldName ) {
    console.log( '* processFilesInputFileField(): ', that.idForm, formFileObjs, fieldName );

    if( that.checkInputFileField( formFileObjs, fieldName ) ) {
      for( var i = 0, formFileObj; (formFileObj = formFileObjs[i]); i++ ) {
        that.uploadFile( formFileObj, fieldName );
      }
    }
  }; // that.processFilesInputFileField

  that.checkInputFileField = function checkInputFileField( formFileObjs, fieldName ) {
    console.log( '* checkInputFileField:', that.idForm, formFileObjs, fieldName );

    var $fileField = $( 'input[name="' + fieldName + '"][form="' + that.idForm + '"]' );
    $( '#' + $fileField.attr('id') + '-error' ).remove();

    $fileField.data( 'validateFiles', formFileObjs );
    var valRes = that.validateObj.element( 'input[name="' + fieldName + '"][form="' + that.idForm + '"]' );
    $fileField.data( 'validateFiles', false );

    return valRes;
  }; // that.checkInputFileField

  that.uploadFile = function uploadFile( formFileObj, fieldName ) {
    console.log( '* uploadFile: ', that.idForm, formFileObj, fieldName );

    var formData = new FormData();
    formData.append( 'idForm', that.idForm );
    formData.append( 'cgIntFrmId', that.cgIntFrmId );
    formData.append( 'fieldName', fieldName );

    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );
    var $fileFieldWrap = $fileField.closest( '.cgmMForm-wrap.cgmMForm-field-' + fieldName );

    var tnProfile = $fileField.attr('data-tnProfile');
    if( typeof tnProfile === 'undefined' ) {
      tnProfile = 'modFormTn';
    }
    if( tnProfile ) {
      formData.append( 'tnProfile', tnProfile );
    }

    formData.append( 'ajaxFileUpload', formFileObj );

    $( '.'+fieldName+'-info[data-form_id="'+that.idForm+'"]' ).show();

    $fileFieldWrap.find('.fileFieldDropZone .upload, .fileFieldDropZone .spinner').toggle();

    $.ajax({
      url: '/cgml-form-file-upload', type: 'POST',
      // Form data
      data: formData,
      //Options to tell jQuery not to process data or worry about content-type.
      cache: false, contentType: false, processData: false,
      // Custom XMLHttpRequest
      xhr: function() {
        var myXhr = $.ajaxSettings.xhr();
        if( myXhr.upload ) { // Check if upload property exists for handling the progress of the upload
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
      /*
      beforeSend: function beforeSendHandler( $jqXHR, $settings ) {
        $( '#status' ).html( 'Upload Failed (' + $textStatus + ')' );
      },
      */
      success: function successHandler( $jsonData, $textStatus, $jqXHR ) {
        console.log( 'Executando fileSendOk...', $jsonData );

        var fieldName = $jsonData.moreInfo.fieldName;
        $( '.'+fieldName+'-info[data-form_id="'+that.idForm+'"] .wrap .progressBar' ).hide();

        if( $jsonData.result === 'ok' ) {

          var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );
          var $fileFieldWrap = $fileField.closest( '.cgmMForm-wrap.cgmMForm-field-' + fieldName );
          $fileFieldWrap.find('.fileFieldDropZone .upload, .fileFieldDropZone .spinner').toggle();

          that.fileSendOk( fieldName, formFileObj, $jsonData.moreInfo );

          var successActions = $jsonData.success;
          if( successActions.onFileUpload ) {
            eval( successActions.onFileUpload+'( that.idForm, fieldName );' );
          }
        }
        else {
          // console.log( 'uploadFile ERROR' );
          $( '.'+fieldName+'-info[data-form_id="'+that.idForm+'"] .wrap .status' ).html( __('Error loading file') );

          for(var i in $jsonData.jvErrors) {
            var errObj = $jsonData.jvErrors[i];
            // console.log( 'uploadFile ERROR', errObj );

            if( errObj.fieldName !== false ) {
              if( errObj.JVshowErrors[ errObj.fieldName ] === false ) {
                var $defMess = that.validateObj.defaultMessage( errObj.fieldName, errObj.ruleName );
                if( typeof $defMess !== 'string' ) {
                  $defMess = $defMess( errObj.ruleParams );
                }
                errObj.JVshowErrors[ errObj.fieldName ] = $defMess;
              }
              // console.log( errObj.JVshowErrors );
              that.validateObj.showErrors( errObj.JVshowErrors );
            }
            else {
              // console.log( errObj.JVshowErrors );
              that.showErrorsValidateForm( errObj.JVshowErrors.msgText, errObj.JVshowErrors.msgClass );
            }
          }
          // if( $jsonData.formError !== '' ) that.validateObj.showErrors( {'submit': $jsonData.formError} );
        }
      },
      error: function errorHandler( $jqXHR, $textStatus, $errorThrown ) { // textStatus: timeout, error, abort, or parsererror
        console.log( 'uploadFile errorHandler', $jqXHR, $textStatus, $errorThrown );
        $( '.'+fieldName+'-info[data-form_id="'+that.idForm+'"] .status' ).html( 'Upload Failed (' + $textStatus + ')' );
      }
    });
  }; // that.uploadFile

  that.deleteFormFileEvent = function deleteFormFileEvent( evnt ) {
    console.log( '* deleteFormFileEvent: ', evnt );
    var $fileField = $( evnt.target );
    var fieldName = $fileField.attr( 'data-fieldname' );
    var fileId = $fileField.attr( 'data-file_id' ) || false;
    var fileTempId = $fileField.attr( 'data-file_temp_id' ) || false;

    that.deleteFormFile( fieldName, fileId, fileTempId );
  }; // that.deleteFormFileEvent

  that.deleteFormFile = function deleteFormFile( fieldName, fileId, fileTempId ) {
    console.log( '* deleteFormFile: ', that.idForm, fieldName, fileId, fileTempId );

    var formData = new FormData();
    formData.append( 'execute', 'delete' );
    formData.append( 'idForm', that.idForm );
    formData.append( 'cgIntFrmId', that.cgIntFrmId );
    formData.append( 'fieldName', fieldName );
    formData.append( 'fileId', fileId );
    if( fileTempId !== false ) {
      formData.append( 'fileTempId', fileTempId );
    }

    $.ajax( {
      url: '/cgml-form-file-upload', type: 'POST',
      data: formData,
      //Options to tell jQuery not to process data or worry about content-type.
      cache: false, contentType: false, processData: false
    } )
    .done( function ( response ) {
      // console.log( 'Executando deleteFormFile.done...' );
      // console.log( response );
      if( response.result === 'ok' ) {

        that.fileDeleteOk( fieldName, fileId, fileTempId );
        // fileFieldToInput( that.idForm, fieldName );

        var successActions = response.success;
        if( successActions.onFileDelete ) {
          eval( successActions.onFileDelete+'( that.idForm, fieldName );' );
        }
      }
      else {
        console.log( 'deleteFormFile.done...ERROR', response );
        for(var i in response.jvErrors) {
          var errObj = response.jvErrors[i];
          // console.log( errObj );

          if( errObj.fieldName !== false ) {

            // TODO !!!

          }
          else {
            // console.log( errObj.JVshowErrors );
            that.showErrorsValidateForm( errObj.JVshowErrors.msgText, errObj.JVshowErrors.msgClass );
          }

        } // for
      }
    } );
  }; // that.deleteFormFile

  that.fileFieldGroupAddElem = function fileFieldGroupAddElem( fieldName, fileInfo ) {
    console.log( 'fileFieldGroupAddElem: ', that.idForm, fieldName, fileInfo );

    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );
    var groupId = $fileField.attr('data-fm_group_id');
    var groupFiles = [];

    // console.log( 'groupId antes: ',groupId );
    // console.log( 'groupFiles antes: ',groupFiles );

    if( groupId ) {
      groupFiles = that.fileGroup[ groupId ];
    }
    else {
      groupId = that.idForm+'_'+fieldName;
      that.fileGroup[ groupId ] = groupFiles;
      $fileField.attr( 'data-fm_group_id', groupId );
    }

    // console.log( 'groupId: ',groupId );
    // console.log( 'groupFiles: ',groupFiles );

    groupFiles.push( fileInfo );

    // console.log( 'groupFiles despois: ',groupFiles );
    that.fileGroup[ groupId ] = groupFiles;

    that.fileFieldGroupWidget( fieldName );
  }; // that.fileFieldGroupAddElem

  that.fileFieldGroupRemoveElem = function fileFieldGroupRemoveElem( fieldName, fileId, fileTempId ) {
    console.log( '* fileFieldGroupRemoveElem: ', that.idForm, fieldName, fileId, fileTempId );

    var $fileField = $( 'input[name="'+fieldName+'"][form="'+that.idForm+'"]' );
    var groupId = $fileField.attr('data-fm_group_id');
    var groupFiles = that.fileGroup[ groupId ];

    var newGroupFiles = jQuery.grep( groupFiles, function( elem ) {
      // console.log('grep: ',elem);
      return (
        ( fileId !== false && elem.id != fileId ) ||
        ( fileTempId !== false && ( !elem.hasOwnProperty('tempId') || elem.tempId != fileTempId ) )
      );
    });
    that.fileGroup[ groupId ] = newGroupFiles;

    that.fileFieldGroupWidget( fieldName );
  }; // that.fileFieldGroupRemoveElem

  that.fileSendOk = function fileSendOk( fieldName, formFileObj, moreInfo ) {
    console.log( '* fileSendOk: ', that.idForm, fieldName, formFileObj, moreInfo );

    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );

    var fileInfo = {
      'id': false,
      'formFileObj': formFileObj,
      'tempId': moreInfo.tempId,
      'name': moreInfo.fileName,
      'type': moreInfo.fileType,
      'size': moreInfo.fileSize,
    };

    var tnProfile = $fileField.attr('data-tnProfile');
    if( tnProfile ) {
      fileInfo.tnProfile = tnProfile;
    }

    fileInfo.fileSrcTn = moreInfo.hasOwnProperty('fileSrcTn') ? moreInfo.fileSrcTn : false;

    if( $fileField.attr('multiple') ) {
      that.fileFieldGroupAddElem( fieldName, fileInfo );
    }
    else {
      that.fileFieldToOk( fieldName, fileInfo );
    }
  }; // that.fileSendOk

  that.fileDeleteOk = function fileDeleteOk( fieldName, fileId, fileTempId ) {
    console.log( '* fileDeleteOk: ', that.idForm, fieldName, fileId, fileTempId );
    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );

    if( $fileField.attr('multiple') ) {
      that.fileFieldGroupRemoveElem( fieldName, fileId, fileTempId );
    }
    else {
      that.fileFieldToInput( fieldName );
    }
  }; // that.fileDeleteOk

  that.fileFieldGroupWidget = function fileFieldGroupWidget( fieldName ) {
    console.log( '* fileFieldGroupWidget: ', that.idForm, fieldName );

    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );
    var groupId = $fileField.attr('data-fm_group_id');
    var groupFiles = [];

    // console.log( 'groupId antes: ',groupId );
    // console.log( 'groupFiles antes: ',groupFiles );

    if( groupId ) {
      groupFiles = that.fileGroup[ groupId ];
    }
    else {
      groupId = that.idForm+'_'+fieldName;
      that.fileGroup[ groupId ] = groupFiles;
      $fileField.attr( 'data-fm_group_id', groupId );
    }

    var $fileFieldWrap = $fileField.closest( '.cgmMForm-wrap.cgmMForm-field-' + fieldName );
    var $fileFieldDropZone = $fileFieldWrap.find( '.fileFieldDropZone' );
    var $filesWrap = $fileFieldWrap.find('.cgmMForm-fileBoxWrap');

    if( $filesWrap.length == 1 ) {
      $filesWrap = $( $filesWrap[0] );
      $filesWrap.find('*').remove();
    }
    else {
      $filesWrap = $( '<div>' ).addClass( 'cgmMForm-fileBoxWrap clearfix' );
      $fileFieldDropZone.after( $filesWrap );
    }

    $.each( groupFiles, function(){
      // console.log('Añadimos esto a fileBoxWrap;', this, $filesWrap);
      $filesWrap.append( that.fileBox( fieldName, this, that.deleteFormFileEvent )
       .css( {'float': 'left', 'width': '23%', 'margin': '1%' } ) );
    } );
  }; // that.fileFieldGroupWidget

  that.fileFieldToOk = function fileFieldToOk( fieldName, fileInfo ) {
    console.log( '* fileFieldToOk: ', that.idForm, fieldName, fileInfo );
    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );
    var $fileFieldWrap = $fileField.closest( '.cgmMForm-wrap.cgmMForm-field-' + fieldName );

    var $filesWrap = $( '<div>' ).addClass( 'cgmMForm-fileBoxWrap clearfix' );

    $fileField.attr( 'readonly', 'readonly' ).prop( 'disabled', true ).hide();

    //$( '#'+fieldName+'-error[data-form_id="'+that.idForm+'"]' ).hide();
    $( '#' + $fileField.attr('id') + '-error' ).remove();

    // Show Title file field
    // $( '.cgmMForm-' + that.idForm+'.cgmMForm-titleFileField_'+fieldName ).show();
    $( '.cgmMForm-' + that.idForm+'.cgmMForm-titleFileField_'+fieldName ).removeAttr('display');

    if( !fileInfo.hasOwnProperty('tnProfile') && $fileField.attr('data-tnProfile') ) {
      fileInfo.tnProfile = $fileField.attr('data-tnProfile');
    }

    $filesWrap.append( that.fileBox( fieldName, fileInfo, that.deleteFormFileEvent ) );
    $fileFieldWrap.append( $filesWrap );

    that.removeFileFieldDropZone( fieldName );
  }; // that.fileFieldToOk

  that.fileBox = function fileBox( fieldName, fileInfo, deleteFunc ) {
    console.log( '* fileBox: ', that.idForm, fieldName, fileInfo );

    var $fileBoxElem = $( '<div>' ).addClass( 'cgmMForm-fileBoxElem fileFieldInfo fileUploadOK formFileDelete' )
      .attr( { 'data-form_id': that.idForm, 'data-fieldname': fieldName, 'data-file_id': fileInfo.id } );
    if( fileInfo.hasOwnProperty('tempId') ) {
      $fileBoxElem.attr( 'data-file_tempId', fileInfo.tempId );
    }



    var $fileBoxMenu = $( '<div>' ).addClass( 'fileFieldMenu' )
      .attr( { 'data-form_id': that.idForm, 'data-fieldname': fieldName, 'data-file_id': fileInfo.id } );

    // Element to send delete order
    var $deleteButton = $( '<i>' ).addClass( 'formFileDelete fa fa-times-circle' )
      .attr( { 'data-fieldname': fieldName, 'data-form_id': that.idForm, 'title': __('Discard')+' '+ fileInfo.name } )
      .on( 'click', deleteFunc );
    if( fileInfo.id !== false ) {
      $deleteButton.attr( 'data-file_id', fileInfo.id );
    }
    else {
      $deleteButton.attr( 'data-file_temp_id', fileInfo.tempId );
    }
    $fileBoxMenu.append(
      $('<div>').addClass('icons delete').append(
        $deleteButton
      )
    );

    // Element to download
    var $downloadButton = null;
    if( fileInfo.id !== false ) {
      $downloadButton = $( '<a class="formFileDownload" href="/cgmlformfilewd/'+fileInfo.id+'-a'+fileInfo.aKey+
        '/'+fileInfo.name+'" target="_blank"><i class="fa fa-arrow-circle-down" title="'+__('Download')+' '+fileInfo.name+'"></i></a>' );
    }
    else {
      $downloadButton = $( '<i class="formFileDownload disabled fa fa-arrow-circle-down"></i>' );
    }
    $fileBoxMenu.append(
      $('<div>').addClass('icons download').append(
        $downloadButton
      )
    );


    var fileTextInfo = fileInfo.name;

    $fileBoxMenu.append( '<div class="fileTextInfo" title="'+fileInfo.name+'">'+fileTextInfo+'</div>' );



    var tnSrc = cogumelo.publicConf.media+'/module/form/img/file.png';

    if( fileInfo.fileSrcTn ) {
      tnSrc = fileInfo.fileSrcTn;
    }
    if( fileInfo.id !== false && fileInfo.type && fileInfo.type.indexOf( 'image' ) === 0 ) {
      var tnProfile = 'modFormTn';
      if( fileInfo.hasOwnProperty('tnProfile') ) {
        tnProfile = fileInfo.tnProfile;
      }
      else {
        var inputTnProfile = $( 'input[name="'+fieldName+'"][form="'+that.idForm+'"]' ).attr('data-tnProfile');
        if( typeof inputTnProfile !== 'undefined' ) {
          tnProfile = inputTnProfile;
        }
      }

      if( tnProfile ) {
        // tnSrc = '/cgmlImg/'+fileInfo.id+'-a'+fileInfo.aKey+'/'+tnProfile+'/'+fileInfo.name;
        tnSrc = '/cgmlImg/'+fileInfo.id+'-a'+fileInfo.aKey+'/'+tnProfile+'/'; // quito name porque puede no coincidir la extension
      }
    }

    var tnClass = 'tn-';
    tnClass += (fileInfo.id) ? fileInfo.id : 'N';
    tnClass += '-';
    tnClass += fileInfo.hasOwnProperty('tempId') ? fileInfo.tempId : 'N';
    tnClass += '-id';

    $fileBoxElem.append( '<img class="tnImage '+tnClass+'" data-tnClass="'+tnClass+'" '+
      'src="'+tnSrc+'" alt="'+fileInfo.name+'" title="'+fileInfo.name+'"></img>' );

    $fileBoxElem.append( $fileBoxMenu );

    return $fileBoxElem;
  }; // that.fileBox

  that.fileFieldToInput = function fileFieldToInput( fieldName ) {
    console.log( '* fileFieldToInput: ', that.idForm, fieldName );

    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );
    var $fileFieldWrap = $fileField.closest( '.cgmMForm-wrap.cgmMForm-field-' + fieldName );

    // console.log( $fileField );

    // $fileFieldWrap.find( '.fileUploadOK' ).remove();
    $fileFieldWrap.find( '.cgmMForm-fileBoxWrap' ).remove();

    $fileField.removeAttr( 'readonly' );
    $fileField.prop( 'disabled', false ); //$fileField.removeProp( 'disabled' );
    $fileField.val( null );
    $fileField.show();

    // Hide and clear Title file field/value
    that.hideFileTitleField( fieldName );

    that.createFileFieldDropZone( fieldName );
  }; // that.fileFieldToInput

  that.createFileFieldDropZone = function createFileFieldDropZone( fieldName ) {
    console.log( '* createFileFieldDropZone: ', that.idForm, fieldName );

    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );
    var $fileFieldWrap = $fileField.closest( '.cgmMForm-wrap.cgmMForm-field-' + fieldName );
    var $fileDefLabel = $fileFieldWrap.find( 'label' );

    $buttonText = ( $fileDefLabel.length > 0 ) ? $fileDefLabel.html() : 'Upload file';

    // console.log( 'Preparando DropZone #fileFieldDropZone_' + that.idForm + '_' + fieldName );
    var $fileFieldDropZone = $( '<div>' ).addClass( 'fileFieldDropZone fileFieldDropZoneWait' )
      .attr( {
        'id': 'fileFieldDropZone_' + that.idForm + '_' + fieldName,
        // 'for': $fileField.attr( 'id' ),
        'data-fieldname': fieldName, 'data-form_id': that.idForm,
        'style': 'text-align:center; cursor:pointer;'
      });

    $fileFieldDropZone.append(
      '<div class="internal">'+
      '<i class="upload fa fa-cloud-upload" style="font-size:100px; color:#7fb1c7;"></i>'+
      '<i class="spinner fa fa-spinner fa-pulse fa-fw" style="font-size:80px; color:#7fb1c7; margin-bottom: 20px; display:none"></i>'+
      '<br><span class="cgmMForm-button-js">' + $buttonText + '</span>'+
      '</div>'
    );

    $fileFieldWrap.append( $fileFieldDropZone );

    // Pasamos el click en fileFieldDropZone al input file
    $fileFieldDropZone.on( 'click', function() {
      $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' ).click();
    });

    $fileField.hide();
    // $fileDefLabel.hide();

    // Setup the fileFieldDropZone listeners.
    //$fileFieldDropZoneElem = $( '.fileFieldDropZone' );
    // console.log( 'fileFieldDropZoneElem: ', $fileFieldWrap.find( '.fileFieldDropZone' ) );

    var fileFieldDropZoneElem = document.getElementById( 'fileFieldDropZone_' + that.idForm + '_' + fieldName );
    fileFieldDropZoneElem.addEventListener( 'drop', that.fileFieldDropZoneDrop, false);
    fileFieldDropZoneElem.addEventListener( 'dragover', that.fileFieldDropZoneDragOver, false);
  }; // that.createFileFieldDropZone

  that.removeFileFieldDropZone = function removeFileFieldDropZone( fieldName ) {
    console.log( '* removeFileFieldDropZone: ', that.idForm, fieldName );

    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );
    var $fileFieldWrap = $fileField.closest( '.cgmMForm-wrap.cgmMForm-field-' + fieldName );

    $fileFieldWrap.find( '.fileFieldDropZone' ).remove();
  }; // that.removeFileFieldDropZone

  that.fileFieldDropZoneDrop = function fileFieldDropZoneDrop( evnt ) {
    console.log( '* fileFieldDropZoneDrop() ', that.idForm, evnt );

    evnt.stopPropagation();
    evnt.preventDefault();

    var files = evnt.dataTransfer.files; // FileList object.
    // console.log( 'fileFieldDropZoneDrop files: ', files );

    var $fileFieldDropZone = $( evnt.target ).closest( '.fileFieldDropZone' );
    var fieldName = $fileFieldDropZone.data( 'fieldname' );
    var $fileField = $( 'input[name="' + fieldName + '"][form="'+that.idForm+'"]' );

    // $fileField.data( 'dropfiles', false );

    if( files.length === 1 || $fileField.attr('multiple') ) {
      // $fileField.data( 'dropfiles', files );
      that.processFilesInputFileField( files, fieldName );
    }
  }; // that.fileFieldDropZoneDrop

  that.fileFieldDropZoneDragOver = function fileFieldDropZoneDragOver( evnt ) {
    console.log( '* fileFieldDropZoneDragOver event: ', that.idForm, evnt );

    evnt.stopPropagation();
    evnt.preventDefault();
    // evnt.originalEvent.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
    evnt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
  }; // that.fileFieldDropZoneDragOver



  /*
  ***  Agrupaciones de campos  ***
  */
  that.addGroupElement = function addGroupElement( evnt ) {
    console.log( 'addGroupElement:', that.idForm, evnt );

    var groupName = $( evnt.target ).attr('groupName');

    var formData = new FormData();
    formData.append( 'execute', 'getGroupElement' );
    formData.append( 'idForm', that.idForm );
    formData.append( 'cgIntFrmId', that.cgIntFrmId );
    formData.append( 'groupName', groupName );

    console.log( '* formData', formData );

    // Desactivamos los bins del form durante el proceso
    that.unbindForm();

    $.ajax({
      url: '/cgml-form-group-element', type: 'POST',
      // Form data
      data: formData,
      //Options to tell jQuery not to process data or worry about content-type.
      cache: false, contentType: false, processData: false,
      // Custom XMLHttpRequest
      success: function successHandler( $jsonData, $textStatus, $jqXHR ) {
        // console.log( 'getGroupElement success:' );
        // console.log( $jsonData );

        var groupName = $jsonData.moreInfo.groupName;

        $( '#' + that.idForm + ' .JQVMC-group-' + groupName + ' .formError' ).remove();

        if( $jsonData.result === 'ok' ) {
          // console.log( 'getGroupElement OK' );
          // console.log( 'idForm: ' + that.idForm + ' groupName: ' + groupName );

          $( $jsonData.moreInfo.htmlGroupElement ).insertBefore(
            '#' + that.idForm + ' .cgmMForm-group-' + groupName + ' .addGroupElement'
          );

          $.each( $jsonData.moreInfo.validationRules, function( fieldName, fieldRules ) {
            // console.log( 'fieldName: ' + fieldName + ' fieldRules: ', fieldRules );
            // console.log( 'ELEM: #' + that.idForm + ' .cgmMForm-field.cgmMForm-field-' + fieldName );
            $( '#' + that.idForm + ' .cgmMForm-field.cgmMForm-field-' + fieldName ).rules( 'add', fieldRules );
          });

          // console.log( 'getGroupElement OK Fin' );
        }
        else {
          // console.log( 'getGroupElement ERROR' );
          var errObj = $jsonData.jvErrors[0];
          // console.log( errObj.JVshowErrors );
          that.showErrorsValidateForm( errObj.JVshowErrors[0], 'group-' + groupName );
        }

        // Activamos los bins del form despues del proceso
        that.bindForm();

        // console.log( 'getGroupElement success: Fin' );
      },
      error: function errorHandler( $jqXHR, $textStatus, $errorThrown ) { // textStatus: timeout, error, abort, or parsererror
        // console.log( 'uploadFile errorHandler', $jqXHR, $textStatus, $errorThrown );
        $( '#status' ).html( 'ERROR: (' + $textStatus + ')' );

        // Activamos los bins del form despues del proceso
        that.bindForm();
      }
    });
  }; // that.addGroupElement

  that.removeGroupElement = function removeGroupElement( evnt ) {
    console.log( '* removeGroupElement:', that.idForm, evnt );

    var groupName = $( evnt.target ).attr('groupName');
    var groupIdElem = $( evnt.target ).attr('groupIdElem');

    var formData = new FormData();
    formData.append( 'execute', 'removeGroupElement' );
    formData.append( 'idForm', that.idForm );
    formData.append( 'cgIntFrmId', that.cgIntFrmId );
    formData.append( 'groupName', groupName );
    formData.append( 'groupIdElem', groupIdElem );

    console.log( '* formData', formData );

    // Desactivamos los bins del form durante el proceso
    that.unbindForm();

    $.ajax({
      url: '/cgml-form-group-element', type: 'POST',
      // Form data
      data: formData,
      //Options to tell jQuery not to process data or worry about content-type.
      cache: false, contentType: false, processData: false,
      // Custom XMLHttpRequest
      success: function successHandler( $jsonData, $textStatus, $jqXHR ) {
        // console.log( 'removeGroupElement success:' );
        // console.log( $jsonData );

        var groupName = $jsonData.moreInfo.groupName;

        $( '#' + that.idForm + ' .JQVMC-group-' + groupName + ' .formError' ).remove();

        if( $jsonData.result === 'ok' ) {
          // console.log( 'removeGroupElement OK' );
          // console.log( that.idForm, groupName, $jsonData.moreInfo.groupIdElem );
          // console.log( '#' + that.idForm + ' .cgmMForm-groupElem_C_' + $jsonData.moreInfo.groupIdElem );
          $( '#' + that.idForm + ' .cgmMForm-groupElem_C_' + $jsonData.moreInfo.groupIdElem ).remove();
        }
        else {
          // console.log( 'removeGroupElement ERROR' );
          var errObj = $jsonData.jvErrors[0];
          // console.log( errObj.JVshowErrors );
          that.showErrorsValidateForm( errObj.JVshowErrors[0], 'group-' + groupName );
        }

        // Activamos los bins del form despues del proceso
        that.bindForm();

        // console.log( 'removeGroupElement success: Fin' );
      },
      error: function errorHandler( $jqXHR, $textStatus, $errorThrown ) { // textStatus: timeout, error, abort, or parsererror
        // console.log( 'uploadFile errorHandler', $jqXHR, $textStatus, $errorThrown );
        $( '#status' ).html( 'ERROR: (' + $textStatus + ')' );

        // Activamos los bins del form despues del proceso
        that.bindForm();
      }
    });
  }; // that.removeGroupElement

}; // cogumelo.formControllerClass



// Cargamos el fichero del idioma de sesion para jQuery Validator
if( cogumelo.publicConf.C_LANG !== 'en' ) {

  switch( cogumelo.publicConf.C_LANG ) {
    case "pt":
        basket.require( { url: '/vendor/bower/jquery-validation/src/localization/messages_'+cogumelo.publicConf.C_LANG+'_PT.js' } );
        break;
    case "br":
        basket.require( { url: '/vendor/bower/jquery-validation/src/localization/messages_'+cogumelo.publicConf.C_LANG+'_BR.js' } );
        break;
    default:
        basket.require( { url: '/vendor/bower/jquery-validation/src/localization/messages_'+cogumelo.publicConf.C_LANG+'.js' } );
  }

  //basket.require( { url: '/vendor/bower/jquery-validation/src/localization/messages_'+cogumelo.publicConf.C_LANG+'.js' } );
}





var formGrapesJS = false;
var formProbas = false;
