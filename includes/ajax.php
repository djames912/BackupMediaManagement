<?php
require_once 'functions.php';

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
?>
