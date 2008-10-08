<table class="bordered" style="width:100%" id="">
	<tr>
		<td>sender email</td>
		<td><input type="text" name="data[sender]" value="{$object.sender|default:null}"/></td>
	</tr>
	<tr>
		<td>bounce to email</td>
		<td><input type="text" name="data[bounce_to]" value="{$object.bounce_to|default:null}" /></td>
	</tr>
	<tr>
		<td>priority</td>
		<td><input type="text" value="{$object.priority|default:null}" /></td>
	</tr>
	<tr>
		<td>signature:</td>
		<td>	
			<textarea name="data[signature]" style="width:340px" class="autogrowarea">{$object.signature|default:null}</textarea>
		</td>
	</tr>
</table>