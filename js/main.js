// Main java script page for this project.

// This array will hold the batch tapes (used in create batch)
var batchTapes = [];
// This array holds the objects containing the media barcode and status of the batch that is being
// checked in.
var batchMembers = [];
// This variable stores the number of batch members that have been checked.  It has to be global.
var membersChecked = 0;
/* This variable stores the ID of the returning media batch.  It's something of a kluge to solve a problem
 * after the programmer (Douglas James) realized that the batch ID was not being tracked as the
 * returning batch was being processed.  It was a simple way to fix that little problem.
 */
var returningBatchID = 0;

// This function sets and formats the current time.
function time_to_text(time)
{
  var tmp = new Date(time*1000);
  var datestr = (tmp.getMonth()+1)+'/'+tmp.getDate()+'/'+tmp.getFullYear();
  return datestr;
}

// This function returns today's time stamp.$batchData
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
//   * func is the callback Fn to run when the event fi$batchDatares
function add_event(element, event, func) 
{
    if(document.addEventListener)
    { //code for non-IE
    	element.addEventListener(nonie_events[event],func,false);
    }
    else
    {
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

/* This function submits data provided by the UI to the appropriate AJAX helper function that actually
 * adds a batch and its members to the database.  It accepts no input and returns no output.  It
 * does check to see if the batchTapes array is empty or not which prevents submitting an empty
 * batch to the database.
 * 
 * @returns {undefined}
 */
function addBatch()
{
  //console.log("In addBatch function");
  if(batchTapes.length > 0)
  {
    var tempObj = new Object();
    tempObj.date = $("#createdate").val();
    tempObj.bcodes = batchTapes;
    var procData = prepData(tempObj, "submitBatch");
    //console.log(procData);
    ajaxCall(procData, showCreateBatchResultCallback);
    //formObj.reset(); // reset form
    batchTapes = []; // wipe out batchTapes array
  }
  else
  {
    var targetDiv = document.getElementById("battprslt");
    var textOut = "Cannot submit empty batch!";
     targetDiv.innerHTML = textOut;
  }
}
/* This function gets the history of of an individual piece of media and displays it.  It returns nothing.
 * 
 * @returns {undefined}
 */
function getMediaHistory()
{
  var formObj = document.getElementById("indmediahistory");
  var tempObj = new Object();
  tempObj.mediaid = formObj.elements["mediaid"].value;
  var procData = prepData(tempObj, "lookupMediaDetail");
  ajaxCall(procData, showMediaDetailCallback);
   formObj.elements["mediaid"].value = "";
}

/* This function gets the returning batch IDs for the Modify Batch page.  It returns nothing.
 * 
 * @returns {undefined}
 */
function getReturningBatchIDs()
{
  //console.log("In getReturningBatchIDs.");
  var tempObj = new Object();
  tempObj.returnDays = 90;
  var procData = prepData(tempObj, "lookupReturningBatches");
  //console.log(procData);
  ajaxCall(procData, showReturningBatchesCallback);
}

/* This function accepts a batch ID submitted by the value of the button that was clicked.
 * It then preps the data and makes the appropriate AJAX call.
 * @param {type} batchID
 * @returns {undefined}
 */
function getReturningBatchMembers(batchID)
{
  document.getElementById('checkedbatch').disabled = true;
  var procData = prepData(batchID, "lookupBatchMembers");
  ajaxCall(procData, prepBatchMembersCallback);
}

/* This function takes the global batchMembers array, gathers additional data from the form that is
 * necessary to check the batch in and assign it a location and then submits it to the appropriate
 * AJAX function for processing and insertion into the database.  It returns nothing.
 * 
 * @returns {undefined}
 */
function batchCheckIn()
{
  var tempObj = new Object();
  tempObj.batchID = returningBatchID;
  //tempObj.date = $("#checkindate").val();
  tempObj.locID = $("#checkinloc").val();
  tempObj.members = batchMembers;
  var procData = prepData(tempObj, "procBatchCheckIn");
  //console.log("Construct: ", procData);
  returningBatchID = 0; //Wipes out this global variable once the batch is sumitted to the AJAX function.
  ajaxCall(procData, showBatchCheckInResultsCallback);
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
   if (e.keyCode != 13){
       return;
   }
   // Check intput format for [5 or 6 numbers] [1 letter] [1 number]
   if((match = str.match(/([0-9]{5,6}[L][0-9])/))) {
     //document.getElementById("addtapeform").submit();
     $("#addtapeform").submit();
   }
}

/* This function is the listener for the text box on modifytape.php.  It listenes for a carriage return and
 * then calls the appropriate functions to process the input.  It returns nothing.
 * @param {type} mediaElement
 * @returns {undefined}
 */
modIndTapeInputCapture = function(mediaElement)
{
  var tempObj = new Object();
  var mediaBarCode = this.value;
   if (mediaElement.keyCode != 13){
       return;
   }
   // Check intput format for [5 or 6 numbers] [1 letter] [1 number]
   if((match = mediaBarCode.match(/([0-9]{5,6}[L][0-9])/))) {
     tempObj.locID = $("#assignloc").val();
     tempObj.mediaID = mediaBarCode;
     var procData = prepData(tempObj, "procIndMedia");
     //console.log("Data: ", tempObj);
     ajaxCall(procData, modIndMediaCallback);
   }
   else
   {
     console.log("Invalid media bar code.");
   }
   $('#indmedid').val("");
};

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
    if (request.readyState == 4)
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
  if (data.length == 0)
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
  if(data.RSLT == 0)
  {
    alert(data.MSSG);
  }
  else
  {
    alert(data.MSSG);
  }
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
  if (data.length == 0)
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
  if (data.length == 0)
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
   if(data.length == 0)
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
  if(data.length == 0)
  {
    textOut += "Strange. Empty data set sent.";
  }
  else
  {
    //console.log(data);
    if(data.RSLT == "0")
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

/* This function accepts no arguments.  It gets information out of the appropriate form, creates and object
 * and then submits the object to the addNewUser() AJAX function.
 */
function addUser()
{
  var formObj = document.getElementById("addnewuserform");
  var tempObj = new Object();
  tempObj.userFirst = formObj.elements['newuserfirst'].value;
  tempObj.userLast = formObj.elements['newuserlast'].value;
  tempObj.userName = formObj.elements['newusername'].value;
  tempObj.passwd = formObj.elements['newuserpasswd'].value;
  tempObj.accessLvl = formObj.elements['useraccesslevel'].value;
  var procData = prepData(tempObj, "addNewUser");
  ajaxCall(procData, adduserCallback);
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
  if(data.length == 0)
  {
    textOut += "Strange. Empty data set sent.";
  }
  else
  {
    //console.log(data);
    if(data.RSLT == "0")
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
  if(data.length == 0)
  {
    textOut += "Strange. Empty data set sent.";
  }
  else
  {
    //console.log(data);
    if(data.RSLT == "0")
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

/* Show tape callback function.  This function takes a data object and displays data in the correct
 * results div on the calling page.
 * @param {type} data
 * @returns {undefined}
 */
var showAddTapeResultCallback = function(data)
{
  //console.log(data);
  if(data.CALLER == "batch")
    displayLocation = "battprslt";
  else
    displayLocation = "tprslt";
  if(data.RSLT == "0") 
    successVal = "success";
  else if (data.RSLT == "1") 
    successVal = "failure";
  else
    successVal = "no_res";
      // Create the tape div in results
    show_tape(document.getElementById(displayLocation), data.DATA, successVal);
};
/* This function reads input from the keyboard on the createbatch.php page.  It listens for a
 * carriage return.  When it sees a carriage return it checks the final value in the text box.  If the
 * final value is valid, it moves on to the code that checks to see if the tape is available for use in the
 * batch.
 * @param {type} mediaElement
 * @returns {undefined}
 */
var batchTapeInputCapture = function(mediaElement) 
{
  var mediaBarCode = this.value;
  var successVal = "no_res";
  if(mediaElement.keyCode != 13)
  {
    return;
  }
  // Check intput format for [5 or 6 numbers] [1 letter] [1 number]
  if((match = mediaBarCode.match(/([0-9]{5,6}[L][0-9])/))) 
  {
    //console.log(mediaBarCode);
    var codePresent = $.inArray(mediaBarCode, batchTapes);
    if(codePresent == "-1")
    {
      var procData = prepData(mediaBarCode, "checkBatchMembers");
      //console.log(procData);
      ajaxCall(procData, batchTapeCheckCallback);
    }
  }
  else
  {
    console.log("Invalid media bar code");
  }
  $('#mediaid').val("");
};

/* This function reads input from the text box on the modifybatch.php page which is where batches
 * can be checked back upon return from offsite storage.  When a carriage return is detected, this
 * function checks to be sure a valid bar code has been received and then calls the necessary
 * functions to ensure that the value exists in the batch, and then switches the checked in value in
 * the batchMembers array.  It also keeps a counter as the batch members are checked in.  Once the
 * counter matches the number of members in the batch, the submit button is enabled on
 * modifybatch.php.  Control is handed off to the functions called by the submit button once it is
 * clicked.
 * 
 * @param {type} mediaElement
 * @returns {undefined}
 */
var batchMemberInputCapture = function(mediaElement) 
{
  var mediaBarCode = this.value;
  var toCheck = batchMembers.length;
  // Ignore all key presses except the 'enter' key.
  if(mediaElement.keyCode != 13)
  {
    return;
  }
  // Check intput format for [5 or 6 numbers] [1 letter] [1 number]
  if((match = mediaBarCode.match(/([0-9]{5,6}[L][0-9])/))) 
  {
    for(cntr = 0, len = batchMembers.length; cntr < len; cntr++)
    {
      if(batchMembers[cntr].ID == mediaBarCode && batchMembers[cntr].CHK == "false")
      {
        batchMembers[cntr].CHK = "true";
        membersChecked++;
        //console.log("Members: ", toCheck, "Checked: ", membersChecked);
        $("#retbatchmbrs").empty();
        showBatchMembersCallback(batchMembers);
      }
      if(membersChecked == toCheck)
      {
        document.getElementById('checkedbatch').disabled = false;
        membersChecked = 0;
      }
    }
  }
  else
  {
    console.log("Invalid media bar code.");
  }
  $('#btchmbrid').val("");
};

/* This function accepts data from the calling AJAX function, calls the showAddTapeResultCallback
 * which will display a colored message based on the backend function data provied.  It checks the
 * results itself and if it finds the success condition, the media bar code is added to the batchTapes
 * array.  If the result is not successful, the media bar code is NOT added to the array.
 * 
 * @param {type} data
 * @returns {undefined}
 */
var batchTapeCheckCallback = function(data) 
{
  //console.log("Now in batchTapeCheckCallback");
  //console.log(data);
  data.CALLER = "batch";
  showAddTapeResultCallback(data);
  // If successful.
  if(data.RSLT == "0")
  {
     batchTapes.push(data.DATA);
   }
   //console.log(batchTapes);
};

/* This function does noting more than displays the results of inserting a new batch into the database
 * by means of an AJAX call to the appropriate backend function.  It displays in the appropriate
 * location on the screen whether or not the insert was successful.
 * 
 * @param {type} data
 * @returns {undefined}
 */
var showCreateBatchResultCallback = function(data)
{
  var targetDiv = document.getElementById("battprslt");
  var textOut = "";
  // show success/failure message in result box
  //console.log("In showCreateBatchResultCallback");
  //console.log(data);
  if(data.RSLT == "0")
  {
    textOut = data.MSSG;
  }
  else
  {
    textOut = "Unable to create batch!";
  }
  targetDiv.innerHTML = textOut;
};

/* This function accepts data passed in from the AJAX function and then parses it.  It outputs its results
 * to the designated location on the page.  It returns nothing.
 */
var showMediaDetailCallback = function(data)
{
  var tempObj = new Object();
  var targetDiv = document.getElementById("mediadetail");
  var textOut = "";
  //console.log("In showMediaDetailcallback");
  //console.log(data);
  if(data.RSLT == "1")
  {
    textOut = data.MSSG;
  }
  else
  {
    tempObj = data.DATA;
    textOut = "<CENTER>Detail for: " + tempObj.mediaLabel + "</CENTER><BR>";
    textOut += "Type:     " + tempObj.mediaType + "<BR>";
    textOut += "Vendor: " + tempObj.vendor + "<BR>";
    textOut += "PO:        " + tempObj.poNum + "<BR>";
    textOut += "<CENTER>History</CENTER><BR>";
    textOut += "=================================<BR>";
    //console.log(tempObj.mediaHistory['0']);
    for(cntr = 0, len = tempObj.mediaHistory.length; cntr < len; cntr++)
    {
      textOut += "Date:       " + tempObj.mediaHistory[cntr].date + "<BR>";
      textOut += "Location: " + tempObj.mediaHistory[cntr].location + "<BR>";
      textOut += "User:       " + tempObj.mediaHistory[cntr].user + "<BR>";
      textOut += "Batch:     " + tempObj.mediaHistory[cntr].batch_id + "<BR>";
      textOut += "Member: " + tempObj.mediaHistory[cntr].batch_num + "<BR>";
      textOut += "=================================<BR>";
    }
  }
  targetDiv.innerHTML = textOut;
};

/* This function accepts the database query results from the AJAX function, it then checks it for a valid
 * query result.  If it doesn't find one, it prints a message contained in the returning data.  If it does
 * find a valid query, it parses through the array of objects contained in the DATA field and displays
 * buttons containing that data in the appropriate location on the web page.  It returns nothing.
 */
var showReturningBatchesCallback = function(data)
{
  //console.log("In showReturningBatchesCallback");
  //console.log(data.DATA[0]);
  var targetDiv = document.getElementById("retbatchlist");
  var htmlOut = "";
  if(data.RSLT == "1")
  {
    htmlOut = "<label class=\"spn\">" + data.MSSG + "</label>";
  }
  else
  {
    for(cntr = 0, len = data.DATA.length; cntr < len; cntr++)
    {
      htmlOut += "<button class=\"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button btchbtn\" value=\"" + data.DATA[cntr].ID + "\" role=\"button\" aria-disabled=\"false\">";
      htmlOut += "<span class=\"ui-button-text\">" + data.DATA[cntr].label + "</span>";
      htmlOut += "</button>";
    }
  }
  targetDiv.innerHTML = htmlOut;
  // Listener to register a click on the buttons created by this function.
  $('.btchbtn').click(function() 
  {
    batchMembers = [];  //Wipes out the array at each click of a batch button.
    membersChecked = 0;  //Sets the global variable back to zero for a new batch.
    returningBatchID = this.value;
    $("#retbatchmbrs").empty();
    getReturningBatchMembers(this.value);
  });
};

/* This function accepts an array of media bar codes, and puts them in an array with the default
 * status set.  It then passes the prepped array off to the function that handles the display.
 */
var prepBatchMembersCallback = function(data)
{
  //console.log("Data values received:", data.DATA);
  if(data.RSLT == "1")
  {
    show_tape(document.getElementById("retbatchmbrs"), "No members found! Empty batch.", "failure");
  }
  else
  {
    for(cntr = 0, len = data.DATA.length; cntr < len; cntr++)
    {
      var tmpObj = new Object();
      tmpObj.ID = data.DATA[cntr];
      tmpObj.CHK = "false";
      batchMembers.push(tmpObj);
    }
    showBatchMembersCallback(batchMembers);
  }
};

/* This function accepts an array of media bar codes and their current status in the array.  In this case
 * it checks to see if they have been entered or not and then displays the members with the appropriate
 * color background.
 */
var showBatchMembersCallback = function(data)
{
  //console.log("Callback Display: ", data[0]);
  var successVal = "failure";
  var targetDiv = document.getElementById("retbatchmbrs");
  for(cntr = 0, len = data.length; cntr < len; cntr++)
  {
    if(data[cntr].CHK == "true")
      successVal = "success";
    else
      successVal = "no_res";
    show_tape(document.getElementById("retbatchmbrs"), data[cntr].ID, successVal);
  }
};

/* This function accepts a status from the AJAX function that is called by the checkBatchIn function
 * and displays whether or not the batch check in process was completed or not.  It returns nothing.
 */
var showBatchCheckInResultsCallback = function(data)
{
  //console.log("showBatchCheckInResultsCallback: ", data);
  var targetDiv = document.getElementById("retbatchmbrs");
  $("#retbatchmbrs").empty();
  if(data.RSLT == "1")
    show_tape(document.getElementById("retbatchmbrs"), "Batch check in failed!", "failure");
  else
    show_tape(document.getElementById("retbatchmbrs"), "Batch check in successful.", "success");
};


var modIndMediaCallback = function(data)
{
  //console.log("modIndMediaCallback: ", data);
  $("#indmedassign").empty();
  if(data.RSLT == "1")
  {
    show_tape(document.getElementById("indmedassign"), data.MSSG, "failure");
  }
  else
  {
    show_tape(document.getElementById("indmedassign"), data.MSSG, "success");
  }
};