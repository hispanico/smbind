{include file="pages.tpl"}
<font size="-1" face="Arial,Helvetica">
<a class="class" href="./newuser.php">Add a new user</a>
</font>
<br><br>
<table width="100%"  border="0" cellspacing="1">
  <tr>
    <td><font size="-1" face="Arial,Helvetica"><strong>Username</strong></font></td>
    <td><center><font size="-1" face="Arial,Helvetica"><strong>Administrator</strong></font></center></td>
    <td><font size="-1" face="Arial,Helvetica"><strong>Delete</strong></font></td>
  </tr>
{section name=i loop=$userlist}
  <tr>
    <td><font face="Arial,Helvetica" size="-1">
{if $userlist[i].username != "admin"}
<a class="class" href="./user.php?i={$userlist[i].id}">{$userlist[i].username}</a>
{else}
{$userlist[i].username}
{/if}
   </font></td>
    <td><center><font face="Arial,Helvetica" size="-1">
{if $userlist[i].admin == "yes"}<IMG ALT="YES" WIDTH="20" HEIGHT="20" SRC="../images/yes.png">
{elseif $userlist[i].admin == "no"}<IMG ALT="NO" WIDTH="20" HEIGHT="20" SRC="../images/no.png">
{/if}
   </font></center></td>
    <td><font face="Arial,Helvetica" size="-1">
{if $userlist[i].username != "admin"}
<a class="class" href="./deleteuser.php?i={$userlist[i].id}">Delete</a>
{else}
Delete
{/if}
   </font></td>
  </tr>
{/section}
</table>
{include file="pages.tpl"}
