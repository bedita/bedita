<fieldset>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Name{/t}:</td>
		<td class="field">
			<input type="text" name="data[label]" value="{$object.label|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Name is required (at least %1 alphanumerical char){/t}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Status{/t}:</td>
		<td class="field">
			{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	</table>
</fieldset>