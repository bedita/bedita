{foreach from=$objsRelated item="c"}
<li class="itemBox obj {$c.status}">
	
		
	<input type="hidden" class="id" 	name="reorder[{$c.id}][id]" value="{$c.id}" />
	<input type="text" class="priority"	name="reorder[{$c.id}][priority]" value="{$c.priority|default:""}" size="3" maxlength="3"/>

	<span title="{$c.module}" class="listrecent {$c.module}" style="margin-left:0px">&nbsp;&nbsp;</span>
	{$c.title|default:'<i>[no title]</i>'|truncate:"64":"…":true}
	
	<div style="margin-top:-20px; float:right;">
		<input type="button" class="BEbutton link" onClick="window.open($(this).attr('href'));" href="{$html->url('/')}{$c.module}/view/{$c.id}" name="details" value="››" />
		<input type="button" name="remove" value="x" />
	</div>
	
</li>
{/foreach}


