<div class="z-formrow">
    <label for="blocks_cache_time">{gt text="Cache time (enter positive number in seconds to enable cache)"}</label>
    <input id="blocks_cache_time" type="text" name="cache_time" size="10" maxlength="50" value="{$cache_time|safetext}" />
</div>
<div class="z-formrow">
    <label for="blocks_cache_dir">{gt text="Cache directory name (within Zikula Temp directory)"}</label>
    <input id="blocks_cache_dir" type="text" name="cache_dir" size="30" maxlength="255" value="{$cache_dir|safetext}" />
</div>
