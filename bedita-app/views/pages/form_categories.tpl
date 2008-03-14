<h2 class="showHideBlockButton">{t}Categories{/t}</h2>
<div class="blockForm" id="category" style="display: none">
<fieldset>
	<div id="categories_container">
	{if !empty($areaCategory)}
		<table class="indexList" style="width: 570px;">
		{if !empty($areaCategory.area)}
		{foreach key="areaName" item="areaC" from=$areaCategory.area}
			<tr>
				<th style="background-color:white; font-weight: bold; border:1px solid grey;">{$areaName}</th>
			</tr>
			{foreach from=$areaC item="cat" name="fc"}
			<tr>
				<td class="cellList">
				<input type="checkbox" id="cat_{$cat.id}" 
					name="data[ObjectCategory][]" value="{$cat.id}"
					{if $object && in_array($cat.id, $object.ObjectCategory)}checked="checked"{/if}/>
				<label for="cat_{$cat.id}">{$cat.label}</label>
				</td>
			</tr>
			{/foreach}
		{/foreach}
		{/if}
		{if !empty($areaCategory.noarea)}
			<tr>
				<td style="border: 0px; padding-top:15px;">{t}Categories with no area associated{/t}</td>
			</tr>
			{foreach item="noareaC" from=$areaCategory.noarea}
			<tr>
				<td class="cellList">
				<input type="checkbox" id="cat_{$noareaC.id}" 
					name="data[ObjectCategory][]" value="{$noareaC.id}"
					{if $object && in_array($noareaC.id, $object.ObjectCategory)}checked="checked"{/if}/>
				<label for="cat_{$noareaC.id}">{$noareaC.label}</label>
				</td>
			</tr>
			{/foreach}
		{/if}
		</table>
	{else}
		{t}No categories found{/t}
	{/if}
	</div>
</fieldset>
</div>