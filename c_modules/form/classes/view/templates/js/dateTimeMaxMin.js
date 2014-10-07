$.validator.addMethod(
  "dateTimeMin",
  function( value, element, param) {
    
    var valueDateTimeArray = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    var paramDateTimeArray = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(param);
    var valueDateTime = "";    
    var paramDateTime = "";
    
    if(valueDateTimeArray)
      valueDateTime = new Date(valueDateTimeArray[1], valueDateTimeArray[2], valueDateTimeArray[3], valueDateTimeArray[4], valueDateTimeArray[5], valueDateTimeArray[6]);
    if(paramDateTimeArray)
      paramDateTime = new Date(paramDateTimeArray[1], paramDateTimeArray[2], paramDateTimeArray[3], paramDateTimeArray[4], paramDateTimeArray[5], paramDateTimeArray[6]);
    
    return (valueDateTime.getTime() > paramDateTime.getTime());
    
  },
    "The date entered is too old"
);


$.validator.addMethod(
  "dateTimeMax",
  function( value, element, param ) {    
    var valueDateTimeArray = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    var paramDateTimeArray = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(param);
    var valueDateTime = "";    
    var paramDateTime = "";
    
    if(valueDateTimeArray)
      valueDateTime = new Date(valueDateTimeArray[1], valueDateTimeArray[2], valueDateTimeArray[3], valueDateTimeArray[4], valueDateTimeArray[5], valueDateTimeArray[6]);
    if(paramDateTimeArray)
      paramDateTime = new Date(paramDateTimeArray[1], paramDateTimeArray[2], paramDateTimeArray[3], paramDateTimeArray[4], paramDateTimeArray[5], paramDateTimeArray[6]);
    
    return (valueDateTime.getTime() < paramDateTime.getTime());
  },
    "The date entered must be oldest"
);
