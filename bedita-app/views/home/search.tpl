<script type="text/javascript">
<!--

// select page to visualize
{if !empty($objects.toolbar) && $objects.toolbar.next > 0 && $objects.toolbar.dim > 5}
	page = {$objects.toolbar.next};
{else}
	page = 1;
{/if}

urlToSearchPag = urlToSearch +  "/" + page + "/10";

{literal}
$(document).ready(function() {

	$("#moreRes").click(function() {
		$("#searchResult").load(urlToSearchPag, {searchstring: $("input[name='searchstring']").val()}, function() {
			
		});
	});
	
});
{/literal}
//-->
</script>

<ul class="bordered smallist">
{if !empty($objects.items)}
	{foreach from=$objects.items item="o"}
		<li><span class="listrecent {$o.module}">&nbsp;&nbsp;</span>&nbsp;<a class="{$o.status}" title="{$o.module} | {t}modified on{/t} {$o.modified}" href="{$html->url('/')}{$o.module}/view/{$o.id}">{$o.title|default:'<i>[no title]</i>'}</a></li>
	{/foreach}
{else}
	{t}no results{/t}
{/if}
</ul>

{if !empty($objects.toolbar) && $objects.toolbar.next > 0}
	<a href="javascript: void(0);" id="moreRes">{t}more results{/t}</a>
{/if}