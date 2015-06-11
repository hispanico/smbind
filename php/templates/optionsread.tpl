
<form name="form1" method="post" action="./options.php">
<font face="Arial,Helvetica" size="-0"><strong>Record Types</strong></font>
<table width="100%"  border="0" cellspacing="1">
	{section name=x loop=$records}
	    <tr>
		    {section name=y loop=$records[x]}
		<td align="right">
		    <input type="checkbox" name={$records[x][y].prefkey} {if $records[x][y].prefval == "on"}checked{/if}>
		</td>
		<td align="left">
		   <font face="Arial,Helvetica" size="-1">
		   {$records[x][y].prefkey}
		   </font>
		</td>
		{/section}
	    </tr>
	{/section}
</table>
<table>
{section name=prefkey loop=$options}
<tr>
<td align="left"><font face="Arial,Helvetica" size="-0"><strong>
{if $options[prefkey].prefkey == "510_hostmaster"}Site Hostmaster Address{/if}
{if $options[prefkey].prefkey == "500_prins"}Default Primary NS{/if}
{if $options[prefkey].prefkey == "501_secns"}Default Secondary NS{/if}
{if $options[prefkey].prefkey == "502_terns"}Default Tertiary NS{/if}
{if $options[prefkey].prefkey == "509_nsttl"}TTL for default NS records{/if}
{if $options[prefkey].prefkey == "650_range"}Items Per Page{/if}
</strong></font></td>
	<td align="left"><input type="text" size="35" name="{$options[prefkey].prefkey}" value="{$options[prefkey].prefval}"></td>
</tr>
{/section}
</table>
<br><br><center><input type="submit" value="Save"></center><br>
</form>
