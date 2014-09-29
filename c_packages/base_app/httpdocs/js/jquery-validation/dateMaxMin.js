$.validator.addMethod(
  "dateMin",
  function( value, element, param) {
    
    var valueDateArray = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(value);
    var paramDateArray = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(param);
    var valueDate = "";    
    var paramDate = "";
    
    if(valueDateArray)
      valueDate = new Date(valueDateArray[1], valueDateArray[2], valueDateArray[3]);
    if(paramDateArray)
      paramDate = new Date(paramDateArray[1], paramDateArray[2], paramDateArray[3]);
    
    return (valueDate.getTime() > paramDate.getTime());
    
  },
    "The date entered is too old"
);


$.validator.addMethod(
  "dateMax",
  function( value, element, param ) {    
    var valueDateArray = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(value);
    var paramDateArray = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(param);
    var valueDate = "";    
    var paramDate = "";
    
    if(valueDateArray)
      valueDate = new Date(valueDateArray[1], valueDateArray[2], valueDateArray[3]);
    if(paramDateArray)
      paramDate = new Date(paramDateArray[1], paramDateArray[2], paramDateArray[3]);
    
    return (valueDate.getTime() < paramDate.getTime());
  },
    "The date entered must be oldest"
);
