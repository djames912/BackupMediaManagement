<?php

/*
 * This file contains all the major functions used by this application.
 */

/*
 * This function creates a connection to the database and returns a connection
 * resource.  It accepts no arguments.
 */
function dbconnect()
{
  try
  {
    $connection = "mysql:host=" . $dbHost . "; dbname=" . $dbName;
    $dbcon = new PDO($connection, $dbUser, $dbPass, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
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
