<script type="text/javascript">
<!--
{literal}
$(document).ready(function() {

	$("#areacontent").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).reorderListItem
	}).css("cursor","move");

	$("#contents_nav a").click(function() {
			
		$("#loading").show();
		$("#areacontentC").load(urlC, {page:$(this).attr("rel")}, function() {
			$("#loading").hide();
		});
		
	});

});
{/literal}
//-->
</script>
<div style="min-height:120px; margin-top:10px;">

{if !empty($contents.items)}
	
	<ul id="areacontent" class="bordered">
		{foreach from=$contents.items item="c"}
		<li class="itemBox obj {$c.status}">
			<input type="hidden" class="id" 	name="reorder[{$c.id}][id]" value="{$c.id}" />
			<input type="text" class="priority"	name="reorder[{$c.id}][priority]" value="{$c.priority}" size="3" maxlength="3"/>
	
			<span title="{$c.module}" class="listrecent {$c.module}" style="margin-left:0px">&nbsp;&nbsp;</span>
			<a title="{$c.module} | {$c.created}" href="{$html->url('/')}{$c.module}/view/{$c.id}">{$c.title}</a>
			
		</li>
		{/foreach}
	</ul>		


	
	<div id="contents_nav">

		
	{if $contents.toolbar.prev > 0}
		<a href="javascript:void(0);" rel="{$contents.toolbar.prev}" class="graced" style="font-size:3em">‹</a>
	{/if}
	{if $contents.toolbar.next > 0}
		<a href="javascript:void(0);" rel="{$contents.toolbar.next}" class="graced" style="font-size:3em">›</a>
	{/if}
	</div>

{else}
	&nbsp;&nbsp;<em>{t}no contents{/t}</em>
	<hr />
{/if}

</div>	

	