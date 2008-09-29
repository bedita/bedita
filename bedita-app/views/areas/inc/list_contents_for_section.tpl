{foreach from=$objsRelated item="c"}
<li class="itemBox obj {$c.status}">
	
		
	<input type="hidden" class="id" 	name="reorder[{$c.id}][id]" value="{$c.id}" />
	<input type="text" class="priority"	name="reorder[{$c.id}][priority]" value="{$c.priority|default:""}" size="3" maxlength="3"/>

	<span title="{$c.module}" class="listrecent {$c.module}" style="margin-left:0px">&nbsp;&nbsp;</span>
	<a title="{$c.module} | {$c.created}" href="{$html->url('/')}{$c.module}/view/{$c.id}">{$c.title}</a>
	

	<div style="margin-top:-20px; float:right;">
		{$c.lang} &nbsp;&nbsp;&nbsp; 
		<input type="button" class="" value="x" />
	</div>
	
</li>
{/foreach}



