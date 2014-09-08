{checkpermission component='Ephemerides::' instance='::' level='ACCESS_EDIT' assign='authedit'}
{section name='ephemerides' loop=$items}
{setmetatag name='description' value=$items[ephemerides].content|strip_tags|trim|truncate:500}
{pagesetvar name='title' value="`$items[ephemerides].yid`-`$items[ephemerides].mid`-`$items[ephemerides].did`"|strip_tags}
<div class="ephemerides_display">
	{if $items[ephemerides].yid}
    <h3>{$items[ephemerides].yid|safetext}</h3>
    {/if}
    {$items[ephemerides].content|safehtml}
    {if $authedit}<a href="{modurl modname='Ephemerides' type='admin' func='modify' eid=$items[ephemerides].eid delcache=true}">Edit</a>{/if}
    {notifydisplayhooks eventname='ephemerides.ui_hooks.items.display_view' id=$items[ephemerides].eid}
</div>
{/section}
