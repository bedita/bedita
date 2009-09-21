<script type="text/javascript">
var sendNewsletterUrl = "{$html->url('/newsletter/sendNewsletter')}";
var testNewsletterUrl = "{$html->url('/newsletter/testNewsletter')}";
{literal}
$(document).ready(function() {
	$("#sendNewsletter").click(function() {
		$("#updateForm").attr("action", sendNewsletterUrl).submit();
	});
	
	$("#testNewsletter").click(function() {
		to = prompt("{/literal}{t}Send email to{/t}{literal}");
		$("#updateForm").attr("action", testNewsletterUrl + "/" + to);
		$("#updateForm").submit();
	});
});
{/literal}
</script>


<div class="tab"><h2>{t}Invoice{/t}</h2></div>

<fieldset id="invoice">			

<table class="bordered" style="width:100%">

	<tr>
		<th>{t}start{/t}:</th>
		<th>{t}to recipients{/t}:</th>
		<th>{t}status{/t}:</th>
	</tr>
	<tr>
		<td style="vertical-align:middle">
	<input size=10 type="text" class="dateinput" name="data[start_sending]" id="eventStart" value="{if !empty($object.start_sending)}{$object.start_sending|date_format:$conf->datePattern}{/if}"/>
	<input size=5 type="text" id="timeStart" name="data[start_sending_time]" value="{if !empty($object.start_sending)}{$object.start_sending|date_format:"%H:%M"}{/if}" />

		</td>
		<td>
		{if !empty($groupsByArea)}
			{foreach from=$groupsByArea item="groups" key="pub"}
				<ul>
					<li style="padding:2px;">
						<b>{$pub|upper}</b>
						<ul style="margin:0px">
						{foreach from=$groups item="group" name="fc"}
							<li style="padding:2px;">
							<input type="checkbox" name="data[MailGroup][]" value="{$group.id}"{if !empty($group.MailMessage)} checked{/if}/> {$group.group_name}
							</li>
						{/foreach}
						</ul>
					</li>
				</ul>
			{/foreach}
		{/if}
		</td>
		
		{if !empty($object.mail_status) && $object.mail_status == "injob"}
			<td style="color:red; text-decoration: blink;">{t} in job{/t}</td>
		{else}
			<td class="info">{t}{$object.mail_status|default:''}{/t}</td>
		{/if}
		
	</tr>
</table>
	<div class="modalcommands newsletter">
		<input type="button" id="testNewsletter" value="  test newsletter  " {if !($object.id|default:false)}disabled="disabled"{/if}/> 
		{if (empty($object) || ($object.mail_status!='sent' && $object.mail_status != 'injob'))}
		&nbsp;&nbsp;
		<input type="button" id="sendNewsletter" value="  SAVE & QUEUE newsletter  " />
		{/if}
	</div>
	
</fieldset>

{*dump var=$object*}