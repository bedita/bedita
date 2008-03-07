<h2 class="showHideBlockButton">{t}Categories{/t}</h2>
<div class="blockForm" id="category" style="display: none">
<fieldset>
	<div id="categories_container">
	{if isset($categories)}
		<table class="tableForm" border="0">
		{foreach key=val item=cat from=$categories}
			<tr>
				<td class="field">
					<input type="checkbox" id="cat_{$cat.ObjectCategory.id}" 
						name="cat_{$cat.ObjectCategory.id}"/></td>
				<td class="label"><label for="cat_{$cat.ObjectCategory.id}">{$cat.ObjectCategory.label}</label></td>
				<td class="status">&nbsp;</td>
			</tr>
		{/foreach}
		</table>
	{/if}
	</div>
</fieldset>
</div>