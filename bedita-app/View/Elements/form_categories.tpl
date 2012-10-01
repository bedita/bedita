{if !empty($areaCategory)}

<div class="tab"><h2>{t}Categories{/t}</h2></div>

<fieldset id="category" >
	
<table class="bordered">
		
	{if !empty($areaCategory.area)}
		{foreach key="areaName" item="areaC" from=$areaCategory.area}
			<tr>
				<td><h2 style="color:white; margin-left:-10px">{$areaName}</h2></td>
			</tr>
			{foreach from=$areaC item="cat" name="fc"}
			<tr>
				<td>
				<input type="checkbox" id="cat_{$cat.id}" 
					name="data[Category][{$cat.id}]" value="{$cat.id}"
					{if $object && in_array($cat.id, $object.assocCategory)}checked="checked"{/if}/>
				<label for="cat_{$cat.id}">{$cat.label}</label>
				</td>
			</tr>
			{/foreach}
		{/foreach}
	
	{/if}
	
	{if !empty($areaCategory.noarea)}
			
			<tr>
				<th><h2 style="color:white; margin-left:-10px">{t}Generic categories{/t}</h2></th>
			</tr>
			{foreach item="noareaC" from=$areaCategory.noarea}
			<tr>
				<td>
				<input type="checkbox" id="cat_{$noareaC.id}" 
					name="data[Category][{$noareaC.id}]" value="{$noareaC.id}"
					{if $object && in_array($noareaC.id, $object.assocCategory)}checked="checked"{/if}/>
				<label for="cat_{$noareaC.id}">{$noareaC.label}</label>
				</td>
			</tr>
			{/foreach}
		{/if}

</table>

</fieldset>

{/if}