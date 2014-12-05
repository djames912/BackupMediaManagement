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
         $dbLink = dbconnect();
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
?>
