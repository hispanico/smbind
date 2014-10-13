{include file="pages.tpl"}
<form name="form1" method="post" action="./record.php?i={$zone.id}">
<table border="0" cellpadding="0" cellspacing="3">
  <tr>
    <td>
<div align="right"><font face="Arial,Helvetica" size="-1"><strong>Zone:</strong></font></div></td>
    <td><font size="-1" face="Arial,Helvetica">{$zone.name}</font></td>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Serial:</strong></font></div></td>
    <td><font size="-1" face="Arial,Helvetica">{$zone.serial}</font></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Refresh:</strong></font></div></td>
    <td><input type="text" name="refresh" size="25" class="a1" value="{$zone.refresh}"></td>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Retry:</strong></font></div></td>
    <td><input type="text" name="retry" size="25" class="a1" value="{$zone.retry}"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Expire:</strong></font></div></td>
    <td><input type="text" name="expire" size="25" class="a1" value="{$zone.expire}"></td>
<td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>TTL:</strong></font></div></td>
    <td><input type="text" name="ttl" size="25" class="a1" value="{$zone.ttl}"></td>
  </tr>
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>NS1:</strong></font></div></td>
    <td><input type="text" name="pri_dns" size="25" class="a1" value="{$zone.pri_dns}"></td>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>NS2:</strong></font></div></td>
    <td><input type="text" name="sec_dns" size="25" class="a1" value="{$zone.sec_dns}"></td>
  </tr>
  {if $admin == "yes"}
  <tr>
    <td><div align="right"><font face="Arial,Helvetica" size="-1"><strong>Owner: </strong></font></div></td>
    <td><select name="owner" class="a1">
	{section name=i loop=$userlist}
		<option value="{$userlist[i].id}"{if $userlist[i].id == $zone.owner} selected{/if}>{$userlist[i].username}</option>
	{/section}
	</select>
	</td>
  </tr>
  {/if}
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

<input type="hidden" name="zone" value="{$zone.name}">
<input type="hidden" name="zoneid" value="{$zone.id}">
<table border="0" cellpadding="0" cellspacing="3">
  <tr>
    <td><font face="Arial,Helvetica" size="-1"><strong>Host</strong></font></td>
    <td><font face="Arial,Helvetica" size="-1"><strong>Type</strong></font></td>
    <td><font face="Arial,Helvetica" size="-1"><strong>Destination</strong></font></td>
    <td><font face="Arial,Helvetica" size="-1"><strong>Valid</strong></font></td>
    <td><font face="Arial,Helvetica" size="-1"><strong>Delete</strong></font></td>
  </tr>
  {section name=i loop=$record}
  <tr>
    <td>
	<input type="text" name="host[{$smarty.section.i.index}]" class="a1" value="{$record[i].host}" size="16">
	<input type="hidden" name="host_id[{$smarty.section.i.index}]" value="{$record[i].id}">
    </td>
    <td><select name="type[{$smarty.section.i.index}]" class="a1">
      {html_options values=$types selected=$record[i].type output=$types}
    </select></td>
{if $record[i].type == "MX"}
    <td>
	<input type="text" name="pri[{$smarty.section.i.index}]" class="a1" size="1" value="{$record[i].pri}">
	<input type="text" name="destination[{$smarty.section.i.index}]" class="a1" size="27" value="{$record[i].destination}">
    </td>
{else}
    <td>
	<input type="text" name="destination[{$smarty.section.i.index}]" class="a1" size="32" value="{$record[i].destination}">
    </td>
{/if}
    <td><center>
{if $record[i].valid == "yes"}<IMG ALT="YES" WIDTH="20" HEIGHT="20" SRC="../images/yes.png">
{elseif $record[i].valid == "no"}<IMG ALT="NO" WIDTH="20" HEIGHT="20" SRC="../images/no.png">
{else}<IMG ALT="UNKNOWN" WIDTH="20" HEIGHT="20" SRC="../images/unknown.png">
{/if}</center></td>
    <td><center><input type="checkbox" name="delete[{$smarty.section.i.index}]" class="a1"></center></td>
  </tr>
{/section}
  <tr>
   <td colspan="3"><hr size="1" noshade></td>
  </tr>
  <tr>
    <td><input type="text" name="newhost" class="a1" size="16"></td>
    <td><select name="newtype" class="a1">
      {html_options values=$types output=$types}
    </select></td>
    <td><input type="text" name="newdestination" class="a1" size="32"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>
		<input type="hidden" name="total" value="{$smarty.section.i.total}">
		<input name="Submit" type="submit" class="a" value="Save"></td>
  </tr>
</table>
</form>
{include file="pages.tpl"}
