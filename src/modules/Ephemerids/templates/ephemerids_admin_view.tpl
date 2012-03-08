{gt text="Ephemerides list" assign='templatetitle'}
{include file="ephemerids_admin_menu.tpl"}

<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='filenew.gif' set='icons/large' alt=$templatetitle}</div>
    <h2>{$templatetitle}</h2>

    <form action="{modurl modname='Ephemerids' type='admin' func='view'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            {if $enablecategorization and $numproperties gt 0}
            <div id="ephemerids_multicategory_filter">
                <label for="ephemerids_property">{gt text='Category'}</label>
                {gt text='All' assign='lblDef'}
                {nocache}
                {if $numproperties gt 1}
                {html_options id='ephemerids_property' name='ephemerids_property' options=$properties selected=$property}
                {else}
                <input type="hidden" id="ephemerids_property" name="ephemerids_property" value="{$property}" />
                {/if}
                <div id="ephemerids_category_selectors" style="display: inline">
                    {foreach from=$catregistry key='prop' item='cat'}
                    {assign var='propref' value=$prop|string_format:'ephemerids_%s_category'}
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
            <label for="ephemerids_keyword">{gt text='Search by keyword'}:</label>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <input id="ephemerids_keyword" type="text" name="ephemerids_keyword" value="{$ephemerids_keyword|safetext}" size="20" maxlength="128" />
            &nbsp;
            <input name="submit" type="submit" value="{gt text='Filter'}" />
            <input name="clear" type="submit" value="{gt text='Clear'}" />
        </div>
    </form>

    <table class="z-admintable">
        <thead>
            <tr>
                <th>{gt text="Month"}</th>
                <th>{gt text="Day"}</th>
                <th>{gt text="Year"}</th>
                <th>{gt text="Content"}</th>
                {if $enablecategorization}
                <th>{gt text='Category'}</th>
                {/if}
                <th>{gt text="Internal ID"}</th>
                <th>{gt text="Status"}</th>
                <th>{gt text="Type"}</th>
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
                <td>
                    {if $ephemeride.status eq 0}<strong><em>{gt text='Inactive'}</em></strong>{/if}
                    {if $ephemeride.status eq 1}{gt text='Active'}{/if}
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
            <tr class="z-admintableempty"><td colspan="4">{gt text='No items found.'}</td></tr>
            {/foreach}
        </tbody>
    </table>
    {pager rowcount=$pager.numitems limit=$pager.itemsperpage posvar=startnum}
</div>
