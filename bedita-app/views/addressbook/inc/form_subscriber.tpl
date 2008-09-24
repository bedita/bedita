{*
** subscriber form template
*}


{include file="../common_inc/form_common_js.tpl"}


<form action="{$html->url('/newsletter/saveSubscriber')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[MailAddress][id]" value="{$subscriber.MailAddress.id|default:''}"/>



<div class="tab"><h2>{t}Subscriber details{/t}</h2></div>

<fieldset id="subscriberdetails">	
		<table class="bordered">
		<tr>
			<th><label id="lemail" for="email">{t}Email{/t}</label></th>
			<td colspan="2"><input type="text" id="email" name="data[MailAddress][email]" value="{$subscriber.MailAddress.email|default:''}" /></td>
		</tr>
		<tr>
			<th>Status</th>
			<td colspan="2">
				<input type="radio" name="data[MailAddress][status]" value="valid"{if $subscriber.MailAddress.status|default:"valid" == "valid"} checked{/if} /> {t}valid{/t}
				<input type="radio" name="data[MailAddress][status]" value="blocked"{if $subscriber.MailAddress.status|default:"valid" == "blocked"} checked{/if} /> {t}blocked{/t} 
			</td>
		</tr>
		<tr>
			<th>html</th>
			<td colspan="2">
				<input type="radio" name="data[MailAddress][html]" value="1"{if $subscriber.MailAddress.html|default:1 == 1} checked{/if}> {t}yes{/t}
				&nbsp;&nbsp;
				<input type="radio" name="data[MailAddress][html]" value="0"{if $subscriber.MailAddress.html|default:1 == 0} checked{/if}> {t}no{/t}
			</td>
		</tr>
		<tr>
			<th>{t}In recipient groups{/t}:</th>
			<td colspan="2">
			{if !empty($groupsByArea)}
				{assign var="index" value=0}
				{foreach from=$groupsByArea item="groups" key="pub"}
					<ul>
					{$pub}
					
					{foreach from=$groups item="group" name="fc"}
						<li>
						<input type="checkbox" name="data[joinGroup][{$index}][mail_group_id]" value="{$group.id}"{if !empty($group.subscribed)} checked{/if}/> {$group.group_name}
						<input type="hidden" name="data[joinGroup][{$index}][id]" value="{$group.MailGroupAddress.id|default:""}" />
						<input type="hidden" name="data[joinGroup][{$index}][status]" value="confirmed" />
						<input type="hidden" name="data[joinGroup][{$index}][command]" value="{$group.MailGroupAddress.command|default:"confirm"}" />
						<input type="hidden" name="data[joinGroup][{$index}][hash]" value="{$group.MailGroupAddress.hash|default:""}" />
						<input type="hidden" name="data[joinGroup][{$index++}][created]" value="{$group.MailGroupAddress.created|default:""}" />
						</li>
					{/foreach}
					
					</ul>
				{/foreach}
			{/if}
			</td>
		</tr>
		<tr>
			<th>{t}Received Messages{/t}:</th>
			<td colspan="2">144</td>
		</tr>
		<tr>
			<th>{t}Subscribed on{/t}:</th>
			<td colspan="2">{$subscriber.MailAddress.created|date_format:$conf->datePattern|default:''}</td>
		</tr>
		<tr>
			<th>{t}Addressbook name{/t}:</th>
			{if !empty($subscriber.Card.id)}
			<td>
				{$subscriber.Card.name} {$subscriber.Card.surname}
			</td>
			<td>
				<input onClick="document.location.href='{$html->url('/addressbook/view/')}{$subscriber.MailAddress.card_id}'" type="button" value=" view address book detail">
			</td>
			{else}
			 <td colspan="2">
				<em>none</em>
			</td>
			{/if}
			
		</tr>
		<tr>
			<th>{t}Username{/t}:</th>
			{if !empty($subscriber.User.id)}
			<td colspan="2">
				{$subscriber.User.userid}
			</td>
			{else}
			<td><em>none</em></td>
			<td><input onclick="document.location.href='{$html->url('/admin/viewUser/')}'" type="button" value=" create account "></td>
			{/if}
			
		</tr>
		</table>
</fieldset>
	




</form>
	


