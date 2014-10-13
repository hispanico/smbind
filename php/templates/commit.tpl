<font size="-1" face="Arial,Helvetica">
{if $bad}
{section name=i loop=$bad}
<b>WARNING:</b> The following zone contains bad or uncommitted records: <a class="class" href="./record.php?i={$bad[i].id}"><b>{$bad[i].name}</b></a><br>
{/section}
{else}The changes have been committed successfully.<br>
{/if}
{if $output}
<br>Output from named-checkzone: <br>
{section name=i loop=$output}
<pre>  {$output[i]}</pre>
{/section}
{/if}
</font>
