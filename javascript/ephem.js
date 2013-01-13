/**
 * create the onload function to enable the respective functions
 *
 */
Event.observe(window, 'load', ephemerids_init_check, false);

function ephemerids_init_check()
{
    if($('ephemerids_multicategory_filter')) {
        ephemerids_filter_init(); 
    }
}

/**
 * Admin panel functions
 */
function ephemerids_filter_init()
{
    Event.observe('ephemerids_property', 'change', ephemerids_property_onchange, false);
    ephemerids_property_onchange();
    //$('ephemerids_multicategory_filter').style.display = 'inline';
}

function ephemerids_property_onchange()
{
    $$('div#ephemerids_category_selectors select').each(function(select){
        select.hide();
    });
    var id = "ephemerids_"+$('ephemerids_property').value+"_category";
    $(id).show();
}
