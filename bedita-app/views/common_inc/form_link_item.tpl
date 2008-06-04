<div class="itemBox" style="height:80px; width:180px;">
	<input type="hidden" name="index" value="" />
	<input type="hidden" class="id" 	name="data[ObjectRelation][][id]" value="{$objRelated.id|default:''}" />
	<input type="hidden" class="switch" name="data[ObjectRelation][][switch]" value="link" />
	<div class="itemHeader">
		<input type="text" class="priority" name="data[ObjectRelation][][priority]" value="{$objRelated.priority|default:1}" size="3" maxlength="3"/>
	</div>
	<div class="itemInfo">
		<div><span class="title">{t}Title{/t}: </span><a href="{$objRelated.url}" target="{$objRelated.target}">{$objRelated.title|default:''}</a></div>
		<div><span class="title">{t}Url{/t}: </span>{$objRelated.url|default:''}</div>
		<div><span class="title">{t}Type{/t}: </span>{$objRelated.target|default:''}</div>
	</div>
	<div class="itemFooter">
		<input type="button" value="{t}X{/t}" />
	</div>
</div>
