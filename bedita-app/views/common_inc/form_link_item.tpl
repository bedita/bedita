
<input type="hidden" class="id" 	name="data[ObjectRelation][{$objRelated.id}][id]" value="{$objRelated.id|default:''}" />
<input type="hidden" class="switch" name="data[ObjectRelation][{$objRelated.id}][switch]" value="link" />
<input type="text" class="priority" name="data[ObjectRelation][{$objRelated.id}][priority]" value="{$objRelated.priority|default:1}" size="3" maxlength="3"/>

{t}Title{/t}: <a href="{$objRelated.url}" target="{$objRelated.target}">{$objRelated.title|default:''}</a>

{t}Url{/t}: {$objRelated.url|default:''}

{t}Target{/t}: {$objRelated.target|default:''}
<input type="button" value="{t}X{/t}" />

