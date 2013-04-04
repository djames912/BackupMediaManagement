<?php

/*
 * This file contains all the major functions used by this application.
 */
require_once 'configure.php';
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
 * being used (e.g. kerberos, NTLM) and the password doesn't need to be set locally.
 */
function addUser($userGivenName, $userSurname, $accessLevel, $uname = NULL, $password = NULL)
{
  if(is_null($password))
    $password = "EXT AUTH MECH USED";
  
  if(is_null($uname))
  {
    $unameLowerFirst = strtolower($userGivenName);
    $unameLowerLast = strtolower($userSurname);
    $unameFirstInit = substr($unameLowerFirst, 0);
    $uname = $unameLowerFirst .+ $unameFirstInit;
  }
  
  if(!(isset($uname) || $userGivenName || $userSurname || $accessLevel))
  {
    $r_val['RSLT'] = "1";
    $r_val['MSSG'] = "Incomplete data set passed.";
  }
  else
  {
    try
    {
      $bldQuery = "INSERT INTO users(uname, givenname, surname, password, access) VALUES('$uname', '$userGivenName', '$userSurname', '$password', $accessLevel');";
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
  return $r_val;
}
?>
