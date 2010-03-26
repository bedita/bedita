{foreach from=$objsRelated item="s"}
	<tr class="obj {$s.status}">
		<td>
			<input type="hidden" class="id" 	name="reorder[{$s.id}][id]" value="{$s.id}" />
			<input type="text" class="priority"	name="reorder[{$s.id}][priority]" value="{$s.priority|default:""}" size="3" maxlength="3"/>
		</td>
		<td>
			<span class="listrecent areas" style="margin-left:0px">&nbsp;&nbsp;</span>
		</td>
		<td style="width:100%">
			{$s.title|default:'<i>[no title]</i>'|truncate:"64":"…":true}
		</td>
		<td>
			{$s.lang}
		</td>
		<td class="commands" style="white-space:nowrap">
			<input type="button" class="BEbutton link" onClick="window.location.href = ($(this).attr('href'));" 
			href="{$html->url('/')}areas/view/{$s.id}" name="details" value="››" />
		</td>
	</tr>
{/foreach}





