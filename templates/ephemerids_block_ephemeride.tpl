{section name='ephemerids' loop=$items}
<div class="ephemerids_block">
	{if $items[ephemerids].yid}
    <h3>{$items[ephemerids].yid|safetext}</h3>
    {/if}
    {$items[ephemerids].content|safehtml}
</div>
{/section}
