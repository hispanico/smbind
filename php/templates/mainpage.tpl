<font face="Arial,Helvetica" size="-1">
<strong>Welcome, {$user}.</strong>
{if $admin == "yes"} (<u>administrator</u>){/if}<br><br>
{if $status == 0}DNS Services are <b>started</b>.{/if}
{if $status == 1}DNS Services are <b>stopped</b>.{/if}
You maintain <strong>{$zones}</strong> zones.<br><br>
{section name=i loop=$bad}
<b>WARNING:</b> The following zone contains bad or uncommitted records: <a class="class" href="./record.php?i={$bad[i].id}"><b>{$bad[i].name}</b></a><br>
{/section}
</font>
