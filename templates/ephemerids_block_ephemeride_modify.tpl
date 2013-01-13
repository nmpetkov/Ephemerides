{if $enablecategorization}
<div class="z-formrow">
    <label>{gt text='Choose categories'}</label>
    {nocache}
    {foreach from=$catregistry key='prop' item='cat'}
    {array_field_isset array=$category field=$prop returnValue=1 assign='selectedValue'}
    <div class="z-formnote">
        {selector_category category=$cat name="category[$prop]" multipleSize=5 selectedValue=$selectedValue}
    </div>
    {/foreach}
    {/nocache}
</div>
{/if}
<div class="z-formrow">
    <label for="blocks_cache_time">{gt text="Cache time (enter positive number in seconds to enable cache)"}</label>
    <input id="blocks_cache_time" type="text" name="cache_time" size="10" maxlength="50" value="{$cache_time|safetext}" />
</div>
<div class="z-formrow">
    <label for="blocks_cache_dir">{gt text="Cache directory name (within Zikula Temp directory)"}</label>
    <input id="blocks_cache_dir" type="text" name="cache_dir" size="30" maxlength="255" value="{$cache_dir|safetext}" />
</div>
