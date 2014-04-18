<div style="min-height:100px; margin-top:10px;">
{if !empty($sections)}

	<div id="areasections">
	<table class="indexlist" style="width:100%; margin-bottom:10px;">
		<tbody class="disableSelection">
		
		{include file="../../elements/form_assoc_object.tpl" objsRelated=$sections}
			
		</tbody>
	</table>
	</div>		
	
{else}
	<em style="display: inline-block; margin-bottom: 10px;">{t}no sections{/t}</em>
{/if}
	
	{include file="inc/tools_commands.tpl" type="section"}
	
	{bedev}
	{include file="inc/bulk_actions.tpl" type="section"}	
	{/bedev}
</div>




