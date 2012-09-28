<script type="text/javascript">
<!--
// select page to visualize
{if !empty($objects.toolbar) && $objects.toolbar.next > 0 && $objects.toolbar.dim > 5}
	page = {$objects.toolbar.next};
{else}
	page = 1;
{/if}

urlToSearchPag = urlToSearch +  "/" + page + "/10";

$(document).ready(function() {

	$("#moreRes").click(function() {
		$("#searchResult").load(urlToSearchPag, { searchstring: $("input[name='searchstring']").val()}, function() {
			
		});
	});
	
});
//-->
</script>

<ul class="bordered smallist">
{if !empty($objects.items)}
	{foreach from=$objects.items item="o"}
		<li><span class="listrecent {$o.module_name}">&nbsp;&nbsp;</span>&nbsp;<a class="{$o.status}" title="{$o.module_name} | {t}modified on{/t} {$o.modified|date_format:$conf->dateTimePattern}" href="{$this->Html->url('/')}{$o.module_name}/view/{$o.id}">{$o.title|default:'<i>[no title]</i>'}</a></li>
	{/foreach}
{else}
	{t}no results{/t}
{/if}
</ul>

{if !empty($objects.toolbar) && $objects.toolbar.next > 0}
	<a href="javascript: void(0);" id="moreRes">{t}more results{/t}</a>
{/if}