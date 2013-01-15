{section name='ephemerides' loop=$items}
<div class="ephemerides_block">
	{if $items[ephemerides].yid}
    <h3>{$items[ephemerides].yid|safetext}</h3>
    {/if}
    {$items[ephemerides].content|safehtml}
</div>
{/section}
