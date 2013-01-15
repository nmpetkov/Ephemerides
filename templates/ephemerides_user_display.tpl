{section name='ephemerides' loop=$items}
<div class="ephemerides_display">
	{if $items[ephemerides].yid}
    <h3>{$items[ephemerides].yid|safetext}</h3>
    {/if}
    {$items[ephemerides].content|safehtml}
    {notifydisplayhooks eventname='ephemerides.ui_hooks.items.display_view' id=$items[ephemerides].eid}
</div>
{/section}
