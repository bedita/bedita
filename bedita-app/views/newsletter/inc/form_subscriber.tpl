{*
** subscriber form template
*}


{include file="../common_inc/form_common_js.tpl"}


<form action="{$html->url('/newsletter/savesubscriber')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>



<div class="tab"><h2>{t}Subscriber details{/t}</h2></div>

<fieldset id="subscriberdetails">	
		<table class="bordered">
		<tr>
			<th><label id="lemail" for="email">{t}Email{/t}</label></th>
			<td colspan="2"><input type="text" id="email" name="" value="" /></td>
		</tr>
		<tr>
			<th>Status</th>
			<td colspan="2">
				{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator="&nbsp;"}
			</td>
		</tr>
		<tr>
			<th>html</th>
			<td colspan="2">
				<input type="radio" name="html" value="Y"> yes
				&nbsp;&nbsp;
				<input type="radio" name="html" value="N"> no
			</td>
		</tr>
		<tr>
			<th>{t}In recipient groups{/t}:</th>
			<td colspan="2">
				<input type="checkbox">  gruppo uno
				<br />
				<input type="checkbox">  gruppo azione 2
				<br />
				<input type="checkbox"> group II
				<br />
				<input type="checkbox"> Quarto gruppo
			</td>
		</tr>
		<tr>
			<th>{t}Received Messages{/t}:</th>
			<td colspan="2">144</td>
		</tr>
		<tr>
			<th>{t}Subscribed on{/t}:</th>
			<td colspan="2">11 oct 2002</td>
		</tr>
		<tr>
			<th>{t}Addressbook name{/t}:</th>
			<td>Giovannone De'Cappelli stinti</td>
			<td>
				<input onClick="document.location.href='{$html->url('/addressbook/view/')}{$objects[i].id}'" type="button" value=" view address book detail">
			</td>
		</tr>
		<tr>
			<th>{t}Username{/t}:</th>
			<td><em>none</em></td>
			<td><input onclick="document.location.href='{$html->url('/admin/viewUser/')}'" type="button" value=" create account "></td>
		</tr>
		</table>
</fieldset>
	




</form>
	


