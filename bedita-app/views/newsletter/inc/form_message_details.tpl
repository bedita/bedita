<table class="bordered" style="width:100%" id="">
	<tr>
		<td>{t}sender email{/t}</td>
		<td><input type="text" name="data[sender]" value="{$object.sender|default:null}"/></td>
	</tr>
	<tr>
		<td>{t}reply to{/t}</td>
		<td><input type="text" name="data[reply_to]" value="{$object.reply_to|default:null}" /></td>
	</tr>
	<tr>
		<td>{t}bounce to email{/t}</td>
		<td><input type="text" name="data[bounce_to]" value="{$object.bounce_to|default:null}" /></td>
	</tr>
	<tr>
		<td>{t}priority{/t}</td>
		<td><input type="text" value="{$object.priority|default:null}" /></td>
	</tr>
	<tr>
		<td>{t}signature{/t}:</td>
		<td>	
			<textarea name="data[signature]" style="width:340px" class="autogrowarea">{$object.signature|default:null}</textarea>
		</td>
	</tr>
	<tr>
		<td>{t}privacy disclaimer{/t}:</td>
		<td>	
			<textarea name="data[privacy_disclaimer]" style="width:340px" class="autogrowarea">{$object.privacy_disclaimer|default:null}</textarea>
		</td>
	</tr>
</table>