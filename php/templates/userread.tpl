
<form name="form1" method="post" action="./user.php?i={$user.id}">
<table width="320"  border="0">
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Username:</strong></font></div></td>
    <td><font face="Arial,Helvetica" size="-1">{$user.username}</font></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Administrator:</strong></font></div></td>
    <td><font face="Arial,Helvetica" size="-1">{html_radios values=$admin_array checked=$user.admin output=$admin_array separator=" " name="admin"}</font></td>
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
<font size="-0" face="Arial,Helvetica"><strong>Zones owned by this user:</strong></font>
<table width="100%"  border="0" cellspacing="1">
  <tr>
    <td><font face="Arial,Helvetica" size="-1"><strong>Name</strong></font></td>
    <td><font face="Arial,Helvetica" size="-1"><strong>Serial</strong></font></td>
  </tr>
{section name=i loop=$zonelist}
  <tr>
    <td><font size="-1" face="Arial,Helvetica"><a class="class" href="./record.php?i={$zonelist[i].id}">{$zonelist[i].name}</a></font></td>
    <td><font size="-1" face="Arial,Helvetica">{$zonelist[i].serial}</font></td>
  </tr>
{/section}
</table>
