<div class="tab"><h2>Config and messages</h2></div>
<fieldset id="configmessages">		
	<table class="bordered">
		<tr>
			<td colspan="2">{assign var='mailgroup_opting_method' value=$object.security|default:''}
				<label for="optingmethod">{t}subscribing method{/t}:</label>
				&nbsp;&nbsp;
				<select id="optingmethod" name="data[MailGroup][security]">
					<option value="none"{if $mailgroup_opting_method == 'none'} selected{/if}>Single opt-in (no confirmation required)</option>
					<option value="all"{if $mailgroup_opting_method == 'all'} selected{/if}>Double opt-in (confirmation required)</option>
				</select>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top">
				<label for="confirmin">{t}Confirmation-In mail message{/t}:</label>
				<br />
				<textarea name="data[MailGroup][confirmation_in_message]" id="optinmessage" style="width:220px" class="autogrowarea">{$object.confirmation_in_message|default:$default_confirmation_in_message}</textarea>
			</td>
			<td style="vertical-align:top">
				<label for="confirmout">{t}Confirmation-Out mail message{/t}:</label>
				<br />
				<textarea name="data[MailGroup][confirmation_out_message]" id="optoutmessage" style="width:220px" class="autogrowarea">{$object.confirmation_out_message|default:$default_confirmation_out_message}</textarea>
			</td>
		</tr>
	</table>
</fieldset>