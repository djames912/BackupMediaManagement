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
 * It returns a properly formatted list of user names.
 */
function generateUserList()
{
  $rawOutput = getTableContents('users', 'uname');
  return json_encode($rawOutput['DATA']);
}
?>
