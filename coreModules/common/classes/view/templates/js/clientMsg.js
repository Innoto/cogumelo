var geozzy = geozzy || {};

cogumelo.clientMsgClass = function( options ) {

  var that = this;

  var msgBaseOpts = new Object({
    // DO NOT change this values. Lobibox depended on it
    // bodyClass
    // modalClasses

    alertType: 'info',  // Available types (info|success|warning|error)
    promptType: 'text', // Available types (text|number|color)

    title: __('Information'),

    horizontalOffset: 5,                //If the messagebox is larger (in width) than window's width. The messagebox's width is reduced to window width - 2 * horizontalOffset
    verticalOffset: 5,                  //If the messagebox is larger (in height) than window's height. The messagebox's height is reduced to window height - 2 * verticalOffset
    width: 600,
    height: 'auto',                     // Height is automatically calculated by width
    closeButton: true,                  // Show close button or not
    draggable: false,                   // Make messagebox draggable
    buttonsAlign: 'center',             // Position where buttons should be aligned
    closeOnEsc: true,                   // Close messagebox on Esc press
    delayToRemove: 200,                 // Time after which lobibox will be removed after remove call. (This option is for hide animation to finish)
  });
  that.msgDefOptions = $.extend( true, {}, msgBaseOpts, options );

  var notifyBaseOpts = new Object({
    notifyType: 'info', // Available types (info|success|warning|error)

    // title: false,               // Title of notification. Do not include it for default title or set custom string. Set this false to disable title
    size: 'mini',               // normal, mini, large
    delay: 15000,               // Hide notification after this time (in miliseconds)
    closeOnClick: false,        // Close notifications by clicking on them
    position: 'top right',      // Place to show notification. Available options: "top left", "top right", "bottom left", "bottom right"

    icon: true,                 // Icon of notification. Leave as is for default icon or set custom string
    msg: '',                    // Message of notification
    img: null,                  // Image source string
    closable: true,             // Make notifications closable
    delayIndicator: true,       // Show timer indicator
    width: 400,                 // Width of notification box
    sound: false,               // Sound of notification. Set this false to disable sound. Leave as is for default sound or set custom soud path
    rounded: false,             // Whether to make notification corners rounded
    messageHeight: 60,          // Notification message maximum height. This is not for notification itself, this is for .lobibox-notify-msg
    pauseDelayOnHover: true,    // When you mouse over on notification, delay will be paused, only if continueDelayOnInactiveTab is false.
  });
  that.notifyDefOptions = $.extend( true, {}, notifyBaseOpts, options );


  // Msg Notify
  that.notify = function( msg, fnOptions ) {
    var result = false;

    var notifyType = 'info';
    var msgOptions = $.extend( true, {}, that.notifyDefOptions, fnOptions );

    if( typeof msgOptions.notifyType === 'string' ) {
      notifyType = msgOptions.notifyType;
    }

    msgOptions.msg = msg;
    result = new Object({
      status: true,
      Lobibox: Lobibox.notify( notifyType, msgOptions )
    });

    return result;
  };


  // Msg Alert
  that.alert = function( msg, fnOptions ) {
    var result = false;

    var alertType = 'info';
    var msgOptions = $.extend( true, {}, that.msgDefOptions, fnOptions );

    if( typeof msgOptions.alertType === 'string' ) {
      alertType = msgOptions.alertType;
    }

    msgOptions.msg = msg;

    result = new Object({
      status: true,
      Lobibox: Lobibox.alert( alertType, msgOptions )
    });

    return result;
  };


  // Msg Confirm
  that.confirm = function( msg, fnResult, fnOptions ) {
    var result = false;

    var msgOptions = $.extend( true, {}, that.msgDefOptions, fnOptions );

    msgOptions.msg = msg;

    // cogumelo.clientMsg.confirm('OKI', function(val){ console.log('fnResult:', val); } );

    if( typeof fnResult === 'function' ) {
      msgOptions.callback = function( $this, button, ev ) {
        console.log( 'callback:', $this, button, ev );
        var val = ( button === 'yes' );
        console.log( 'VALOR:', val );
        fnResult( val );
      };
    }

    result = new Object({
      status: true,
      Lobibox: Lobibox.confirm( msgOptions )
    });

    return result;
  };


  // Msg Prompt
  that.prompt = function( msg, fnResult, fnOptions ) {
    var result = false;

    var promptType = 'text';
    var msgOptions = $.extend( true, {}, that.msgDefOptions, fnOptions );

    if( typeof msgOptions.promptType === 'string' ) {
      promptType = msgOptions.promptType;
      // Any HTML5 input type can be used in prompt window.
      // text, color, date, datetime, email, number, range, ...
    }


    msgOptions.msg = msg;

    // cogumelo.clientMsg.prompt( 'OKI', function(val){ console.log('fnResult:', val); } );

    if( typeof fnResult === 'function' ) {
      msgOptions.callback = function( $this, button, ev ) {
        console.log( 'callback:', $this, button, ev );
        var val = null;
        if( button === 'ok' ) {
          val = $this.getValue();
        }
        console.log( 'VALOR:', val );
        fnResult( val );
      };
    }

    result = new Object({
      status: true,
      Lobibox: Lobibox.prompt( promptType, msgOptions )
    });

    return result;
  };


  that.window = function( msg, fnOptions ) {

    // that.msgDefOptions = $.extend( true, {}, that.msgDefOptions, options );
  };

};


// Default Geozzy clientMsgClass instance
cogumelo.clientMsg = new cogumelo.clientMsgClass();
