{include file="pages.tpl"}
<font size="-1" face="Arial,Helvetica">
<a class="class" href="./newzone.php">Create a new zone</a>
</font>
<br><br>
<table width="100%"  border="0" cellspacing="1">
  <tr>
    <td><font size="-1" face="Arial,Helvetica"><strong>Name</strong></font></td>
    <td><font size="-1" face="Arial,Helvetica"><strong>Serial</strong></font></td>
    <td><center><font size="-1" face="Arial,Helvetica"><strong>Changed</strong></font></center></td>
    <td><center><font size="-1" face="Arial,Helvetica"><strong>Valid</strong></font></center></td>
    <td><font size="-1" face="Arial,Helvetica"><strong>Delete</strong></font></td>
  </tr>
{section name=i loop=$zonelist}
  <tr>
    <td><font size="-1" face="Arial,Helvetica"><a class="class" href="./record.php?i={$zonelist[i].id}">{$zonelist[i].name}</a></font></td>
    <td><font size="-1" face="Arial,Helvetica">{$zonelist[i].serial}</font></td>
    <td><center><font size="-1">
{if $zonelist[i].updated == "yes"}<IMG ALT="YES" WIDTH="20" HEIGHT="20" SRC="../images/yes.png">
{elseif $zonelist[i].updated == "no"}<IMG ALT="NO" WIDTH="20" HEIGHT="20" SRC="../images/no.png">
{/if}
   </font></center></td>
    <td><center><font size="-1">
{if $zonelist[i].valid == "yes"}<IMG ALT="YES" WIDTH="20" HEIGHT="20" SRC="../images/yes.png">
{elseif $zonelist[i].valid == "no"}<IMG ALT="NO" WIDTH="20" HEIGHT="20" SRC="../images/no.png">
{else}<IMG ALT="UNKNOWN" WIDTH="20" HEIGHT="20" SRC="../images/unknown.png">
{/if}
   </font></center></td>
    <td><font size="-1" face="Arial,Helvetica"><a class="class" href="./deletezone.php?i={$zonelist[i].id}">Delete</a></font></td>
  </tr>
{/section}
</table>
{include file="pages.tpl"}
