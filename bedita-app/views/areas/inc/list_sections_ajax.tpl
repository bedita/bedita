<script type="text/javascript">
<!--
{literal}
$(document).ready(function() {

	$("#areasections").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).reorderListItem
	}).css("cursor","move");

	$("#sections_nav a").click(function() {
			
		$("#loading").show();
		$("#areasectionsC").load(urlS, {page:$(this).attr("rel")}, function() {
			$("#loading").hide();
		});
		
	});

});
{/literal}
//-->
</script>

{if !empty($sections.items)}

	<ul style="margin-top:10px; display: block;" id="areasections" class="bordered">
		{foreach from=$sections.items item=s}
		<li class="itemBox">
			<input type="hidden" class="id" 	name="reorder[{$s.id}][id]" value="{$s.id}" />
			<input type="text" class="priority"  name="reorder[{$s.id}][priority]" value="{$s.priority}" size="3" maxlength="3"/>
			<span class="listrecent areas" style="margin-left:0px">&nbsp;&nbsp;</span>
			<a title="{$s.created}" href="{$html->url('/')}areas/index/{$s.id}">{$s.title}</a>
			
		</li>
		{/foreach}
	</ul>		
	
	<div id="sections_nav">
	{if $sections.toolbar.prev > 0}
		<a href="javascript:void(0);" rel="{$sections.toolbar.prev}" class="graced" style="font-size:3em">‹</a>
	{/if}
	{if $sections.toolbar.next > 0}
		<a href="javascript:void(0);" rel="{$sections.toolbar.next}" class="graced" style="font-size:3em">›</a>
	{/if}
	</div>

{else}
	{t}no sections{/t}
{/if}