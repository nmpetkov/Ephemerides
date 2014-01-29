{ajaxheader modname='Ephemerides' filename='ephem.js' nobehaviour=true noscriptaculous=true}
{gt text="Click to activate" assign=activate}
{gt text="Click to deactivate" assign=deactivate}

{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="view" size="small"}
    <h3>{gt text='Ephemerides List'}</h3>
</div>

<form class="z-form" action="{modurl modname='Ephemerides' type='admin' func='view'}" method="post" enctype="application/x-www-form-urlencoded">
    <fieldset{if $filter_active} class='filteractive'{/if}>
        {if $filter_active}{gt text='active' assign='filteractive'}{else}{gt text='inactive' assign='filteractive'}{/if}
        <legend>{gt text='Filter %1$s, %2$s page listed' plural='Filter %1$s, %2$s records listed' count=$pager.numitems tag1=$filteractive tag2=$pager.numitems}</legend>
		{if $enablecategorization and $numproperties gt 0}
		<div id="ephemerides_multicategory_filter">
			<label for="ephemerides_property">{gt text='Category'}</label>
			{gt text='All' assign='lblDef'}
			{nocache}
			{if $numproperties gt 1}
			{html_options id='ephemerides_property' name='ephemerides_property' options=$properties selected=$property}
			{else}
			<input type="hidden" id="ephemerides_property" name="ephemerides_property" value="{$property}" />
			{/if}
			<div id="ephemerides_category_selectors" style="display: inline">
				{foreach from=$catregistry key='prop' item='cat'}
				{assign var='propref' value=$prop|string_format:'ephemerides_%s_category'}
				{if $property eq $prop}
				{assign var='selectedValue' value=$category}
				{else}
				{assign var='selectedValue' value=0}
				{/if}
				<noscript>
					<div class="property_selector_noscript"><label for="{$propref}">{$prop}</label>:</div>
				</noscript>
				{selector_category category=$cat name=$propref selectedValue=$selectedValue allValue=0 allText=$lblDef editLink=false}
				{/foreach}
			</div>
			{/nocache}
		</div>
		{/if}
		<label for="ephemerides_keyword">{gt text='Search by keyword'}:</label>
		<input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
		<input id="ephemerides_keyword" type="text" name="ephemerides_keyword" value="{$ephemerides_keyword|safetext}" size="20" maxlength="128" />
		&nbsp;&nbsp;
		<span class="z-nowrap z-buttons">
			<input class='z-bt-filter' name="submit" type="submit" value="{gt text='Filter'}" />
			<input class='z-bt-cancel' name="clear" type="submit" value="{gt text='Clear'}" />
		</span>
    </fieldset>
</form>

<table class="z-datatable">
	<thead>
		<tr>
			<th>{sortlink __linktext='Month' sort='mid' currentsort=$sort sortdir=$sortdir modname='Ephemerides' type='admin' func='view' keyword=$ephemerides_keyword property=$property category=$category}</th>
			<th>{sortlink __linktext='Day' sort='did' currentsort=$sort sortdir=$sortdir modname='Ephemerides' type='admin' func='view' keyword=$ephemerides_keyword property=$property category=$category}</th>
			<th>{sortlink __linktext='Year' sort='yid' currentsort=$sort sortdir=$sortdir modname='Ephemerides' type='admin' func='view' keyword=$ephemerides_keyword property=$property category=$category}</th>
			<th>{sortlink __linktext='Content' sort='content' currentsort=$sort sortdir=$sortdir modname='Ephemerides' type='admin' func='view' keyword=$ephemerides_keyword property=$property category=$category}</th>
			{if $enablecategorization}
			<th>{gt text='Category'}</th>
			{/if}
			<th>{sortlink __linktext='ID' sort='eid' currentsort=$sort sortdir=$sortdir modname='Ephemerides' type='admin' func='view' keyword=$ephemerides_keyword property=$property category=$category}</th>
			<th>{sortlink __linktext='Status' sort='status' currentsort=$sort sortdir=$sortdir modname='Ephemerides' type='admin' func='view' keyword=$ephemerides_keyword property=$property category=$category}</th>
			<th>{sortlink __linktext='Type' sort='type' currentsort=$sort sortdir=$sortdir modname='Ephemerides' type='admin' func='view' keyword=$ephemerides_keyword property=$property category=$category}</th>
			<th>{gt text="Actions"}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$ephemerides item='ephemeride'}
		<tr class="{cycle values='z-odd,z-even'}" style="vertical-align: top">
			<td>{$ephemeride.mid}</td>
			<td>{$ephemeride.did}</td>
			<td>{if $ephemeride.yid}{$ephemeride.yid}{/if}</td>
			<td>{$ephemeride.content}</td>
			{if $enablecategorization}
			<td>
				{assignedcategorieslist item=$ephemeride}
			</td>
			{/if}
			<td>{$ephemeride.eid}</td>
			<td class="z-nowrap">
                <div id="statusactive_{$ephemeride.eid}" style="display: {if $ephemeride.status}block{else}none{/if};">
                    <a href="javascript:void(0);" onclick="setstatus({$ephemeride.eid},0)">{img src="greenled.png" modname="core" set="icons/extrasmall" title=$deactivate alt=$deactivate}</a>
                    &nbsp;{gt text="Active"}
                </div>
                <div id="statusinactive_{$ephemeride.eid}" style="display: {if $ephemeride.status}none{else}block{/if};">
                    <a href="javascript:void(0);" onclick="setstatus({$ephemeride.eid},1)">{img src="redled.png" modname="core" set="icons/extrasmall" title=$activate alt=$activate}</a>
                    &nbsp;{gt text="Inactive"}
                </div>
                {img id="statusajaxind_"|cat:$ephemeride.eid style="display: none;" modname=core set="ajax" src="indicator_circle.gif" alt=""}
			</td>
			<td>
				{if $ephemeride.type eq 1}{gt text='Event'}{/if}
				{if $ephemeride.type eq 2}{gt text='Holiday'}{/if}
			</td>
			<td>
				{foreach item='option' from=$ephemeride.options}
				<a href="{$option.url|safetext}">{img modname='core' set='icons/extrasmall' src=$option.image title=$option.title alt=$option.title}</a>
				{/foreach}
			</td>
		</tr>
		{foreachelse}
		<tr class="z-datatableempty"><td colspan="9">{gt text='No items found.'}</td></tr>
		{/foreach}
	</tbody>
</table>

{pager rowcount=$pager.numitems limit=$pager.itemsperpage posvar=startnum}

{adminfooter}