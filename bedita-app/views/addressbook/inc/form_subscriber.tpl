{*
** subscriber form template
*}


<div class="tab"><h2>{t}Newsletter subscriptions{/t}</h2></div>

<fieldset id="subscriberdetails">	
		<table class="bordered">
		<tr>
			<th>{t}in recipient groups{/t}:</th>
			<td colspan="2">
			{if !empty($groupsByArea)}
				{assign var="index" value=0}
				{foreach from=$groupsByArea item="groups" key="pub"}
					<ul>
					{$pub}
					
					{foreach from=$groups item="group" name="fc"}
						<li>
						<input type="checkbox" name="data[joinGroup][{$index}][mail_group_id]" value="{$group.id}"{if !empty($group.subscribed)} checked{/if}/> {$group.group_name}
						<input type="hidden" name="data[joinGroup][{$index}][id]" value="{$group.MailGroupCard.id|default:""}" />
						<input type="hidden" name="data[joinGroup][{$index}][status]" value="{$group.MailGroupCard.status|default:"confirmed" />
						<input type="hidden" name="data[joinGroup][{$index}][command]" value="{$group.MailGroupCard.command|default:"confirm"}" />
						<input type="hidden" name="data[joinGroup][{$index}][hash]" value="{$group.MailGroupCard.hash|default:""}" />
						<input type="hidden" name="data[joinGroup][{$index++}][created]" value="{$group.MailGroupCard.created|default:""}" />
						</li>
					{/foreach}
					
					</ul>
				{/foreach}
			{/if}
			</td>
		</tr>
		<tr>
			<th><label id="lemail" for="email">{t}with email{/t}</label></th>
			<td colspan="2"><input type="text" id="email" name="data[mail_email]" value="{$object.mail_email|default:''}" /></td>
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
		<tr>
			<th>{t}received Messages{/t}:</th>
			<td colspan="2">144</td>
		</tr>
		<tr>
			<th>{t}subscribed on{/t}:</th>
			<td colspan="2">{$object.created|date_format:$conf->datePattern|default:''}</td>
		</tr>
		</table>
</fieldset>
	


