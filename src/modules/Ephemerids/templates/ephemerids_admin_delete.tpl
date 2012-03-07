{gt text="Delete ephemeride" assign='templatetitle'}
{include file="ephemerids_admin_menu.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src=editdelete.gif set=icons/large alt=$templatetitle}</div>
    <h2>{$templatetitle}</h2>
    <p class="z-warningmsg">{gt text="Do you really want to delete this ephemeride?"}</p>
    <form class="z-form" action="{modurl modname='Ephemerids' type='admin' func='delete'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <input type="hidden" name="confirmation" value="1" />
            <input type="hidden" name="eid" value="{$eid|safetext}" />
			<div class="z-formbuttons">
                {button src='button_ok.gif' set='icons/small' __alt='Confirm deletion?' __title='Confirm deletion?'}
				<a href="{modurl modname='Ephemerids' type='admin' func='view'}">{img modname='core' src=button_cancel.gif set=icons/small __alt="Cancel" __title="Cancel"}</a>
			</div>
        </div>
    </form>
</div>
