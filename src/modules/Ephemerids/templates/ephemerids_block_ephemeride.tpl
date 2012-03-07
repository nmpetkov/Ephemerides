{capture assign='templatestyles'}
<style type="text/css">
	.ephemerids_block {
		background:#FAFAFA;
		border:1px solid #C8CDD2;
		color:#333333;
		padding:1em;
    }
</style>
{/capture}
{pageaddvar name='header' value=$templatestyles}

{section name='ephemerids' loop=$items}
<div class="ephemerids_block">
	{if $items[ephemerids].yid}
    <h3>{$items[ephemerids].yid|safetext}</h3>
    {/if}
    {$items[ephemerids].content|safehtml}
</div>
{/section}
