
<form name="form1" method="post" action="./zonelist.php">
<table width="320"  border="0">
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Zone:</strong></font></div></td>
    <td><input type="text" name="name" class="a1"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Refresh:</strong></font></div></td>
    <td><input type="text" name="refresh" class="a1" value="28800"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Retry:</strong></font></div></td>
    <td><input type="text" name="retry" class="a1" value="7200"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Expire:</strong></font></div></td>
    <td><input type="text" name="expire" class="a1" value="1209600"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Time To Live:</strong></font></div></td>
    <td><input type="text" name="ttl" class="a1" value="86400"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Primary NS: </strong></font></div></td>
    <td><input type="text" name="pri_dns" class="a1" value="{$pri_dns}"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Secondary NS:</strong></font></div></td>
    <td><input type="text" name="sec_dns" class="a1" value="{$sec_dns}"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Web Server IP:</strong></font></div></td>
    <td><input type="text" name="www" class="a1" value=""></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Mail Server IP:</strong></font></div></td>
    <td><input type="text" name="mail" class="a1" value=""></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>FTP Server IP:</strong></font></div></td>
    <td><input type="text" name="ftp" class="a1" value=""></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Owner: </strong></font></div></td>
    <td><select name="owner" class="a1">
	{section name=i loop=$userlist}
		<option value="{$userlist[i].id}" {if $current_user == $userlist[i].id}selected{/if}>{$userlist[i].username}</option>
	{/section}
	</select>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input name="Submit" type="submit" class="a" value="Add zone"></td>
  </tr>
</table>
</form>
