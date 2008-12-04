{foreach from=$objsRelated item="c"}
<li class="itemBox obj {$c.status}">
	
		
	<input type="hidden" class="id" 	name="reorder[{$c.id}][id]" value="{$c.id}" />
	<input type="text" class="priority"	name="reorder[{$c.id}][priority]" value="{$c.priority|default:""}" size="3" maxlength="3"/>

	<span title="{$c.module}" class="listrecent {$c.module}" style="margin-left:0px">&nbsp;&nbsp;</span>
	{$c.title|truncate:"64":"…":true}
	
	<div style="margin-top:-20px; float:right;">
		{*
		{$c.lang} &nbsp;&nbsp;&nbsp; 
		*}
		<input type="button" class="BEbutton link" onClick="window.open($(this).attr('href'));" href="{$html->url('/')}{$c.module}/view/{$c.id}" name="details" value="››" />
		<!-- 
		<a title="{$c.module} | {$c.created}" href="{$html->url('/')}{$c.module}/view/{$c.id}">details</a>
		-->
		<input type="button" name="remove" value="x" />
	</div>
	
</li>
{/foreach}

{*
<table border=1>
{foreach from=$objsRelated item="c"  name="assocForeach"}
<tr class="itemBox obj {$c.status}">
	<td>
		<input type="hidden" class="id" 	name="reorder[{$c.id}][id]" value="{$c.id}" />
		<input type="text" class="priority"	name="reorder[{$c.id}][priority]" value="{$c.priority|default:""}" size="3" maxlength="3"/>
	</td>
	<td style="width:10px;">
		<span title="{$c.module}" class="listrecent {$c.module}" style="margin-left:0px">&nbsp;</span>
	</td>	
	<td>
		<a title="{$c.module} | {$c.created}" href="{$html->url('/')}{$c.module}/view/{$c.id}">{$c.title|truncate:"64":"…":true}</a>
	</td>
	<td>
		{$c.lang}
	</td>
	<td style="text-align:right; white-space:nowrap">
		<input class="BEbutton link" rel="{$html->url('/')}{$c.module}/view/{$c.id}" 
		name="details" type="button" value="details" >{$html->url('/')}{$c.module}/view/{$c.id}
		<input class="BEbutton" name="remove" type="button" value="x">
	</td>	
</tr>
{/foreach}
</table>
*}

