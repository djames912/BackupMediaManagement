<?php

/*
 * This file contains an example setup of the dbconnect().  It is an example only
 * and should be copied into dbconfig.php.local and then have the appropriate
 * variables set to correctly communicate with the database.
 */

/*
 * This function creates a connection to the database and returns a connection
 * resource.  It accepts no arguments.
 */
function dbconnect()
{
  // Database credentials:
  $dbHost = "changeme";
  $dbName = "changeme";
  $dbUser = "changeme";
  $dbPass = "changeme";
  try
  {
    $connection = "mysql:host=" . $dbHost . "; dbname=" . $dbName;
    $dbcon = new PDO($connection, $dbUser, $dbPass, array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    return $dbcon;
  }
  catch (PDOException $exception)
  {
    echo "<p>Unable to connect to the database</p>";
    echo "<p>Please check your database installation/setup and try again.";
    $dbcon['RSLT'] = "1";
    $dbcon['MSSG'] = $exception->getMessage();
  }
}
?>
