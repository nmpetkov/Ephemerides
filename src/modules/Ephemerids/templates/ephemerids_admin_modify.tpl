{gt text="Edit ephemerid" assign='templatetitle'}
{include file="ephemerids_admin_menu.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='edit.gif' set='icons/large' alt=$templatetitle}</div>
    <h2>{$templatetitle}</h2>
    <form class="z-form" action="{modurl modname="Ephemerids" type='admin' func='update'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <input type="hidden" name="ephemerid[eid]" value="{$eid|safetext}" />
            <fieldset>
                <legend>{gt text='Content'}</legend>
                <div class="z-formrow">
                    <label for="ephemerids_date">{gt text='Date'}</label>
                    <div id="ephemerids_date">{html_select_date year_as_text=true time=$date field_array='ephemerid'}</div>
					<p class="z-formnote z-warningmsg">{gt text="The year can't be lower than the unix epoch one (1970) or will be reseted to 1970 for formatting purposes."}</p>
                </div>
                {configgetvar name='multilingual' assign='multilingual'}
                {if $multilingual}
                <div class="z-formrow">
                    <label for="ephemerids_language">{gt text='Language'}</label>
                    {html_select_locales id='ephemerids_language' name='ephemerid[language]' all=true installed=true selected=$language}
                </div>
                {/if}
                <div class="z-formrow">
                    <label for="ephemerids_content">{gt text="Content"}</label>
                    <textarea id="ephemerids_content" name="ephemerid[content]" cols="50" rows="8">{$content|safehtml}</textarea>
                </div>
                {if $enablecategorization}
                <div class="z-formrow">
                    <label>{gt text='Categories'}</label>
                    {gt text='Choose a category' assign='lblDef'}
                    {nocache}
                    {foreach from=$catregistry key='property' item='category'}
                    {array_field_isset array=$__CATEGORIES__ field=$property assign='catExists'}
                    {if $catExists}
                    {array_field_isset array=$__CATEGORIES__.$property field='id' returnValue=1 assign='selectedValue'}
                    {else}
                    {assign var='selectedValue' value='0'}
                    {/if}
                    <div class="z-formnote">{selector_category category=$category name="ephemerid[__CATEGORIES__][$property]" field='id' selectedValue=$selectedValue defaultValue='0' defaultText=$lblDef}</div>
                    {/foreach}
                    {/nocache}
                </div>
                {/if}
                <div class="z-formrow">
                    <label for="ephemerids_status">{gt text="Status"}</label>
                    <select name="ephemerid[status]" id="ephemerids_status">
                        <option label="{gt text="Active"}" value="1"{if $status eq 1} selected="selected"{/if}>{gt text="Active"}</option>
                        <option label="{gt text="Inactive"}" value="0"{if $status eq 0} selected="selected"{/if}>{gt text="Inactive"}</option>
                    </select>
                </div>
                <div class="z-formrow">
                    <label for="ephemerids_type">{gt text="Type"}</label>
                    <select name="ephemerid[type]" id="ephemerids_type">
                        <option value="1"{if $type eq 1} selected="selected"{/if}>{gt text="Event"} ({gt text="connected to given year"})</option>
                        <option value="2"{if $type eq 2} selected="selected"{/if}>{gt text="Holiday"} ({gt text="not connected to given year, no block title"})</option>
                    </select>
                </div>
            </fieldset>
            <fieldset>
                <legend>{gt text='Meta data'}</legend>
                <ul>
                    {usergetvar name='uname' uid=$cr_uid assign='username'}
                    <li>{gt text='Created by %s' tag1=$username}</li>
                    <li>{gt text='Created on %s' tag1=$cr_date|date_format}</li>
                    {usergetvar name='uname' uid=$lu_uid assign='username'}
                    <li>{gt text='Last update by %s' tag1=$username}</li>
                    <li>{gt text='Updated on %s' tag1=$lu_date|date_format}</li>
                </ul>
            </fieldset>
            <div class="z-formbuttons">
                {button src='button_ok.gif' set='icons/small' __alt='Update' __title='Update'}
                <a href="{modurl modname='Ephemerids' type='admin' func='view'}">{img modname='core' src='button_cancel.gif' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
            </div>
        </div>
    </form>
</div>
