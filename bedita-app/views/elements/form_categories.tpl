{if !empty($areaCategory)}

{$relcount = $object.assocCategory|@count|default:0}

<div class="tab"><h2 {if empty($relcount)}class="empty"{/if}>{t}Categories{/t} &nbsp; {if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}</h2></div>

<fieldset id="category" >
<table class="bordered">
	{if !empty($areaCategory.noarea)}
		<tr>
			<th><h3 style="margin-left:-10px">{t}Generic categories{/t}</h3></th>
		</tr>
		{foreach item="noareaC" from=$areaCategory.noarea}
		<tr>
			<td>
			<input type="checkbox" id="cat_{$noareaC.id}" 
				name="data[Category][{$noareaC.id}]" value="{$noareaC.id}"
				{if $object && in_array($noareaC.id, $object.assocCategory)}checked="checked"{/if}/>
			<label for="cat_{$noareaC.id}">{$noareaC.label|escape}</label>
			</td>
		</tr>
		{/foreach}
	{/if}
			
	{if !empty($areaCategory.area)}
		{foreach key="areaName" item="areaC" from=$areaCategory.area}
			<tr>
				<td><h3 style="margin-left:-10px">{$areaName}</h3></td>
			</tr>
			{foreach from=$areaC item="cat" name="fc"}
			<tr>
				<td>
				<input type="checkbox" id="cat_{$cat.id}" 
					name="data[Category][{$cat.id}]" value="{$cat.id}"
					{if $object && in_array($cat.id, $object.assocCategory)}checked="checked"{/if}/>
				<label for="cat_{$cat.id}">{$cat.label|escape}</label>
				</td>
			</tr>
			{/foreach}
		{/foreach}
	
	{/if}
</table>
</fieldset>
{/if}