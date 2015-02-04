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
  //error_log(print_r($batchData, true));
  $rawOutput = addTapeBatch($batchData);
  return $rawOutput;
}

/* This function accepts a media bar code as an argument and calls the appropriate PHP function
 * to look up all the information available on that media bar code.  It returns all the available data
 * as an object.
 */
function lookupMediaDetail($mediaID)
{
  $rawOutput = getMediaDetail($mediaID->mediaid);
  //error_log(print_r($rawOutput, true));
  return $rawOutput;
}

/* This function accepts an optional argument of a number of days into the future to look for returning
 * batches of media backup.  The function returns the details of the returning batches.
 */
function lookupReturningBatches($returnData = NULL)
{
  //error_log("In lookupReturningBatches AJAX", 0);
  if(is_null($returnData))
  {
    $returnDays = $GLOBALS['maxReturnDays'];
  }
  else
  {
    $returnDays = $returnData->returnDays;
  }
  $rawOutput = getReturningBatchIDs($returnDays);
  //error_log(print_r($rawOutput, true));
  return $rawOutput;
}

/* This function accepts a batch ID as an argument.  It fetches the members of the designated batch
 * ID and returns them.
 */
function lookupBatchMembers($batchID)
{
  $rawOutput = array();
  if(!$batchID)
  {
    $rawOutput['RSLT'] = "1";
    $rawOutput['MSSG'] = "Batch ID required, not provided.";
  }
  else
  {
    $rawOutput = getBatchMembers($batchID);
    //error_log(print_r($rawOutput, true));
  }
  return $rawOutput;
}

/* This function accepts an object containing the batch ID and the member bar code numbers.  It takes
 * that data and then builds an object with additional information which it then passes to the back end
 * function checkBatchIn() which requires additional information that does not come over from the
 * Javascript code.  It returns whether or not the check in was successful.
 */
function procBatchCheckIn($batchData)
{
  $r_val = array();
  $batchData->uName = $_SESSION['UserName'];
  foreach($batchData->members as $indMedia)
  {
    if($indMedia->CHK == "false")
    {
      $r_val['RSLT'] = "1";
      $r_val['MSSG'] = "Incomplete batch found.";
    }
    else
    {
      $rawData = getLastRecord($indMedia->ID);
      if($rawData['RSLT'] == "1")
      {
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = "Unable to get last record for $indMedia->ID";
      }
      else
      {
        $dataRecord = $rawData['DATA'];
        $indMedia->BMN = $dataRecord['0']->batch_num;
      }
    }
  }
  $rawData = checkBatchIn($batchData);
  if($rawData['RSLT'] == "1")
  {
    $r_val['RSLT'] = "1";
    $r_val['MSSG'] = "Unable to check batch in.";
    $r_val['DATA'] = $batchData->batchID;
  }
  else
  {
    $r_val['RSLT'] = "0";
    $r_val['MSSG'] = "Batch checked in.";
    $r_val['DATA'] = $batchData->batchID;
  }
  return $r_val;
}

/* This function accepts an object that is generated from an assigned location and then a scanned
 * bar code.  It then adds a user name property to the object.  It checks to see if the media is found
 * in the database.  If the media is found, it retrieves the last record for the media from the database.
 * It then checks to see if the location being assigned is the same as found in the last record.  It also
 * checks to see if the assigned location is one of the exceptions.
 */
function procIndMedia($mediaData)
{
  $tempData = array();
  $r_val = array();
  $mediaData->uName = $_SESSION['UserName'];
  $tempData = tapeExists(trim($mediaData->mediaID)); //Check to see if the tape exists.
  if($tempData['RSLT'] == "0") 
  {
    $tempData = getIDLabel('locations', $mediaData->locID);
    $mediaData->locName = $tempData['DATA'];
    error_log(print_r($mediaData, true));
    $tempData = getLastRecord(trim($mediaData->mediaID)); //Get the last record for the media.
    if($tempData['RSLT'] == "0") // If the record is found.
    {
      error_log(print_r($tempData,true));
      if($tempData['DATA']['0']->location == $mediaData->locID) // Is current location the same as new assignment?
      {
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = "$mediaData->mediaID already assigned to selected location.";
      }
      elseif(($tempData['DATA']['0']->batch_id == $GLOBALS['destroyTapeException']['batchID']) && ($_SESSION['AccessLevel'] >= $GLOBALS['adminLevel']))
      {
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = "$mediaData->mediaID assigned to DESTROYED location.  Contact the system administrator to have the media assigned to a different location.";
      }
      elseif($mediaData->locName == $GLOBALS['newTapeLocation'])
      {
        $rawData = assignTape($mediaData->mediaID, $mediaData->locID, $mediaData->uName, $GLOBALS['recycleTapeException']['batchID'], $GLOBALS['recycleTapeException']['batchCount']);
        if($rawData['RSLT'] == "1")
        {
          $r_val['RSLT'] = "1";
          $r_val['MSSG'] = "Unable to recycle $mediaData->mediaID.  Contact system administrator.";
        }
        else
        {
          $r_val['RSLT'] = "0";
          $r_val['MSSG'] = "Successfully recycled $mediaData->mediaID";
        }
      }
      else
      {
        $rawData = assignTape($mediaData->mediaID, $mediaData->locID, $mediaData->uName, $tempData['DATA']['0']->batch_id, $tempData['DATA']['0']->batch_num);
        if($rawData['RSLT'] == "1")
        {
          $r_val['RSLT'] = "1";
          $r_val['MSSG'] = "Unable to assign $mediaData->mediaID to $mediaData->locName.  Contact system administrator.";
        }
        else
        {
          $r_val['RSLT'] = "0";
          $r_val['MSSG'] = "Assigned $mediaData->mediaID to $mediaData->locName.";
        }
      }
    }
    else
    {
      $r_val['RSLT'] = $tempData['RSLT'];
      $r_val['MSSG'] = $tempData['MSSG'];
      $r_val['DATA'] = $mediaData->mediaID;
    }
  }
  else
  {
    $r_val['RSLT'] = $tempData['RSLT'];
    $r_val['MSSG'] = $tempData['MSSG'];
    $r_val['DATA'] = $mediaData->mediaID;
  }
  return $r_val;
}
?>
