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
