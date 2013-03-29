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
?>
