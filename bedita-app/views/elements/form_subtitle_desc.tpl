<div class="tab"><h2>{t}Subtitle, description{/t}</h2></div>

<fieldset>
	<div id="subtitle_langs_container" class="tabsContainer">
		
		<table>
		<tr>
			<th>{t}description{/t}:</th>
			<td>
				<textarea style="width:320px; min-height:16px;" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
			</td>
		</tr>
		</table>
		
	</div>
</fieldset>
