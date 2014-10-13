
<br><br>
<form name="form1" method="post" action="./userlist.php">
<table width="320"  border="0">
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Username:</strong></font></div></td>
    <td><input type="text" name="username_one" class="a1"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Administrator:</strong></font></div></td>
    <td>{html_radios values=$admin_array checked="no" output=$admin_array separator=" " name="admin"}</td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>New password:</strong></font></div></td>
    <td><input type="password" name="password_one" class="a1"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Confirm new password:</strong></font></div></td>
    <td><input type="password" name="confirm_password" class="a1"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input name="Submit" type="submit" class="a" value="Save"></td>
  </tr>
</table>
</form>

