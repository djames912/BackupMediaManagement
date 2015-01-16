<?php
require_once 'functions.php';
session_start();
#---------------------------------------------------------------
# Great/Generic peice of code for handling ajax request/returns 
# echos a Json string
#---------------------------------------------------------------
if($_SERVER['REQUEST_METHOD'] == 'POST')
  {	
    $jstring = stripslashes($_POST['request']);
    //error_log($jstring);
    if($jstring != null && ($fobj = json_decode($jstring)))
    {
       $func = $fobj->func;
	$res = $func($fobj->data);	
	echo json_encode($res);
    }	
    else
    {
      echo "{'error':'json string empty or incorrectly formatted'}";
    }
  }
  else
  {
    echo "{'error':'Incorrect Request type!'}";
  }
  
// This is nothing more than a test function.  That's it.
function test($junk)
{
  //error_log($junk);
  return $junk;
}

/* This function calls getTableContents() with appropriate entries passed to get a list of user names.
 * It returns an array of objects containing the user names.
 */
function generateUserList()
{
  $rawOutput = getTableContents('users', 'uname');
  return $rawOutput['DATA'];
}

/* This function calls getTablecontents() with appropriate entries passed to get a list of media types.
 * It returns an array of objects that contain the media types.
 */
function generateMediaList()
{
  $rawOutput = getTableContents('mtype', 'label');
  return $rawOutput['DATA'];
}

/* This function calls getTableContents() with appropriate entries passed to get a list of vendors.  It
 * returns an array of objects that contain the vendor names.
 */
function generateVendorList()
{
  $rawOutput = getTableContents('vendors', 'v_name');
  return $rawOutput['DATA'];
}

/* This function calls getTableContents() with appropriate entries passed to get a list of locations.  It
 * returns an array of objects that contain the location names.
 */
function generateLocationList()
{
   $rawOutput = getTableContents('locations', 'label');
   return $rawOutput['DATA'];
}

/* This is the helper function that receives and prepares data submitted from the appropriate web form
 * to the backend PHP function that actually submits that data to the database.  It returns the output
 * from the database submission function as an array.
 */
function addNewVendor($vendorData)
{
  //error_log(print_r($vendorData, true));
  $rawOutput = addType('vendors', 'v_name', $vendorData->vendorname);
  //error_log(print_r($rawOutput, true));
  return $rawOutput;
}

/* This is the helper function that receives and prepares data submitted by the appropriate web form to
 * the backend PHP function that actually submits data to the database.  It returns the output from the
 * database submission function as an array.
 */
function addNewMedia($mediaData)
{
  $rawOutput = addType('mtype', 'label', $mediaData->medianame);
   return $rawOutput;
}

/* This is the helper function that receives and prepares data submitted by the appropriate web form to
 * the backend PHP function that actually submits data tot he database.  It returns the output from the
 * database submission function as an array.
 */
function addNewLocation($locationData)
{
  $rawOutput = addType('locations', 'label', $locationData->locationname);
  return $rawOutput;
}

/* This function accepts an object as an argument.  It checks to see if the tape is already present in the
 * database.  If it is, it returns that the tape is already present.  If the tape is not present in the database
 * passes the object to addTape() to be inserted into the database.  The function returns whether or not
 * the insert was successful.
 */
function testNewTape($mediaData)
{
  $r_val = array();
  $mediaData->uname = $_SESSION['UserName'];
  //error_log(print_r($mediaData, true));
  $rawOutput = tapeExists(trim($mediaData->tapeID));
  //error_log(print_r($rawOutput, true));
  if($rawOutput['RSLT'] == "1")
  {
    $insertResult = addTape($mediaData);
    //error_log(print_r($insertResult, true));
    if($insertResult['RSLT'] == "0")
    {
      $r_val['RSLT'] = "0";
      $r_val['MSSG'] = "$mediaData->tapeID added to database.";
      $r_val['DATA'] = trim($mediaData->tapeID);
    }
    else
    {
      $r_val['RSLT'] = "1";
      $r_val['MSSG'] = "Failed to add $mediaData->tapeID to database.";
      $r_val['DATA'] = trim($mediaData->tapeID);
    }
  }
  else
  {
    $r_val['RSLT'] = "1";
    $r_val['MSSG'] = "$mediaData->tapeID already present in database.";
    $r_val['DATA'] = trim($mediaData->tapeID);
  }
  return $r_val;
}

/* This function accepts a tape bar code.
 * It then checks to see if the tape is available for use in a batch that is being created by the user.
 * It returns whether or not the tape is available for use in the batch.
 */
function checkBatchMembers($mediaBarCode)
{
  //error_log(print_r($mediaBarCode, true));
  $rawOutput = tapeAvailable($mediaBarCode);
  $rawOutput['DATA'] = trim($mediaBarCode);
  //error_log(print_r($rawOutput, true));
  return $rawOutput;
}

/* This function accepts an object consisiting of an array of tapes, the date and any other data that
 * comes from the application UI.  The backend function will check for those values and if they are not
 * found it will add appropriate defaults.
 */
function submitBatch($batchData)
{
  // The following lines check property values in the object passed in from Javascript.  If they don't
  // exist, they are added to the object with default values.  Ideally, the object properties are set in
  // the program via the UI.
  if(!property_exists($batchData, 'uname'))
    $batchData->uname = $_SESSION['UserName'];
  if(!property_exists($batchData, 'rdays'))
    $batchData->rdays = $GLOBALS['defaultReturnTime'];
  if(!property_exists($batchData, 'bloc'))
    $batchData->bloc = $GLOBALS['batchCreateLocation'];
  error_log(print_r($batchData, true));
  $rawOutput = addTapeBatch($batchData);
  return $rawOutput;
}
?>
