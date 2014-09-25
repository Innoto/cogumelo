
$.validator.addMethod(
  "timeMin",
  function( value, element, param) {
      
    var valueTimeArray = /^(\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    var paramTimeArray = /^(\d{1,2}):(\d{2}):(\d{2})$/.exec(param);
    var valueTimeSeconds = "";
    var paramTimeSeconds = "";
    if(valueTimeArray){
      valueTimeSeconds = parseInt(valueTimeArray[0])*3600 + parseInt(valueTimeArray[1])*60 + parseInt(valueTimeArray[2]);    
    }
    if(paramTimeArray){
      paramTimeSeconds = parseInt(paramTimeArray[0])*3600 + parseInt(paramTimeArray[1])*60 + parseInt(paramTimeArray[2]);
    }
    
    return (valueTimeSeconds > paramTimeSeconds);
    
  },
    "The time entered is too old"
);


$.validator.addMethod(
  "timeMax",
  function( value, element, param ) {   
    
    var valueTimeArray = /^(\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    var paramTimeArray = /^(\d{1,2}):(\d{2}):(\d{2})$/.exec(param);
    var valueTimeSeconds = "";
    var paramTimeSeconds = "";
    if(valueTimeArray){
      valueTimeSeconds = parseInt(valueTimeArray[0])*3600 + parseInt(valueTimeArray[1])*60 + parseInt(valueTimeArray[2]);    
    }
    if(paramTimeArray){
      paramTimeSeconds = parseInt(paramTimeArray[0])*3600 + parseInt(paramTimeArray[1])*60 + parseInt(paramTimeArray[2]);
    }
    
    
      
    return (valueTimeSeconds < paramTimeSeconds);
  },
    "The time entered must be oldest"
);
