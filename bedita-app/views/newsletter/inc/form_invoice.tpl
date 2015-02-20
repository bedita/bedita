<script type="text/javascript">
var sendNewsletterUrl = "{$html->url('/newsletter/sendNewsletter')}";
var testNewsletterUrl = "{$html->url('/newsletter/testNewsletter')}";

$(document).ready(function() {
	$("#sendNewsletter").click(function() {
		$("#updateForm").prop("action", sendNewsletterUrl).submit();
	});
	
	$("#testNewsletter").click(function() {
		to = prompt("{t}Send email to{/t}");
		$("#updateForm").prop("action", testNewsletterUrl + "/" + to);
		$("#updateForm").submit();
	});
});

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
			<input size=10 {if ($object.mail_status == "sent")}disabled=1{/if} type="text" class="dateinput" name="data[start_sending]" id="eventStart" value="{if !empty($object.start_sending)}{$object.start_sending|date_format:$conf->datePattern}{/if}"/>
			<input size=5 {if ($object.mail_status == "sent")}disabled=1{/if} type="text" id="timeStart" name="data[start_sending_time]" value="{if !empty($object.start_sending)}{$object.start_sending|date_format:"%H:%M"}{/if}" />

		</td>
		<td>
		{if !empty($groupsByArea)}
			{foreach from=$groupsByArea item="groups" key="pub"}
				<ul>
					<li style="padding:2px;">
						<b>{$pub|escape|upper}</b>
						<ul style="margin:0px">
						{foreach from=$groups item="group" name="fc"}
							<li style="padding:2px;">
							<input type="checkbox" 
							{if ($object.mail_status == "sent")}disabled=1{/if}
							name="data[MailGroup][]" value="{$group.id}"{if !empty($group.MailMessage)} checked{/if}/> {$group.group_name|escape}
							</li>
						{/foreach}
						</ul>
					</li>
				</ul>
			{/foreach}
		{/if}
		</td>
		
		{if !empty($object.mail_status) && $object.mail_status == "injob"}
			<td style="color:red; text-decoration: blink;">{t}in job{/t}</td>
		{else}
			<td class="info">{t}{$object.mail_status|default:''}{/t}</td>
		{/if}
		
	</tr>
</table>
	<div style="background-color:#FFF; padding:20px; text-align:center">
		<input type="button" id="testNewsletter" value="  test newsletter  " {if !($object.id|default:false)}disabled="disabled"{/if}/> 
		{if (empty($object) || ($object.mail_status!='sent' && $object.mail_status != 'injob'))}
		&nbsp;&nbsp;
		{if ($object.mail_status == "sent")}
			<p style="color:#FFF; padding:4px">
			{t}Newsletter sent. To schedule another invoice, please clone this object.{/t}
			</p>
		{else}
			<input type="button" id="sendNewsletter" value="  SAVE & QUEUE newsletter  " />
		{/if}
		
		{/if}
	</div>
	
</fieldset>

{*dump var=$object*}