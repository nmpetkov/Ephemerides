{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="new" size="small"}
    <h3>{gt text='Create new ephemeride'}</h3>
</div>

<div class="z-adminpageicon">{img modname='core' src='filenew.gif' set='icons/large' alt=$templatetitle}</div>
<h2>{$templatetitle}</h2>

<form class="z-form" action="{modurl modname='Ephemerids' type='admin' func='create'}" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    <fieldset>
        <div class="z-formrow">
            <label for="ephemerids_date">{gt text='Date (month, date, year)'}</label>
            <div id="ephemerids_date">{html_select_date year_as_text=true field_array='ephemerid'}</div>
            <p class="z-formnote z-warningmsg">{gt text="The year can be empty in case of holiday."}</p>
        </div>
        {if $modvars.ZConfig.multilingual}
        <div class="z-formrow">
            <label for="ephemerids_language">{gt text='Language'}</label>
            {html_select_locales id='ephemerids_language' name='ephemerid[language]' all=true installed=true selected=$language}
        </div>
        {/if}
    </fieldset>
    <fieldset class="z-linear">
        <div class="z-formrow">
            <label for="ephemerids_content">{gt text='Content'}</label>
            <textarea id="ephemerids_content" name="ephemerid[content]" cols="50" rows="8"></textarea>
        </div>
    </fieldset>
    <fieldset>
        {if $enablecategorization}
        <div class="z-formrow">
            <label>{gt text='Categories'}</label>
            {gt text='Choose a category' assign='lblDef'}
            {nocache}
            {foreach from=$catregistry key='property' item='category'}
            <div class="z-formnote">{selector_category category=$category name="ephemerid[__CATEGORIES__][$property]" field='id' selectedValue='0' defaultValue='0' defaultText=$lblDef}</div>
            {/foreach}
            {/nocache}
        </div>
        {/if}
        <div class="z-formrow">
            <label for="ephemerids_status">{gt text='Status'}</label>
            <select name="ephemerid[status]" id="ephemerids_status">
                <option label="{gt text="Active"}" value="1"{if $status eq 1} selected="selected"{/if}>{gt text="Active"}</option>
                <option label="{gt text="Inactive"}" value="0"{if $status eq 0} selected="selected"{/if}>{gt text="Inactive"}</option>
            </select>
        </div>
        <div class="z-formrow">
            <label for="ephemerids_type">{gt text='Type'}</label>
            <select name="ephemerid[type]" id="ephemerids_type">
                <option value="1"{if $type eq 1} selected="selected"{/if}>{gt text="Event"} ({gt text="connected to given year"})</option>
                <option value="2"{if $type eq 2} selected="selected"{/if}>{gt text="Holiday"} ({gt text="not connected to given year, no block title"})</option>
            </select>
        </div>
    </fieldset>

    {notifydisplayhooks eventname='ephemerids.ui_hooks.items.form_edit' id=null}

    <div class="z-buttonrow z-buttons z-center">
        {button src='button_ok.gif' set='icons/small' __alt='Create' __title='Create'}
        <a href="{modurl modname='Ephemerids' type='admin' func='view'}">{img modname='core' src='button_cancel.gif' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
    </div>
</form>
{adminfooter}