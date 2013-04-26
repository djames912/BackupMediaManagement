<?php

/*
 * *** DO NOT EDIT THIS FILE!!! ***
 * Any changes made in this file will be lost on future upgrades.  Any of these
 * values which are specified in the configure.php.local file will override
 * those contained in this file.
 */
$applicationName = "Backup Media Management";
$applicationRoot = '/BackupMediaManagement/';
$applicationInstance = "Development";

// Sets the default timezone:
date_default_timezone_set('GMT');

// These are default settings that can be changed in the configure.php.local file.
// Be sure the locations match what you have set up for locations in your database.
// If these work for you, then there is no need to change them.
$batchCreateLocation = "Offsite Storage";
$newTapeLocation = "Tape Library";

// This sets the default return time in the number of days.  This can also be changed
// to match your needs.
$defaultReturnTime = 35;

// Sets the default access levels for various pages in the system.  Assign your own
// levels in the configure.php.local file.
$adminLevel = 8;
$modTapeLevel = 6;
$addTapeLevel = 1;
$runReportLevel = 3;
$createBatchLevel = 2;
$modBatchLevel = 3;

// REMOVE THE FOLLOWING LINES FROM THE configure.php.local file:
include 'configure.php.local';
?>
