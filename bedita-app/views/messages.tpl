<script type="text/javascript" charset="utf-8">
$(document).ready ( function () {literal} { {/literal}

{if ($msg->check('Message.error'))}

	{literal}
	$("#messagesDiv")
		.show()							// fade in msg
		.click( function() {
			$(this).fadeOut('slow');	// fade out msg on click
		});
	{/literal}

{elseif ($msg->check('Message.warn'))}


{elseif ($msg->check('Message.info'))}
	{literal}
		$("#messagesDiv")
			.show()												// fade in msg
			.pause( {/literal} {$conf->msgPause} {literal} )	// pause 4 secs
			.fadeOut(1000);										// fade out msg

	{/literal}
{/if}

{literal}
	// hover for all messages
	$("#messagesDiv").hover(
		function() {
			$(this).addClass('messagesDivOver');
		},
		function() {
			$(this).removeClass('messagesDivOver');
		}
	);
})
{/literal}
</script>


<div id="messagesDiv">
	{if ($msg->check('Message.error'))}
	<div class="message-error">
		<div id="error-img"><span></span></div>
		{* the word error already appear in msgs <span id="err-msg">{t}Error{/t}</span>*}
		<p>{$msg->userMsg('error')|capitalize}</p>
	</div>
	{/if}
	{if ($msg->check('Message.warn'))}
	<div class="message-warn">
		<div id="warn-img"><span></span></div>
		<span id="warn-msg">{t}Warning{/t}</span>
		<p>{$msg->userMsg('warn')}</p>
	</div>
	{/if}
	{if ($msg->check('Message.info'))}
	<div class="message-info">
		<div id="info-img"><span></span></div>
		<span id="info-msg">{*t}Info{/t*}</span>
		<p>{$msg->userMsg('info')|capitalize}</p>
	</div>
	{/if}
</div>