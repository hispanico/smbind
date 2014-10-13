{if $pages}<center><font size="-1" face="Arial,Helvetica">{/if}
{foreach name=pages from=$pages item=page}
        {if $smarty.foreach.pages.first}
                {if $current_page == $page}
                        &lt;&lt;First &lt;Previous
                {else}
                        <a class="class" href="{$page_root}page={$page}">&lt;&lt;First</a>
                        <a class="class" href="{$page_root}page={math equation="$current_page - 1"}">&lt;Previous</a>
                {/if}
        {/if}

        {if $current_page == $page}
                {$page}
        {else}
                <a class="class" href="{$page_root}page={$page}">{$page}</a>
        {/if}

        {if $smarty.foreach.pages.last}
                {if $current_page == $page}
                        Next&gt; Last&gt;&gt;
                {else}
                        <a class="class" href="{$page_root}page={math equation="$current_page + 1"}">Next&gt;</a>
                        <a class="class" href="{$page_root}page={$page}">Last&gt;&gt;</a>
                {/if}
        {/if}
{/foreach}
{if $pages}</font></center>{/if}
