{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="config" size="small"}
    <h3>{gt text='Settings'}</h3>
</div>

<form class="z-form" action="{modurl modname='Ephemerides' type='admin' func='updateconfig'}" method="post" enctype="application/x-www-form-urlencoded">
	<div>
		<input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
		<fieldset>
			<div class="z-formrow">
				<label for="ephemerides_enablecategorization">{gt text='Enable categorization'}</label>
				<input id="ephemerides_enablecategorization" type="checkbox" name="enablecategorization"{if $enablecategorization} checked="checked"{/if} />
			</div>
			<div class="z-formrow">
				<label for="ephemerides_itemsperpage">{gt text="Items per page in admin panel list view"}</label>
				<input id="ephemerides_itemsperpage" type="text" name="itemsperpage" size="3" value="{$itemsperpage|safetext}" />
			</div>
			<div class="z-formrow">
				<label for="ephemerides_enablefacebookshare">{gt text='Enable Facebook share button'}</label>
				<input id="ephemerides_enablefacebookshare" type="checkbox" name="enablefacebookshare"{if $enablefacebookshare} checked="checked"{/if} />
			</div>
		</fieldset>
		<div class="z-formrow z-formbuttons">
			{button src='button_ok.png' set='icons/small' __alt='Save' __title='Save'}
			<a href="{modurl modname='Ephemerides' type='admin' func='view'}">{img modname='core' src='button_cancel.png' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
		</div>
	</div>
</form>
{adminfooter}