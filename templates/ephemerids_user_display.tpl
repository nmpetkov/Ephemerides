{section name='ephemerids' loop=$items}
<div class="ephemerids_display">
	{if $items[ephemerids].yid}
    <h3>{$items[ephemerids].yid|safetext}</h3>
    {/if}
    {$items[ephemerids].content|safehtml}
    {notifydisplayhooks eventname='ephemerids.ui_hooks.items.display_view' id=$items[ephemerids].eid}
</div>
{/section}
