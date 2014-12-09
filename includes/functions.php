<?php

/*
 * This file contains all the major functions used by this application.
 */
require_once 'configure.php';
require_once 'configure.php.local';
require_once 'dbconfig.php.local';

/* This function accepts a KERBEROS or NTLM user name, strips off the unecessary
 * characters that are passed in and returns just the user name.  If the user name
 * is already clean, it won't be manipulated.  Originally written by Logan Barnes.
 */
function cleanUserName($uname)
{
  $tmpName = explode('@', $uname);
  $uname = $tmpName[0];
  return $uname;
}

/* This function accepts a user name and returns the UID, the password hash and the
 * access level assigned to the user.
 */
function checkLocalAuth($userName)
{
  if(isset($userName))
  {
    try
    {
      $bldQuery = "SELECT uname, password, access from users WHERE uname='$userName';";
      $dbLink = dbconnect();
      $statement = $dbLink->prepare($bldQuery);
      $statement->execute();
      $result = $statement->fetchObject();
      if(!$result)
      {
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = "No user data found.";
      }
      else
      {
        $r_val['RSLT'] = "0";
        $r_val['MSSG'] = "User data found.";
        $r_val['DATA'] = $result;
      }
    }
    catch(PDOException $exception)
    {
      echo "Unable to retrieve requested data.  Sorry";
      $r_val['RSLT'] = "1";
      $r_val['MSSG'] = $exception->getMessage();
    }
  }
  else
  {
    $r_val['RSLT'] = "1";
    $r_val['MSSG'] = "Insufficient data passed.";
  }
  return $r_val;
}

/* This function accepts user data as arguments, inserts them into the database and
 * then returns whether or not the insert was successful.  The optional arguments are
 * the user name and the password.  If the user name is left NULL then a suitable user
 * name is generated using the first name and first character of the last name.  If
 * the password is left null then that means that an external authentication system is
 * being used (e.g. kerberos, NTLM) and the password doesn't need to be set locally.  If
 * the password has a value it is hashed and inserted into the database.
 */
function addUser($userGivenName, $userSurname, $accessLevel, $uname = NULL, $password = NULL)
{
  $unameProblem = 1;
  if(is_null($password))
    $password = "EXT AUTH MECH USED";
  else
    $password = crypt($password, 69);
  
  if(is_null($uname))
  {
    $unameLowerFirst = strtolower($userGivenName);
    $unameLowerLast = strtolower($userSurname);
    $unameLastInit = substr($unameLowerLast, 0, 1);
    $uname = $unameLowerFirst . $unameLastInit;
  }
  
  $checkUname = checkUserNameExists($uname);
  if($checkUname['RSLT'] == "0")
    $unameProblem = 1;
  else
    $unameProblem = 0;
  
  if(!(isset($uname) || $userGivenName || $userSurname || $accessLevel))
  {
    $r_val['RSLT'] = "1";
    $r_val['MSSG'] = "Incomplete data set passed.";
  }
  else
  {
    if(!$unameProblem)
    {
      try
      {
        $bldQuery = "INSERT INTO users(uname, givenname, surname, password, access) VALUES('$uname', '$userGivenName', '$userSurname', '$password', '$accessLevel');";
        $dbLink = dbconnect();
        $statement = $dbLink->prepare($bldQuery);
        $statement->execute();
        $r_val['RSLT'] = "0";
        $r_val['MSSG'] = "User successfully inserted into database.";
        $r_val['DATA'] = $dbLink->lastInsertId();
      }
      catch(PDOException $exception)
      {
        echo "Unable to insert the requested data into the database.  Sorry.";
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = $exception->getMessage();
      }
    }
    else
    {
      $r_val['RSLT'] = "1";
      $r_val['MSSG'] = "User name already present in database.";
    }
  }
  return $r_val;
}

/* This function accepts a user name as an argument and returns whether or not that
 * username is already in the database.
 */
function checkUserNameExists($uname)
{
  $rowCount = 0;
  if(!isset($uname))
  {
    $r_val['RSLT'] = "1";
    $r_val['MSSG'] = "No user name passed.";
  }
  else
  {
    try
    {
      $bldQuery = "SELECT uname FROM users WHERE uname='$uname';";
      $dbLink = dbconnect();
      $statement = $dbLink->prepare($bldQuery);
      $statement->execute();
      $rowCount = $statement->rowCount();
      if($rowCount)
      {
        $r_val['RSLT'] = "0";
        $r_val['MSSG'] = "User name found in database.";
        $r_val['DATA'] = $rowCount;
      }
      else
      {
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = "User name not found in database.";
      }
    }
    catch(PDOException $exception)
    {
      echo "Unable to retrieve the requested data.  Sorry.";
      $r_val['RSLT'] = "1";
      $r_val['MSSG'] = $exception->getMessage();
    }
  }
  return $r_val;
}

/* This is a generic function that retrieves the entire contents of a table.  It
 * accepts as it's only argument the name of the table that data needs to be gathered
 * from.  It returns the data as an associative containing individual objects that
 * contain the requested data.
 */
function getTableContents($tableName, $colName = NULL)
{
  if(isset($tableName))
  {
    try
    {
      $dbLink = dbconnect();
      if(is_null($colName))
      {
        $bldQuery = "SELECT * FROM $tableName;";
      }
      else
      {
        $bldQuery = "SELECT $colName FROM $tableName;";
      }
      $statement = $dbLink->prepare($bldQuery);
      $statement->execute();
      $r_val['RSLT'] = "0";
      $r_val['MSSG'] = "Data located in $tableName";
      $r_val['DATA'] = $statement->fetchAll(PDO::FETCH_OBJ);
    }
    catch(PDOException $exception)
    {
      echo "Unable to retrieve requested data.";
      $r_val['RSLT'] =  "1";
      $r_val['MSSG'] = $exception->getMessage();
    }
  }
  else
  {
    $r_val['RSLT'] = "1";
    $r_val['MSSG'] = "Incomplete data set passed to function.";
  }
  return $r_val;
}

/* This function accept three arguments: the table name, the column name and the data
 * to be inserted into the table and column.  It checks the database to see if the
 * information is already present, if it is then it takes no action, if it doesn't
 * then the data is inserted into the database.  It returns what specific action,
 * or lack thereof, was taken and the record number of the value inserted if new data
 * was actually inserted.
 */
function addType($tableName, $fieldName, $dataValue)
{
  if(!(isset($tableName) || isset($fieldName) || isset($dataValue)))
  {
    $r_val['RSLT'] = "1";
    $r_val['MSSG'] = "Incomplete data set passed.";
  }
  else
  {
    if($tableName == 'locations')
      $targetValue = 'location';
    if($tableName == 'mtype')
      $targetValue = 'media type';
    if($tableName == 'vendors')
      $targetValue = 'vendor';
    try
    {
      $dbLink = dbconnect();
      $bldQuery = "SELECT * FROM $tableName WHERE $fieldName='$dataValue';";
      $statement = $dbLink->prepare($bldQuery);
      $statement->execute();
      $rowCount = $statement->rowCount();
      if($rowCount >= '1')
      {
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = "Record already exists for $targetValue: $dataValue";
      }
      else
      {
        $bldQuery = "INSERT INTO $tableName($fieldName) VALUES('$dataValue')";
        $statement = $dbLink->prepare($bldQuery);
        $statement->execute();
        $r_val['RSLT'] = "0";
        $r_val['MSSG'] = "Inserted $targetValue $dataValue into the database.";
        $r_val['DATA'] = $dbLink->lastInsertId();
      }
    }
    catch(PDOException $exception)
    {
      echo "Unable to take requested action.";
      $r_val['RSLT'] =  "1";
      $r_val['MSSG'] = $exception->getMessage();
    }
  }
  return $r_val;
}

/* This function accepts a tape object as an argument, it then breaks the object into its individual
 * properties and inserts them into the appropriate database tables and fields.  The function
 * checks to see if the tape already exists in the database.  If it does, it throws the appropriate
 * error message.  If it does not, it does some basic sanity checking and then inserts the tape into
 * the database.  The function returns whether the insert was successful or not, or if the tape was
 * already found and is a duplicate.\
*/
 function addTape($newTape)
 {
   $returnedData = getLableID('locations', $GLOBALS['newTapeLocation']);
   $locationID = $returnedData['DATA'];
    if(!is_object($newTape))
    {
      $r_val['RSLT'] = "1";
      $r_val['MSSG'] = "Data passed is not an object";
    }
    else
    {
      if(!($newTape->tapeID && $newTape->uname && $newTape->venID && $newTape->poNum))
      {
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = "Incomplete data set passed.";
      }
      else
      {
        $checkTape = tapeExists($newTape->tapeID);
        if($checkTape['RSLT'] == "0")
        {
          $r_val['RSLT'] = "1";
          $r_val['MSSG'] = "Tape $newTape->tapeID found in database.  Cannot add.";
        }
        else
        {
          try
          {
            $returnedMediaType = getMediaType($newTape->tapeID);
            $mediaType = $returnedMediaType['DATA'];
            $dbLink = dbconnect();
            $bldQuery = "INSERT INTO tapes(label, mtype, vendor, po_num) VALUES('$newTape->tapeID','$mediaType','$newTape->venID','$newTape->poNum');";
            $statement = $dbLink->prepare($bldQuery);
            $statement->execute();
            $tapeHistory = assignTape($newTape->tapeID, $locationID, $newTape->uname, $GLOBALS['newTapeException']['batchID'], $GLOBALS['newTapeException']['batchCount']);
            if($tapeHistory['RSLT'] == "1")
            {
               $r_val['RSLT'] = $tapeHistory['RSLT'];
               $r_val['MSSG'] = $tapeHistory['MSSG'];
            }
            else
            {
               $r_val['RSLT'] = "0";
               $r_val['MSSG'] = "$newTape->tapeID successfully added to the database.";
            }
          } 
          catch (PDOException $exception) 
          {
            echo "Unable to take requested action.";
            $r_val['RSLT'] = "1";
            $r_val['MSSG'] = $exception->getMessage();
          }
        }
      }
    }
   return $r_val;
 }
 
 /* This function accepts a media bar code number, a location ID, user name, batch ID and batch
  * count.  It then updates the history table for the tapes in question.  This function generates its
  * own time stamp to ensure that the insert record is accurate as to exactly when a record was
  * inserted.  It returns whether or not the insert was successful.
  */
 function assignTape($tapeBarCode, $locationID, $userName, $batchID, $batchCount)
 {
    $timeStamp = time();
    try
    {
       $dbLink = dbconnect();
       $bldQuery = "INSERT INTO history(date, tape_id, location, user, batch_id, batch_num) VALUES('$timeStamp', '$tapeBarCode', '$locationID', '$userName', '$batchID', '$batchCount');";
       $statement = $dbLink->prepare($bldQuery);
       $statement->execute();
       $r_val['RSLT'] = "0";
       $r_val['MSSG'] = "Database history insert for $tapeBarCode successful.";
    }
    catch (PDOException $exception) 
    {
       echo "Unable to take requested action.";
       $r_val['RSLT'] = "1";
       $r_val['MSSG'] = $exception->getMessage();
    }
    return $r_val;
 }
 
 /* This function accepts a tape bar code as an argument, checks the database to see if the tape is already
  * present or not.  If the tape is found, it returns a '0'.  If the tape is not found it returns a '1';
  */
 function tapeExists($tapeBarCode)
 {
   try
   {
     $dbLink = dbconnect();
     $bldQuery = "SELECT * FROM tapes WHERE label='$tapeBarCode';";
     $statement = $dbLink->prepare($bldQuery);
     $statement->execute();
     $rowCount = $statement->rowCount();
      if($rowCount >= '1')
     {
       $r_val['RSLT'] = "0";
       $r_val['MSSG'] = "Record for $tapeBarCode found in database.";
     }
     else
     {
       $r_val['RSLT'] = "1";
       $r_val['MSSG'] = "Record for $tapeBarCode not found in database.";
     }
   }
   catch (PDLException $exception)
   {
     echo "Unable to take requested action.";
     $r_val['RSLT'] = "1";
     $r_val['MSSG'] = $exception->getMessage();
   }
   return $r_val;
 }
 
 /* This function accepts a database table name and then the ID number that needs to have it label name
  * found.  It checks various values against some of the $GLOBALS that are set in configuration files to be
  * sure the requested value isn't one of them.  If the value is not one of the $GLOBALS, it goes to the
  * database table passed in to the function to look for the ID.  If the function finds the ID it will return the
  * label belonging to that ID.  If the function does not find the ID, it returns that fact.
  */
 function getIDLabel($tableName, $nameID)
 {
   if($nameID == $GLOBALS['newTapeException']['batchID'])
   {
     $r_val['RSLT'] = "0";
     $r_val['MSSG'] = "ID located as default variable.";
     $r_val['DATA'] = $GLOBALS['newTapeException']['name'];
   }
   elseif($nameID == $GLOBALS['recycleTapeException']['batchID'])
   {
     $r_val['RSLT'] = "0";
     $r_val['MSSG'] = "ID located as default variable";
     $r_val['DATA'] = $GLOBALS['recycleTapeException']['name'];
   }
   elseif($nameID == $GLOBALS['destroyTapeException']['batchID'])
   {
     $r_val['RSLT'] = "0";
     $r_val['MSSG'] = "ID located as default variable.";
     $r_val['DATA'] = $GLOBALS['destroyTapeException']['name'];
   }
   else
   {
     $cName = $tableName == "vendors" ? "v_name" : "label";
     try
     {
       $dbLink = dbconnect();
       $bldQuery = "SELECT $cName FROM $tableName WHERE ID='$nameID';";
       $statement = $dbLink->prepare($bldQuery);
       $statement->execute();
       $rowCount = $statement->rowCount();
       if($rowCount == '0')
       {
         $r_val['RSLT'] = "1";
         $r_val['MSSG'] = "ID $nameID not found in $tableName";
       }
       else
       {
         $r_val['RSLT'] = "0";
         $r_val['MSSG'] = "ID located in $tableName";
         $returnedData = $statement->fetchAll(PDO::FETCH_OBJ);
         $r_val['DATA'] = $returnedData['0']->$cName;
       }
     }
     catch(PDOException $exception)
     {
       echo "Unable to take requested action";
       $r_val['RSLT'] = "1";
       $r_val['MSSG'] = $exception->getMessage();
     }
   }
   return $r_val;
 }
 
 /* This function accepts a database table name and then a label name.  It returns the ID number associated
  * with that label.
  */
 function getLableID($tableName, $labelName)
 {
   if($labelName == $GLOBALS['newTapeException']['name'])
   {
     $r_val['RSLT'] = "0";
     $r_val['MSSG'] = "Name located as default variable.";
     $r_val['DATA'] = $GLOBALS['newTapeException']['batchID'];
   }
   elseif($labelName == $GLOBALS['recycleTapeException']['name'])
   {
     $r_val['RSLT'] = "0";
     $r_val['MSSG'] = "Name located as default variable.";
     $r_val['DATA'] = $GLOBALS['recycleTapeException']['batchID'];
   }
   elseif($labelName == $GLOBALS['destroyTapeException']['name'])
   {
     $r_val['RSLT'] = "0";
     $r_val['MSSG'] = "Name located as default variable.";
     $r_val['DATA'] = $GLOBALS['destroyTapeException']['name'];
   }
   else
   {
     $searchName = $tableName == "vendors" ? "v_name" : "label";
     try
     {
       $dbLink = dbconnect();
       $bldQuery = "SELECT ID FROM $tableName WHERE $searchName='$labelName';";
       $statement = $dbLink->prepare($bldQuery);
       $statement->execute();
       $rowCount = $statement->rowCount();
       if($rowCount == "0")
       {
         $r_val['RSLT'] = "1";
         $r_val['MSSG'] = "ID for $labelName not found in database.";
       }
       else
       {
         $r_val['RSLT'] = "0";
         $r_val['MSSG'] = "Label located in $tableName";
         $returnedData = $statement->fetchAll(PDO::FETCH_OBJ);
         $r_val['DATA'] = $returnedData['0']->ID;
       }
     }
     catch (PDOException $exception) 
     {
       echo "Unable to take requested action.";
       $r_val['RSLT'] = "1";
       $r_val['MSSG'] = $exception->getMessage();
     }
   }
   return $r_val;
 }
 
 function getMediaType($mediaBarCode = NULL)
 {
   if(is_null($mediaBarCode))
   {
     $r_val['RSLT'] = "1";
     $r_val['MSSG'] = "No data passed to determineMediaType().";
   }
   else
   {
     $mediaNum = substr($mediaBarCode, strlen($mediaBarCode) - 1);
     if(!is_numeric($mediaNum))
     {
       $r_val['RSLT'] = "1";
       $r_val['MSSG'] = "Non-numeric value returned.  Unknown media type detected.";
     }
     else
     {
       $mediaLabel = "LTO" . $mediaNum;
       try
       {
         $dbLink = dbconnect();$dbLink->lastInsertId();
         $bldQuery = "SELECT ID FROM mtype WHERE label='$mediaLabel';";
         $statement = $dbLink->prepare($bldQuery);
         $statement->execute();
         $rowCount = $statement->rowCount();
         if($rowCount == "0")
         {
           $r_val['RSLT'] = "1";
           $r_val['MSSG'] = "Unknown media type for $mediaBarCode";
         }
         else
         {
           $returnedData = $statement->fetchAll(PDO::FETCH_OBJ);
           $r_val['RSLT'] = "0";
           $r_val['MSSG'] = "Media type found in database";
           $r_val['DATA'] = $returnedData['0']->ID;
         }
       } 
       catch (PDOException $exception)
       {
         echo "Unable to take requested action.";
         $r_val['RSLT'] = "1";
         $r_val['MSSG'] = $exception->getMessage();
       }
     }
   }
   return $r_val;
 }
 
 /* This function accepts a string and a number of characters as arguments.  The function truncates the
  * string to match the number of characters provided.  The function returns the truncated string.
  */
 function truncateString($textString, $numberChars)
 {
   if(strlen($textString) > $numberChars)
   {
     $r_val['DATA'] = substr($textString, 0, $numberChars);
   }
   else
   {
     $r_val['DATA'] = $textString;
   }
   return $r_val;
 }
 
 /* This function accepts an abbreviated month name, converts it to a digit and then returns the digit.
  * There is some intermeidate processing which is fairly rudimentary but it works reliably.
  */
 function month2digit($monthAbbrev)
 {
   $monthLower = strtolower($monthAbbrev);
   $monthCorrected = ucfirst($monthLower);
   for($monthNum = 1; $monthNum <= 12; $monthNum++)
   {
     if(date("M", mktime(0, 0, 0, $monthNum, 1, 0)) == $monthCorrected)
     {
       $r_val['DATA'] = $monthNum;
     }
   }
   return $r_val;
 }
 
 /* This function accepts an optional number of days as an argument.  If no argument is passed, it uses the
  * configured $defaultReturnTime.  The function returns the future timestamp.
  */
 function getReturnDate($advanceDate = NULL)
 {
   if(is_null($advanceDate))
   {
     $advanceDate = $GLOBALS['defaultReturnTime'];
   }
   $currentDate = getdate(time());
   $currentDate['mday'] = $currentDate['mday'] + $advanceDate;
   $timeStamp = mktime($currentDate['hours'], $currentDate['minutes'], $currentDate['seconds'], $currentDate['mon'], $currentDate['mday'], $currentDate['year']);
   $r_val['DATA'] =$timeStamp;
 
   return $r_val;
 }
 
 /* This function accepts a formatted date (e.g. m/d/Y or M/d/Y) and uses it to build a time stamp.  The
  * time stamp it creates is used to create a batch time stamp for entry into the database.  It returns the
  * created time stamp to the calling function.  The function tests the month element of the array to see
  * if it is numeric, if it isn't it converts it, if it is, it uses it.  The other check is to see if the year is longer than
  * four digits, if it is it truncates it.  That may produce undesireable results if a random number is passed.
  */
 function createTimeStamp($suppliedDate)
 {
   $dateElements = explode("/", $suppliedDate);
   $batchDay = $dateElements['1'];
   if(is_numeric($dateElements['0']))
   {
     $batchMonth = $dateElements['0'];
   }
   else
   {
     $batchMonth = month2digit($dateElements['0']);
   }
   if(strlen($dateElements['2']) > 4)
   {
     $returnedData = truncateString($dateElements['2'], '4');
     $batchYear = $returnedData['DATA'];
   }
   else
   {
     $batchYear = $dateElements['2'];
   }
   $timeStamp = mktime(date("H"), date("i"), date("s"), $batchMonth, $batchDay, $batchYear);
   $r_val['DATA'] = $timeStamp;
   return $r_val;
 }
 
 /* This function accepts two arguments, the batch label name and the time stamp under which the batch
  * was created.  This function was created to prevent duplicate labels from being returned.  This function,
  * as it now stands, is useful only in being called by the batch generation function since a user will have
  * no real way of knowing exactly what time stamp belongs to a given batch.  It returns the batch ID number.
  */
 function getBatchID($labelName, $timeStamp)
 {
   try
   {
     $dbLink = dbconnect();
     $bldQuery = "SELECT ID FROM batch WHERE label='$labelName' AND date='$timeStamp';";
     $statement = $dbLink->prepare($bldQuery);
     $statement->execute();
     $returnedData = $statement->fetchAll(PDO::FETCH_OBJ);
     $r_val['RSLT'] = "0";
     $r_val['MSSG'] = "ID for batch $labelName with timestamp $timeStamp located.";
     $r_val['DATA'] = $returnedData['0']->ID;
   }
   catch (PDOException $exception) 
   {
     echo "Unable to take requested action.";
     $r_val['RSLT'] = "1";
     $r_val['MSSG'] = $exception->getMessage();
   }
   return $r_val;
 }
 
 /* This function accepts an object containing information about a batch of tapes.  The first element in the
  * object should be an array of media bar codes.  The other elements should be the user name of the user
  * who created the batch and a date.  If the date element is not found, a time stamp is created.  If a date
  * is found, the date is run through createTimeStamp().  Other values are set using $GLOBALS variables for
  * the default storage location and the default return time.  The batch information is then inserted into
  * the batch table and a history table entry is made for each tape in the batch reflecting that fact.  The
  * function returns whether the history insert was successful or not.
  */
 function addTapeBatch($batchData)
 {
   $barCodes = $batchData->bcodes;
   $userName = $batchData->uname;
   $batchDate = $batchData->date;
   $rtnDate = $batchData->rdays;
   $batchLoc = $batchData->bloc;
   
   // This block checks to see if the batch date is set, if it isn't then a value
   // is set.  If it is a valid time stamp is created.
   if(!$batchDate)
   {
     $batchTimeStamp = time();
     $batchTime = date("m/d/Y", time());
   }
   else
   {
     $returnedData = createTimeStamp($batchDate);
     $batchTimeStamp = $returnedData['DATA'];
     $batchTime = $batchDate;
   }
   
   // This block checks to see if the batch return date is set.  If it isn't then
   // the default from the configuration file is used.  If it is, it is sent over
   // to get a valid time stamp which is then used.
   if(!$rtnDate)
   {
     $returnedData = getReturnDate($GLOBALS['defaultReturnTime']);
     $returnDate = $returnedData['DATA'];
   }
   else
   {
     $returnedData = getReturnDate($rtnDate);
     $returnDate = $returnedData['DATA'];
   }
   // This block checks to see if the batch location is set.  If it isn't it goes
   // with the default location.  If it is, it checks to see if it's a numeric value
   // and then verifies the value with the database.  If the value is valid, it sets
   // that as the location ID.  If not, it sets the default.  If the value is not
   // numeric, it checks with the locations named in the database, if the location
   // returns a valid location ID it sets it, otherwise it goes with the default.
   if(!$batchLoc)
   {
     $returnedData = getLableID('locations', $GLOBALS['batchCreateLocation']);
     $locationID = $returnedData['DATA'];
   }
   else
   {
     if(is_numeric($batchLoc))
     {
       $returnedData = getIDLabel('locations', $batchLoc);
       if($returnedData['RSLT'] == "1")
       {
         error_log("$batchLoc not valid.  Setting default.", 0);
         $failSafe = getLableID('locations', $GLOBALS['batchCreateLocation']);
         $locationID = $failSafe['DATA'];
       }
       else
       {
         $locationID = $batchLoc;
       }
     }
     else
     {
       $returnedData = getLableID('locations', $batchLoc);
       if($returnedData['RSLT'] == "1")
       {
         error_log("$batchLoc not valid.  Setting default.", 0);
         $failSafe = getLableID('locations', $GLOBALS['batchCreateLocation']);
         $locationID = $failSafe['DATA'];
       }
       else
       {
         $locationID = $returnedData['DATA'];
       }
     }
   }
   
   // This block checks to see if the media bar codes came over as an array.  If not
   // an error message is thrown and action halts.  If so, the array is looped through
   // and the batch is inserted into the database.  A corresponding entry in the history
   // table is then made for each tape associating it with the batch that was just
   // created.
   if(!is_array($barCodes))
   {
     $r_val['RSLT'] = "1";
     $r_val['MSSG'] = "The batch members were not passed as an array.";
   }
   else
   {
     $batchSize = count($barCodes);
     try
     {
       $dbLink = dbconnect();
       $bldQuery = "INSERT INTO batch(label, total, date, rdate) VALUES('$batchTime', '$batchSize', '$batchTimeStamp', '$returnDate');";
       $statement = $dbLink->prepare($bldQuery);
       $statement->execute();
       // This should return the batch ID number.  No sense in making another database call to get what should already be available.
       $batchID = $dbLink->lastInsertId();
       $loop = 0;
       foreach($barCodes as $tapeID)
       {
         $loop++;
         $historyUpdate [] = assignTape(trim($tapeID), $locationID, $userName, $batchID, $loop);
         $r_val['DATA'] = $historyUpdate;
       }
       $r_val['RSLT'] = "0";
       $r_val['MSSG'] = "Batch created successfully.";
     } 
     catch (PDOException $exception) 
     {
       echo "Unable to take requested action.";
       $r_val['RSLT'] = "1";
       $r_val['MSSG'] = $exception->getMessage();
     }
   }
   return $r_val;
 }
 
 /* This function accepts a batch ID number as an argument, it returns the size of
  * the batch.
  */
 function getBatchSize($batchID)
 {
   if(!is_numeric($batchID))
   {
     $r_val['RSLT'] = "1";
     $r_val['MSSG'] = "Non-numeric value passed.";
   }
   else
   {
     try
     {
       $dbLink = dbconnect();
       $bldQuery = "SELECT total FROM batch WHERE ID='$batchID';";
       $statement = $dbLink->prepare($bldQuery);
       $statement->execute();
       $rowCount = $statement->rowCount();
       if($rowCount == "0")
       {
         $r_val['RSLT'] = "1";
         $r_val['MSSG'] = "No batch with ID $batchID found.";
       }
       else
       {
         $returnedData = $statement->fetchAll(PDO::FETCH_OBJ);
         $r_val['RSLT'] = "0";
         $r_val['MSSG'] = "Lookup for batch ID $batchID complete.";
         $r_val['DATA'] = $returnedData['0']->total;
       }
     }
     catch(PDOException $exception)
     {
       echo "Unable to take requested action.";
       $r_val['RSLT'] = "1";
       $r_val['MSSG'] = $exception->getMessage();
     }
   }
   return $r_val;
 }
 
 /* This function accepts a media bar code as an argument and then builds an object tha provides all the
  * information on the media belonging to that bar code.  It returns an object with all available information
  * on that piece of media.
  */
 function getMediaDetail($mediaBarCode)
 {
   class mediaData
   {
     public $mediaLabel, $mediaType, $vendor, $poNum, $mediaHistory = array();
   }
     $checkMedia = tapeExists($mediaBarCode);
     If($checkMedia['RSLT'] == "1")
     {
       $r_val['RSLT'] = "1";
       $r_val['MSSG'] = "Media bar code $mediaBarCode not found.";
     }
     else
     { 
       try
       {
         $curMedia = new mediaData();
         $curMedia->mediaLabel = $mediaBarCode;
         $dbLink = dbconnect();
         $bldQuery = "SELECT * FROM tapes WHERE label='$curMedia->mediaLabel';";
         $statement = $dbLink->prepare($bldQuery);
         $statement->execute();
         $returnedData = $statement->fetchAll(PDO::FETCH_OBJ);
         $mediatypeReturned = getIDLabel('mtype',$returnedData['0']->mtype);
         $vendorReturned = getIDLabel('vendors', $returnedData['0']->vendor);
         $curMedia->poNum = $returnedData['0']->po_num;
         $curMedia->mediaType = $mediatypeReturned['DATA'];
         $curMedia->vendor = $vendorReturned['DATA'];
         $returnedHistory = getMediaHistory($curMedia->mediaLabel);
         $curMedia->mediaHistory = $returnedHistory['DATA'];
         $r_val['RSLT'] = "0";
         $r_val['MSSG'] = "Media detail lookup for $mediaBarCode complete.";
         $r_val['DATA'] = $curMedia;
       } 
       catch (PDOException $exception) 
       {
         echo "Unable to take requested action.";
         $r_val['RSLT'] = "1";
         $r_val['MSSG'] = $exception->getMessage();
       }
     }
   return $r_val;
 }
 
 /* this function accepts a media bar code as an argument and then builds an array that provides all the
  * entries from the history table associated with the media bar code supplied.  It also loops through the
  * data returned from the database and converts it to a more user friendly format and stores that in the
  * array.  It returns the processed array.  This function is meant to be a helper function to getMediaDetail().
  */
 function getMediaHistory($mediaBarCode)
 {
   $loop =0;
   $mediaHistory = array();
   
   $checkMedia = tapeExists($mediaBarCode);
   if($checkMedia['RSLT'] == "1")
   {
     $r_val['RSTL'] = "1";
     $r_val['MSSG'] = "Media bar code $mediaBarCode not found.";
   }
   else
   {
     $dbLink = dbconnect();
     $bldQuery = "SELECT * FROM history WHERE tape_id='$mediaBarCode';";
     $statement = $dbLink->prepare($bldQuery);
     $statement->execute();
     $returnedData = $statement->fetchAll(PDO::FETCH_ASSOC);
     // This next code block converts many of the values from the form stored in the database to more
     // user friendly data.  I can't think of a reason not to do this here at this point.
     foreach($returnedData as $indHistory)
     {
       $returnedData[$loop]['date'] = date("D M j G:i:s T Y", $indHistory['date']);
       $locationData = getIDLabel('locations', $indHistory['location']);
       $returnedData[$loop]['location'] = $locationData['DATA'];
       $batchData = getIDLabel('batch', $indHistory['batch_id']);
       $returnedData[$loop]['batch_id'] = $batchData['DATA'];
       $loop++;
     }
     $r_val['RSLT'] = "0";
     $r_val['MSSG'] = "History for media $mediaBarCode located.";
     $r_val['DATA'] = $returnedData;
   }
   return $r_val;
 }
?>
