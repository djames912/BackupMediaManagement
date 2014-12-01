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
       if(request.readyState == 4) 
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

// Call back function for generating a current list of users.
var listUsersCallback = function(data)
{
  //console.log("Inside listUsersCallback");
  var targetDiv = document.getElementById("show_users");
  targetDiv.innerHTML = data;
};

// Call back function for add user form.
var adduserCallback = function(data)
{
  var targetDiv = document.getElementById("adduser");
  targetDiv.innerHTML = data;
}