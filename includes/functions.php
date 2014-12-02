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
    if(!is_object($newTape))
    {
      $r_val['RSLT'] = "1";
      $r_val['MSSG'] = "Data passed is not an object";
    }
    else
    {
      if(!($newTape->date && $newTape->tape_id && $newTape->loc_id &&$newTape->uname && $newTape->mtype && $newTape->ven_id && $newTape->po_num))
      {
        $r_val['RSLT'] = "1";
        $r_val['MSSG'] = "Incomplete data set passed.";
      }
      else
      {
        try
        {
          $dbLink = dbconnect();
          $bldQuery = "SELECT * FROM tapes WHERE label='$newTape->tape_id';";
          $statement = $dbLink->prepare($bldQuery);
          $statement->execute();
          $rowCount = $statement->rowCount();
          if($rowCount >= '1')
          {
            $r_val['RSLT'] = "1";
            $r_val['MSSG'] = "Record for $newTape->tape_id already exists in database.";
          }
          else
          {
            try
            {
              $dbLink = dbconnect();
              $bldQuery = "INSERT INTO tapes(label, mtype, vendor, po_num) VALUES('$newTape->tape_id','$newTape->mtype','$newTape->ven_id','$newTape->po_num');";
              $statement = $dbLink->prepare($bldQuery);
              $statement->execute();
              
              $tapeHistory = assignTape($newTape->tape_id, $newTape->loc_id, $newTape->uname, "-1", "-1");
              
            } 
            catch (PDOException $exception) 
            {
              echo "Unable to take requested action.";
              $r_val['RSLT'] = "1";
              $r_val['MSSG'] = $exception->getMessage();
            }
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
 
 /* This function accepts a media bar code number, a location ID, user name, batch ID and batch
  * count.  It then updates the history table for the tapes in question.  This function generates its
  * own time stamp to ensure that the insert record is accurate as to exactly when a record was
  * inserted.  It returns whether or not the insert was successful.
  */
 function assignTape($tapeBarCode, $locationID, $userName, $batchID, $batchCount)
 {
   
 }
 
 /* This function accepts three arguments, a database field name, column name and value.  The function
  * searches the database to see if the value exists in the database and returns a '0' if not found and a
  * '1' if found.
  */
?>
