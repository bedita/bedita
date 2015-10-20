{if !empty($objectProperty)}

<div class="tab"><h2>{t}Custom Properties{/t}</h2></div>
<fieldset id="customProperties">
	
	<table class="indexlist" id="frmCustomProperties">
	<thead>
		<tr>
			<th>{t}name{/t}:</th>
			<th>{t}value{/t}:</th>
		</tr>
	</thead>
	{assign var="countProperty" value="0"}
	{foreach from=$objectProperty item="prop"}
		<tr>
			<td>{$prop.name}</td>
			{if $prop.property_type == "options"}
	
				{if $prop.multiple_choice == 0}
					<td>
					<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_type]" value="{$prop.property_type}" />
					<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_id]" value="{$prop.id}" />
					<select name="data[ObjectProperty][{$countProperty++}][property_value]">
					<option value="">--</option>
					{foreach from=$prop.PropertyOption item="opt"}
						<option value="{$opt.property_option}"{if $opt.selected|default:false} selected="selected"{/if}>{$opt.property_option}</option>
					{/foreach}
					</select>		
					</td>
	
				{else}
					
					<td>
					{foreach from=$prop.PropertyOption item="choice"}
						<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_type]" value="{$prop.property_type}" />
						<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_id]" value="{$prop.id}" />
						<input type="checkbox" name="data[ObjectProperty][{$countProperty++}][property_value]" value="{$choice.property_option}"{if $choice.selected|default:false} checked="checked"{/if} />
						{$choice.property_option}<br/>
					{/foreach}
					</td>
					
				{/if}
				
			{elseif $prop.property_type == "text"}
				<td>
					<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_type]" value="{$prop.property_type}" />
					<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_id]" value="{$prop.id}" />
					<textarea name="data[ObjectProperty][{$countProperty++}][property_value]" class="autogrowarea" style="overflow: hidden; width: 320px; line-height:1.2em;">{$prop.value.property_value|default:""|escape}</textarea>
				</td>
			{elseif $prop.property_type == "number"}
				<td>
					<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_type]" value="{$prop.property_type}" />
					<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_id]" value="{$prop.id}" />
					<input type="text" class="numberinput" name="data[ObjectProperty][{$countProperty++}][property_value]" value="{$prop.value.property_value|default:""}" />
				</td>	
			{elseif $prop.property_type == "date"}
				<td>
					<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_type]" value="{$prop.property_type}" />
					<input type="hidden" name="data[ObjectProperty][{$countProperty}][property_id]" value="{$prop.id}" />
					<input size="10" type="text" style="vertical-align:middle"
						class="dateinput" name="data[ObjectProperty][{$countProperty++}][property_value]" 
						value="{$prop.value.property_value|default:""|date_format:$conf->datePattern}" />			
				</td>
			{/if}
		</tr>
	
	{/foreach} 
	</table>
	

</fieldset>
{/if}