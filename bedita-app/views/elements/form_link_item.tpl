<td class="priority-column">
	<input
		class="id"
		type="hidden"
		name="data[RelatedObject][link][{$objRelated.id}][id]"
		value="{$objRelated.id|default:''}"
	/>
	<input
		class="priority link"
		type="text"
		name="data[RelatedObject][link][{$objRelated.id}][priority]"
		value="{$objRelated.priority|default:1}"
		size="3"
		maxlength="3"
	/>
</td>
<td style="width: 50%">
	<input
		type="text"
		class="linkcontent"
		name="data[RelatedObject][link][{$objRelated.id}][title]"
		value="{$objRelated.title|escape|default:''}"
		style="width: 100%"
	/>
</td>
<td style="width: 50%">
	<input
		type="text"
		class="linkcontent"
		name="data[RelatedObject][link][{$objRelated.id}][url]"
		value="{$objRelated.url}"
		style="width: 100%"
	/>
</td>
<td class="commands">
	<a class="BEbutton" href="{$objRelated.url}" title="{t}open in new window{/t}" target="_blank">{t}open link{/t}</a>
	<a
		class="BEbutton golink"
		target="_blank"
		title="nickname:{$objRelated.nickname|default:''} id:{$objRelated.id}, {$objRelated.mime_type|default:''}"
		href="{$html->url('/')}webmarks/view/{$objRelated.id}"
	></a>
	<a class="BEbutton remove" title="{t}Remove{/t}">x</a>
	<input type="hidden" class="linkmod" name="data[RelatedObject][link][{$objRelated.id}][modified]" value="1" />
</td>
