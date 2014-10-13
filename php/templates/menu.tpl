<TD WIDTH=87>
{section name=i loop=$menu_button}
{if $menu_button[i].link != $menu_current}
	<P><B><FONT SIZE="-1" FACE="Arial,Helvetica">
	<A CLASS="class" HREF="./{$menu_button[i].link}">{$menu_button[i].title}</A>
	</FONT></B></P>
{else}
	<P><B><FONT SIZE="-1" FACE="Arial,Helvetica">
        {$menu_button[i].title}
	</FONT></B></P>
{/if}
{/section}
</TD>
