<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Backup Media Management - Login</title>
  </head>
  <?php
    require_once "includes/functions.php";
  ?>
  <body bgcolor="#000000" text="#FFFFFF" onload="document.form1.UserName.focus()">
    <table width="100%" border="0" cellspacing="0">
      <tr>
        <td bgcolor="#000000">
          <div align="center"><font size="4" color="#FFFFFF"><b><font face="Arial, Helvetica, sans-serif">
              <?php
                echo "$applicationName";
                if($applicationInstance != '' || $applicationInstance = "Production")
                  echo " - $applicationInstance";
              ?>
              </font></b></font></div>
        </td>
      </tr>
      <tr>
        <td>
          <div align="center"><font face="Arial, Helvetica, sans-serif" color="#FFFFFF">Login</font></div>
        </td>
      </tr>
    </table>
    <br>
    <form name="form1" method="post" action="main.php">
      <table width="75%" border="0" align="center">
        <tr>
          <td width="49%">
            <div align="right"><font face="Arial, Helvetica, sans-serif">User Name:</font></div>
          </td>
          <td width="51%">
            <input type="text" name="UserName" size="20">
          </td>
        </tr>
        <tr>
          <td width="49%">
            <div align="right"><font face="Arial, Helvetica, sans-serif">Password:</font></div>
          </td>
          <td width="51%">
            <input type="password" name="Password" size="20">
          </td>
        </tr>
      </table>
      <p align="center">
        <input type="submit" name="Login" value="Login">
      </p>
    </form>
    <p>&nbsp;</p>
  </body>
</html>
