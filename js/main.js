// Main java script page for this project.

// This array will hold the batch tapes (used in create batch)
batchTapes = [];

// This function sets and formats the current time.
function time_to_text(time)
{
  var tmp = new Date(time*1000);
  var datestr = (tmp.getMonth()+1)+'/'+tmp.getDate()+'/'+tmp.getFullYear();
  return datestr;
}

// This function returns today's time stamp.
function today()
{
  var temp = new Date();
  return parseInt(temp.getTime()/1000);
}

// Arrays for specifying events based on an index
nonie_events = ['click','keyup','mouseover','mouseout'];
ie_events = ['onclick','onkeyup','onmouseover', 'onmouseout'];
events = {'click':0,'keyup':1,'m_over':2,'m_out':3};

// Attach an event listener to a JavaScript DOM object
//   * element is a DOM element, not a jQuery one
//   * event is an int, typically referenced by events.click (see above)
//   * func is the callback Fn to run when the event fires
function add_event(element, event, func) {
    if(document.addEventListener){ //code for non-IE
    	element.addEventListener(nonie_events[event],func,false);
    }
    else{
	element.attachEvent(ie_events[event],func); //code for IE
    }
}

// Add tape form builds the tape object, calls the AJAX function and the callback function.
function addTape() 
{
  var formObj = document.getElementById("addtapeform");
  var tempObj = new Object();
  tempObj.venID = formObj.elements["vendor"].value;
  tempObj.locationID = formObj.elements["location"].value;
  tempObj.poNum = formObj.elements["ponumber"].value;
  tempObj.tapeID = formObj.elements["tapeid"].value;
  var procData = prepData(tempObj, "testNewTape");
  ajaxCall(procData, showAddTapeResultCallback);
  formObj.elements["tapeid"].value = "";
}

function addBatch()
{
  var formObj = document.getElementById("createbatchform");
  var tempObj = new Object();
  tempObj.createdate = formObj.elements['createdate'].value;
  tempObj.tapeid = formObj.elements['tapeid'].value;
  console.log(tempObj);
 // var procData = prepData(tempObj.tapeid, "checkBatchMembers");
  //console.log(procData);
  //ajaxCall(procData, batchTapeCheckCallback);
  //ajaxCall(procData, showCreateBatchResultCallback);
  //ajaxCall(batchTapes, showCreateBatchResultCallback);
  formObj.reset(); // reset form
  batchTapes = []; // wipe out batchTapes array
}

// Create tape div and add it to the specified container
//   * container is a DOM object, not a jQuery one
//   * labelText is the tape barcode
//   * flag is text for success, failure, or empty if unknown
function show_tape(container, labelText, flag) {
  //console.log(container, labelText, flag);  
  var tmpDiv = document.createElement('div');
    tmpDiv.innerHTML = labelText;
    tmpDiv.className = "tape";
    
    if (flag == "success") {
        tmpDiv.className += " success";
    } else if (flag == "failure") {
        tmpDiv.className += " fail";
    } else {
        tmpDiv.className += " no_res";
    }

    // jQuery for adding to the top of a container
    $(container).prepend(tmpDiv);
}

// Analyze tape input and submit the addtapeform().
tapeInputCapture = function(e) 
{
   var str = this.value;
   var successVal = "no_res";
   // Ignore all key presses except for Enter key
   //console.log(e.keyCode);
   //console.log(e);
   if (e.keyCode != 13){
       return;
   }
   // Check intput format for [5 or 6 numbers] [1 letter] [1 number]
   if((match = str.match(/([0-9]{5,6}[L][0-9])/))) {
     //document.getElementById("addtapeform").submit();
     $("#addtapeform").submit();
   }
}

// This function accepts a json object and converts it to a json string.
function toJSON(jsonObject)
{
  return JSON.stringify(jsonObject);
}

// This function accepts a json string and converts it to a json object.
function fromJSON(jsonData)
{
  return JSON.parse(jsonData);
}

/* This is the function that makes the calls to the functions stored in the ajax.php file.
 * It accepts as arguments a json string and then the name of the callback function
 * that should be executed.  It formats the received data as a json string and then
 * sends it on to the designated call back function.
 */
function ajaxCall(jsonString, callBackFuncName)
{
  var url = 'includes/ajax.php';
  var params = 'request=' + jsonString;
  var request = new XMLHttpRequest();

  request.onreadystatechange = function ()
  {
    if (request.readyState === 4)
    {
      var jObj = fromJSON(request.responseText);
      callBackFuncName(jObj);
    }
  };
  request.open('POST', url, false);
  request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  request.send(params);
}

/* This function accepts a json object and a function name.  It takes both parameters, addes them to
 * a temporary object, converts them to a json string and returns them to the calling function.  This
 * helps to normalize the data getting passed into ajaxCall().
 */
function prepData(data, funcName)
{
  var tempObj = new Object();
  tempObj.func = funcName;
  tempObj.data = data;
  return toJSON(tempObj);
}

/* This function accepts data from ajaxCall() in the form of an array of objects containing the current
 * list of users.  It places a header at the beginning in the textOut variable.  It then loops through the
 * array of objects (the first loop) and loops through the object (the second loop) and gets the user
 * name out of the object.  The name of the user is added to the textOut variable which is ultimately
 * displayed on the scren with the finished list of users.  It returns nothing.
 */
var listUsersCallback = function (data)
{
  var targetDiv = document.getElementById("show_users");
  var textOut = "Current Users: <BR><BR>";
  if (data.length === 0)
  {
    textOut += "No users found.<BR>";
  }
  else
  {
    for (cntr = 0, len = data.length; cntr < len; cntr++)
    {
      for (objs in data[cntr])
      {
        textOut += data[cntr][objs] + "<BR>";
      }
    }
  }
  targetDiv.innerHTML = textOut;
};

// Call back function for add user form.
var adduserCallback = function (data)
{
  var targetDiv = document.getElementById("adduser");
  targetDiv.innerHTML = data;
};

/* This is the list media types call back function.  It accepts an array of objects from the PHP side of the
 * house, checks to see if the array is empty and prints a message if it is.  If the arry contains objects, it
 * iterates through them and displays them.  It returns nothing.
 * 
 */
var listMediaCallback = function (data)
{
  var targetDiv = document.getElementById("medialist");
  var textOut = "Current Media Types:<BR><BR>";
  if (data.length === 0)
  {
    textOut += "None on file.<BR>";
  }
  else
  {
    for (cntr = 0, len = data.length; cntr < len; cntr++)
    {
      for (objs in data[cntr])
      {
        textOut += data[cntr][objs] + "<BR>";
      }
    }
  }
  targetDiv.innerHTML = textOut;
};

/* This is the vendor list callback function.  It accepts an array of objects from the PHP side of the house,
 * checks to see if the returned array is empty and displays a message if it is.  If the array contains objects,
 * it iterates through them and displays them.  It returns nothing.
 */
var listVendorCallback = function (data)
{
  var targetDiv = document.getElementById("vendorlist");
  var textOut = "Current Vendors on File:<BR><BR>";
  if (data.length === 0)
  {
    textOut += "None on file.<BR>";
  }
  else
  {
    for (cntr = 0, len = data.length; cntr < len; cntr++)
    {
      for (objs in data[cntr])
      {
        textOut += data[cntr][objs] + "<BR>";
      }
    }
  }
  targetDiv.innerHTML = textOut;
};

/* This is the location list callback function.  It accepts an array of objects from the PHP side of the house,
 *  checks to see if the returned array is empty and displays a message if it is.  If the array contains objects,
 *  it iterates through them and displays them.  It returns nothing.
 */
var listLocationCallback = function(data)
{
   var targetDiv = document.getElementById("locationlist");
   var textOut = "Current Locations:<BR><BR>";
   if(data.length === 0)
   {
      textOut += "None set in system.<BR>";
   }
   else
   {
      for(cntr = 0, len = data.length; cntr < len; cntr++)
      {
         for(objs in data[cntr])
         {
            textOut += data[cntr][objs] + "<BR>";
         }
      }
   }
   targetDiv.innerHTML = textOut;
};

/* This function accepts an array from addVendor().  It does some checking to be sure that it actually
 * received information from addVendor() and then, based on the results of the database call, displays
 * an appropriate message.  The display is held for 5 seconds and then the form is reset to its original
 * state.  The function returns nothing.
 */
var showAddVendorResultCallback = function(data)
{
  var targetDiv = document.getElementById("vendorlist");
  var textOut = "<CENTER>STATUS:</CENTER><BR>";
  if(data.length === 0)
  {
    textOut += "Strange. Empty data set sent.";
  }
  else
  {
    //console.log(data);
    if(data.RSLT === "0")
    {
      textOut += "Success!<BR><BR>" + data.MSSG;
    }
    else
    {
      textOut += "Failed!<BR><BR>" + data.MSSG;
    }
  }
  targetDiv.innerHTML = textOut;
  setTimeout(function (){ $("#add_vendor").click(); }, 5000);
};

/* This function accepts no arguments.  It gets information out of the appropriate form, creates an object
 * and then submits the appropriate data to the addNewVendor() AJAX function.  The result of the call
 * to the database is returned from the PHP side of the house to this function.  This fuction then submits
 * the returned information to the appropriate callback function and resets the form to defaults.  It
 * returns nothing.
 */
function addVendor()
{
  var formObj = document.getElementById("addvendorform");
  var tempObj = new Object();
  tempObj.vendorname = formObj.elements["vendorname"].value;
  var procData = prepData(tempObj, "addNewVendor");
  ajaxCall(procData, showAddVendorResultCallback);
  formObj.reset();
}


/* This function accepts no arguments.  It gets information out of the appropriate form, creates an object
 * and then submits the object to the addNewMedia() AJAX function.  The result of the call to the database
 * is returned from the PHP side of the house to this function.  This function then submits the returned
 * information to the appropriate callback function and resets the form to defaults.  It returns nothing.
 */
function addMedia()
{
  var formObj = document.getElementById("addmediaform");
  var tempObj = new Object();
  tempObj.medianame = formObj.elements['mediatypename'].value;
  var procData = prepData(tempObj, "addNewMedia");
  ajaxCall(procData, showAddMediaResultCallback);
  formObj.reset();
}

/* This function accepts no arguments.  It gets information out of the appropriate form, creates an object
 * and then submits the object to the addNewLocation() AJAX function.  The result of the call to the database
 * is returned from the PHP side of the house to this function.  This function then submits the returned 
 * information to the appropriate callback function and resets the form to defaults.  It returns nothing.
 */
function addLocation()
{
   var formObj = document.getElementById("addlocationform");
   var tempObj = new Object();
   tempObj.locationname = formObj.elements['locationname'].value;
   var procData = prepData(tempObj, "addNewLocation");
   ajaxCall(procData, showAddLocationResultCallback);
   formObj.reset();
}

/* This function accepts an array from addMedia().  It does some checking to be sure that it actually
 * received information from addMedia() and then, based on the results of the databshow_tape(document.getElementById("results"), str, successVal);ase call, displays
 * an appropriate message.  The display is held for 5 seconds and then the form is reset to its original
 * state.  The function returns nothing.
 */
var showAddMediaResultCallback = function(data)
{
  var targetDiv = document.getElementById("medialist");
  var textOut = "<CENTER>STATUS:</CENTER><BR>";
  if(data.length === 0)
  {
    textOut += "Strange. Empty data set sent.";
  }
  else
  {
    //console.log(data);
    if(data.RSLT === "0")
    {
      textOut += "Success!<BR><BR>" + data.MSSG;
    }
    else
    {
      textOut += "Failed!<BR><BR>" + data.MSSG;
    }
  }
  targetDiv.innerHTML = textOut;
  setTimeout(function (){ $("#add_media").click(); }, 5000);
};

/* This function accepts an array from addLocation().  It does some checking to be sure that it actually
 * received information from addLocation() and then, based on the results of the database call, displays
 * an appropriate message.  The display is held for 5 seconds and then the form is reset to its original
 * state.  The function returns nothing.
 */
var showAddLocationResultCallback = function(data)
{
  var targetDiv = document.getElementById("locationlist");
  var textOut = "<CENTER>STATUS:</CENTER><BR>";
  if(data.length === 0)
  {
    textOut += "Strange. Empty data set sent.";
  }
  else
  {
    //console.log(data);
    if(data.RSLT === "0")
    {
      textOut += "Success!<BR><BR>" + data.MSSG;
    }
    else
    {
      textOut += "Failed!<BR><BR>" + data.MSSG;
    }
  }
  targetDiv.innerHTML = textOut;
  setTimeout(function (){ $("#add_location").click(); }, 5000);
};

// tapeCallback function
var showAddTapeResultCallback = function(data)
{
  //console.log(data);
  if(data.RSLT === "0") 
    successVal = "success";
  else if (data.RSLT === "1") 
    successVal = "failure";
  else
    successVal = "no_res";
      // Create the tape div in results
    show_tape(document.getElementById("tprslt"), data.DATA, successVal);
};

batchTapeInputCapture = function(e) 
{
  var str = this.value;
  var successVal = "no_res";
  // Ignore all key presses except for Enter key
  //console.log(e.keyCode);
  //console.log(e);
  if (e.keyCode != 13){
    return;
  }
  // Check intput format for [5 or 6 numbers] [1 letter] [1 number]
  if((match = str.match(/([0-9]{5,6}[L][0-9])/))) 
  {
     /* make ajax call to check tape (use batchTapeCheckCallback as callback */
     ajaxCall(str, batchTapeCheckCallback);
  }
};

batchTapeCheckCallback = function(data) 
{
  console.log(data);
  var procData = prepData(data.tapeid, "checkBatchMembers");
  //showAddTapeResultCallback(data);
  showAddTapeResultCallback(procData.DATA);      
  // If successful.
  if(data.RSLT === "0")
  {
     batchTapes.push(data.DATA);
   }
};

showCreateBatchResultCallback = function(data)
{
  // show success/failure message in result box
  console.log(data);
};

showAddTapeResultCallback = function(data)
{
  console.log(data);
};
