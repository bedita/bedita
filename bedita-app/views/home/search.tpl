<script type="text/javascript">
<!--
// select page to visualize
{if !empty($objects.toolbar) && $objects.toolbar.next > 0 && $objects.toolbar.dim > 5}
	page = {$objects.toolbar.next};
{else}
	page = 1;
{/if}

$(document).ready(function() {

	$("#moreRes").click(function() {
		loadSearch(page, 10);
	});

});
//-->
</script>

<ul class="bordered smallist" style="overflow:hidden;">
{if !empty($objects.items)}
	{foreach from=$objects.items item="o"}
		<li style="white-space:nowrap"><span class="listrecent {$o.module_name}">&nbsp;&nbsp;</span>&nbsp;<a class="{$o.status}" title="{$o.module_name} | {t}modified on{/t} {$o.modified|date_format:$conf->dateTimePattern}" href="{$html->url('/')}{$o.module_name}/view/{$o.id}">{$o.title|escape|default:'<i>[no title]</i>'}</a></li>
	{/foreach}
{else}
	{t}no results{/t}
{/if}
</ul>

{if !empty($objects.toolbar) && $objects.toolbar.next > 0}
	<a href="javascript: void(0);" id="moreRes">{t}more results{/t}</a>
{/if}