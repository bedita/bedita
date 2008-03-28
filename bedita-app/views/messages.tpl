<script type="text/javascript" charset="utf-8">
{if ($msg->check('Message.error'))}

	{literal}
	$(document).ready ( function () {
		$("#messagesDiv")
			.fadeIn('slow')										// fade in msg
			.click( function() {
				$(this).fadeOut('slow');
			});
	});
	{/literal}

{elseif ($msg->check('Message.warn'))}


{elseif ($msg->check('Message.info'))}

	{literal}
	$(document).ready ( function () {
		$("#messagesDiv")
			.fadeTo(1000, 0.8)									// fade in msg
			.pause( {/literal} {$conf->msgPause} {literal} )	// pause 4 secs
			.fadeOut(1000);										// fade out msg

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
{/if}
</script>

<div id="messagesDiv" style="">
	{if ($msg->check('Message.error'))}
	<div class="message-error">
		<span id="error-img">&#160;&#160;&#160;</span> <span id="err-msg">{t}Error{/t}</span>
		<p>{$msg->userMsg('error')}</p>
	</div>
	{/if}
	{if ($msg->check('Message.warn'))}
	<div class="message-warn">
		<span id="warn-img">&#160;&#160;&#160;</span> <span id="warn-msg">{t}Warning{/t}</span>
		<p>{$msg->userMsg('warn')}</p>
	</div>
	{/if}
	{if ($msg->check('Message.info'))}
	<div class="message-info">
		<div id="info-img">&#160;&#160;&#160;</div> <span id="info-msg">{*t}Info{/t*}</span>
		<p>{$msg->userMsg('info')|capitalize}</p>
	</div>
	{/if}
</div>