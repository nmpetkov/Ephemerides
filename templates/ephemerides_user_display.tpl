{checkpermission component='Ephemerides::' instance='::' level='ACCESS_EDIT' assign='authedit'}
{assign var=thisyear value=$smarty.now|date_format:"%Y"}
{section name='ephemerides' loop=$items}
{setmetatag name='description' value=$items[ephemerides].content|strip_tags|trim|truncate:500}
{pagesetvar name='title' value="`$items[ephemerides].yid`-`$items[ephemerides].mid`-`$items[ephemerides].did`"|strip_tags}
<div class="ephemerides_display">
    <h3>
	{if $items[ephemerides].yid}{$items[ephemerides].yid|safetext}{else}{$thisyear}{/if}, {1|mktime:0:0:$items[ephemerides].mid:$items[ephemerides].did:$thisyear|dateformat:'%e %B'}{* using current year to avoid unix limitation *}
    </h3>
    {$items[ephemerides].content|safehtml}
    {if $authedit}<a href="{modurl modname='Ephemerides' type='admin' func='modify' eid=$items[ephemerides].eid delcache=true}">Edit</a>{/if}
    {notifydisplayhooks eventname='ephemerides.ui_hooks.items.display_view' id=$items[ephemerides].eid}
    {if $modvars.Ephemerides.enablefacebookshare}
        <div class="fb-share-button" data-href="{modurl modname='Ephemerides' type='user' func='display' eid=$items[ephemerides].eid fqurl=true}"></div>
        {getImage htmlcontent=$items[ephemerides].content putbaseurl=true assign='imagesrc'}{if $imagesrc}{pageaddvarblock}
        <meta property="og:image" content="{$imagesrc}"/>
        {/pageaddvarblock}{/if}
    {/if}
</div>
{/section}
{if $modvars.Ephemerides.enablefacebookshare}
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&appId=1468323200061516&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
{/if}