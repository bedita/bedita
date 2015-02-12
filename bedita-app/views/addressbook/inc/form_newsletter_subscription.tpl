{*
** subscriber form template
*}

{if isset($moduleList.newsletter) && $moduleList.newsletter.status == "on"}
<div class="tab"><h2>{t}Newsletter subscriptions{/t}</h2></div>

<fieldset id="subscriberdetails">	
		<table class="bordered">
		<tr>
			<th>{t}in recipient groups{/t}:</th>
			<td colspan="2">
			{if !empty($groupsByArea)}
				{foreach from=$groupsByArea item="groups" key="pub"}
					<ul>
					{$pub|escape}
					{foreach from=$groups item="group" name="fc"}
					{assign var="index" value=$smarty.foreach.fc.index}
						<li>
						<input type="checkbox" name="data[joinGroup][{$index}][mail_group_id]" value="{$group.id}"{if !empty($group.subscribed)} checked{/if}/> {$group.group_name|escape}
						<input type="hidden" name="data[joinGroup][{$index}][id]" value="{$group.MailGroupCard.id|default:""}" />
						<input type="hidden" name="data[joinGroup][{$index}][status]" value="{$group.MailGroupCard.status|default:"confirmed"}" />
						</li>
					{/foreach}
					
					</ul>
				{/foreach}
			{/if}
			</td>
		</tr>
		<tr>
			<th><label id="lemail" for="email">{t}with email{/t}</label></th>
			<td colspan="2"><input type="text" id="email" name="data[newsletter_email]" value="{$object.newsletter_email|default:''}" /></td>
		</tr>
		<tr>
			<th>status</th>
			<td colspan="2">
				<input type="radio" name="data[mail_status]" value="valid"{if $object.mail_status|default:"valid" == "valid"} checked{/if} /> {t}valid{/t}
				<input type="radio" name="data[mail_status]" value="blocked"{if $object.mail_status|default:"valid" == "blocked"} checked{/if} /> {t}blocked{/t} 
			</td>
		</tr>
		<tr>
			<th>html</th>
			<td colspan="2">
				<input type="radio" name="data[mail_html]" value="1"{if $object.mail_html|default:1 == 1} checked{/if}> {t}yes{/t}
				&nbsp;&nbsp;
				<input type="radio" name="data[mail_html]" value="0"{if $object.mail_html|default:1 == 0} checked{/if}> {t}no{/t}
			</td>
		</tr>
		</table>
</fieldset>
{/if}


