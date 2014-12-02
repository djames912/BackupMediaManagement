// Main java script page for this project.

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
   var	url = 'includes/ajax.php';
   var params = 'request='+jsonString;
   var request = new XMLHttpRequest();
    
   request.onreadystatechange = function () 
   {	
       if(request.readyState === 4) 
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
var listUsersCallback = function(data)
{
  var targetDiv = document.getElementById("show_users");
  var textOut = "Current Users: <BR><BR>";
  if(data.length === 0)
  {
    textOut += "No users found.<BR>";
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

// Call back function for add user form.
var adduserCallback = function(data)
{
  var targetDiv = document.getElementById("adduser");
  targetDiv.innerHTML = data;
};

/* This is the list media types call back function.
 * 
 */
var listMediaCallback = function(data)
{
  var targetDiv = document.getElementById("addmedia");
  var textOut = "Current Media Types:<BR><BR>";
  if(data.length === 0)
  {
    textOut += "None on file.<BR>";
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

/* This is the vendor list callback function.
 * 
 */
var listVendorCallback = function(data)
{
  var targetDiv = document.getElementById("vendorlist");
  var textOut = "Current Vendors on File:<BR><BR>";
  if(data.length === 0)
  {
    textOut += "None on file.<BR>";
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

function addVendor()
{
  var formObj = document.getElementById("addvendorform");
  var tempObj = new Object();
  tempObj.vendorname = formObj.elements["vendorname"].value;
  var displayData = prepData(tempObj, "addNewVendor");
  ajaxCall(displayData, listVendorCallback);
}