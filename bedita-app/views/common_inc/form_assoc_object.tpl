<div class="itemBox" style="height:80px;">
	<input type="hidden" name="index" value="{$objIndex|default:""}" />
	<input type="hidden" class="id" 	name="data[ObjectRelation][{$objIndex|default:""}][id]" value="{$objRelated.id|default:''}" />
	<input type="hidden" class="switch" name="data[ObjectRelation][{$objIndex|default:""}][switch]" value="{$rel|default:''}" />
	<div class="itemHeader">
		<input type="text" class="priority" name="data[ObjectRelation][{$objIndex|default:""}][priority]" value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
	</div>
	<div class="itemInfo">
		<div><span class="title">{t}Title{/t}: </span>{$objRelated.title|default:''}</div>
		<div><span class="title">{t}Type{/t}: </span>{$conf->objectTypeModels[$objRelated.object_type_id]|default:''}</div>
	</div>
	<div class="itemFooter">
		<input type="button" value="{t}X{/t}" />
	</div>
</div>
