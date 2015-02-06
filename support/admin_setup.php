#!/usr/bin/php
<?php
/* This is a shell script that permits a user to set up the default administrator account with a local
 * password.  This account should exist and have a strong password set.  This account, even if it isn't
 * used should be left as a local failsafe account just in case.  You are free to remove it if you so desire
 * but we recommend you leave it.  In any case, you must run this script to set up the account so the
 * rest of the system can be set up beyond the defaults.
 */
require_once '../includes/functions.php';

$tempVar = addUser('Administrative', 'User', '8', 'admin', '-CHOOSE_A_STRONG_PASSWORD-' );

echo 'Result: ' . $tempVar['RSLT'] . "\r\n";
echo 'Message: ' . $tempVar['MSSG'] . "\r\n";
echo 'UID: ' . $tempVar['DATA'] . "\r\n";

?>
