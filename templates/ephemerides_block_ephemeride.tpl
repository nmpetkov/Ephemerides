{if $items}
{ajaxheader imageviewer="true"}
{checkpermission component='Ephemerides::' instance='::' level='ACCESS_EDIT' assign='authedit'}
{assign var=thisyear value=$smarty.now|date_format:"%Y"}
{section name='ephemerides' loop=$items}
<div class="ephemerides_block">
    <h3>
	{if $items[ephemerides].yid}{$items[ephemerides].yid|safetext}{else}{$thisyear}{/if}, {1|mktime:0:0:$items[ephemerides].mid:$items[ephemerides].did:$thisyear|dateformat:'%e %B'}{* using current year to avoid unix limitation *}
    </h3>
    {$items[ephemerides].content|safehtml}
    {if $authedit}<a href="{modurl modname='Ephemerides' type='admin' func='modify' eid=$items[ephemerides].eid delcache=true}">Edit</a>{/if}
    {if $enablefacebookshare}<a href="{modurl modname='Ephemerides' type='user' func='display' eid=$items[ephemerides].eid}" title="{gt text='View and share'}"><img src="modules/Ephemerides/images/facebook_icon_small.png" alt="" style="display: inline;" /></a>{/if}
</div>
{/section}
{/if}