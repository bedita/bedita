{foreach from=$objsRelated item="objRelated" name="o"}

	<tr>
		<td style="vertical-align:top">
		<input type="hidden" class="id" name="data[RelatedObject][question][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="text" class="priority" 
				style="margin:0px; width:20px; text-align:right; background-color:transparent"
				name="data[RelatedObject][question][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
		</td>
		<td>{$objRelated.title}</td>
		<td>{t}{$objRelated.question_type}{/t}</td>
		<td style="text-align:center">{$objRelated.status}</td>
		<td style="white-space:nowrap">
			<input class="BEbutton link" rel="{$html->url('/')}questionnaires/view/{$objRelated.id}" name="details" type="button" value="details">
			<input class="BEbutton" name="remove" type="button" value="x">
		</td>
	</tr>

{/foreach}