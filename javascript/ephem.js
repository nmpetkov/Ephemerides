/**
 * create the onload function to enable the respective functions
 *
 */
Event.observe(window, 'load', ephemerides_init_check, false);

function ephemerides_init_check()
{
    if($('ephemerides_multicategory_filter')) {
        ephemerides_filter_init(); 
    }
}

/**
 * Admin panel functions
 */
function ephemerides_filter_init()
{
    Event.observe('ephemerides_property', 'change', ephemerides_property_onchange, false);
    ephemerides_property_onchange();
    //$('ephemerides_multicategory_filter').style.display = 'inline';
}

function ephemerides_property_onchange()
{
    $$('div#ephemerides_category_selectors select').each(function(select){
        select.hide();
    });
    var id = "ephemerides_"+$('ephemerides_property').value+"_category";
    $(id).show();
}

/**
 * Toggle active/inactive status
 */
function setstatus(eid, status)
{
    ajaxindicator = document.getElementById("statusajaxind_"+eid);
    ajaxindicator.style.display = "inline";

    var pars = {eid: eid, status: status};
    new Zikula.Ajax.Request(Zikula.Config.baseURL+"ajax.php?module=Ephemerides&func=setstatus",
        {parameters: pars, onComplete: setstatus_response});
}
function setstatus_response(req)
{
    if (!req.isSuccess()) {
        Zikula.showajaxerror(req.getMessage());
        return;
    }
    var data = req.getData();
    
    if (data.alert) {
        alert(data.alert);
    }

    ajaxindicator = document.getElementById("statusajaxind_"+data.eid);
    ajaxindicator.style.display = "none";

    elementActive = document.getElementById("statusactive_"+data.eid);
    elementInactive = document.getElementById("statusinactive_"+data.eid);
    if (elementActive && elementInactive) {
        if (data.status == 1) {
            elementActive.style.display = "block";
            elementInactive.style.display = "none";
        } else {
            elementActive.style.display = "none";
            elementInactive.style.display = "block";
        }
    }
}