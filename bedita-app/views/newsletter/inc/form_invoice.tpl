{$html->css('jquery.timepicker', null, ['inline' => false])}
{$html->script('libs/jquery/plugins/jquery.timepicker.min')}


<script type="text/javascript">
var sendNewsletterUrl = "{$html->url('/newsletter/sendNewsletter')}";
var testNewsletterUrl = "{$html->url('/newsletter/testNewsletter')}";

var messageNow = "{t}now{/t}" ;
var sendNowAlert = "{t}Do you want to save and SEND this newsletter now? (mails are being sent as soon as possible){/t}" ;


$(document).ready(function() {

    // calendar events 
    var timePickerOptions = {
        minTime: '00:00',
        maxTime: '23:30',
        timeFormat: 'G:i'
    }
	$('#timeStart').timepicker(timePickerOptions);

	// set correct send message in button at start
	var currentMsg = $('#sendNewsletter').val();
	var changeSendButton = function() {
		if (($( "input[name='data[start_sending]']" ).val() == "") || ($( "input[name='data[start_sending_time]']" ).val() == "")) {
			$('#sendNewsletter').val(currentMsg + " " + messageNow);
		} else {
			var msg = $('#sendNewsletter').val();
			$('#sendNewsletter').val(msg.replace(" " + messageNow, ""));
		}
	}
	changeSendButton();


	$( "input[name='data[start_sending]']" ).change(function() {
		changeSendButton();
	});

	$( "input[name='data[start_sending_time]']" ).change(function() {
		changeSendButton();
	});


	$("#sendNewsletter").click(function() {

		if ($("input[name='data[sender]']").val() == '') {
			alert("{t}Missing sender email{/t}");
			return false;
		}

		// confirm and set datetime now for send
		if (($( "input[name='data[start_sending]']" ).val() == "") && ($( "input[name='data[start_sending_time]']" ).val() == "")) {

			// prepare current time & date
			var roundMinutes = 1000 * 60 * 5; // round 5 minutes
			var d = new Date();
			var d = new Date((Math.floor(d.getTime() / roundMinutes) * roundMinutes) + roundMinutes);
			var dString = ("0" + d.getDate()).slice(-2) + '/' + ("0" + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
			var tString = ("0" + d.getHours()).slice(-2) + ':' +("0" + d.getMinutes()).slice(-2);

			var r = confirm(sendNowAlert);
			if (r == true) {
				$('#eventStart').val(dString);
				$('#timeStart').val(tString);
				$("#updateForm").prop("action", sendNewsletterUrl).submit();
			}
		} else if ($( "input[name='data[start_sending_time]']" ).val() == "") {
			alert("{t}Missing send time{/t}");
			return false;
		} else {
			$("#updateForm").prop("action", sendNewsletterUrl).submit();
		}
	});
	
	// enable save and send button if at least one mailgroup is checked
	var mailGroupCheckboxes = $("input[name='data[MailGroup][]']");
	mailGroupCheckboxes.click(function() {
		$('#sendNewsletter').attr("disabled", !mailGroupCheckboxes.is(":checked"));
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

	<table class="bordered">

		<tr>
			<th>{t}schedule date and time{/t}:</th>
			<th>{t}to recipients{/t}:</th>
			<th>{t}status{/t}:</th>
		</tr>
		<tr>
			<td>
				<input maxlength="10" {if ($object.mail_status == "sent")}disabled=1{/if} type="text" class="dateinput" name="data[start_sending]" id="eventStart" value="{if !empty($object.start_sending)}{$object.start_sending|date_format:$conf->datePattern}{/if}"/>
				<input maxlength="5" ="5" {if ($object.mail_status == "sent")}disabled=1{/if} type="text" id="timeStart" name="data[start_sending_time]" value="{if !empty($object.start_sending)}{$object.start_sending|date_format:"%H:%M"}{/if}" />
			</td>
			<td>
			{if !empty($groupsByArea)}
				{foreach from=$groupsByArea item="groups" key="pub"}
					<ul>
						<li>
							<b>{$pub|escape|upper}</b>
							<ul>
							{foreach from=$groups item="group" name="fc"}
								<li>
								<input type="checkbox" id="mailgroup{$group.id}"
								{if ($object.mail_status == "sent")}disabled=1{/if}
								name="data[MailGroup][]" value="{$group.id}"{if !empty($group.MailMessage)} checked{/if}/> <label for="mailgroup{$group.id}">{$group.group_name|escape}</label>
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

	<div class="sendbuttons">
		<input type="button" id="testNewsletter" value="{t}Test newsletter{/t}" {if !($object.id|default:false)}disabled="disabled"{/if}/> 
		{if (empty($object) || ($object.mail_status!='sent' && $object.mail_status != 'injob'))}
		&nbsp;&nbsp;
		{if ($object.mail_status == "sent")}
			<p style="color:#FFF; padding:4px">
			{t}Newsletter sent. To schedule another invoice, please clone this object.{/t}
			</p>
		{else}
			<input type="button" id="sendNewsletter" value="{t}Save & queue newsletter{/t}" disabled />
		{/if}
		
		{/if}
	</div>
	
</fieldset>

{*dump var=$object*}