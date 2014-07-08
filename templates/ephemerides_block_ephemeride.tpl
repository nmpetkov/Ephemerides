{if $items}
{ajaxheader imageviewer="true"}
{checkpermission component='Ephemerides::' instance='::' level='ACCESS_EDIT' assign='authedit'}
{section name='ephemerides' loop=$items}
<div class="ephemerides_block">
	{if $items[ephemerides].yid}
    <h3>{$items[ephemerides].yid|safetext}</h3>
    {/if}
    {$items[ephemerides].content|safehtml}
    {if $authedit}<a href="{modurl modname='Ephemerides' type='admin' func='modify' eid=$items[ephemerides].eid delcache=true}">Edit</a>{/if}
</div>
{/section}
{/if}