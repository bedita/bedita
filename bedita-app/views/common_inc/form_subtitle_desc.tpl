<div class="tab"><h2>{t}Subtitle, description{/t}</h2></div>

<fieldset>
	<div id="subtitle_langs_container" class="tabsContainer">
		
		<table class="tableForm" border="0">
		<tr>
			<td class="label">{t}Description{/t}:</td>
			<td class="field">
				<textarea class="shortdesc" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
			</td>
		</tr>
		</table>
		
	</div>
	
</fieldset>
