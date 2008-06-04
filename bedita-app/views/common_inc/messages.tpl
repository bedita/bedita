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
			.show()															// fade in msg
			//.pause( {/literal} {$conf->msgPause} {literal} )				// pause 4 secs
			.animate({opacity: 1.0}, {/literal}{$conf->msgPause}{literal}) 	// pause
			.fadeOut(1000);													// fade out msg

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
	<div class="message error">
		<label>{t}Error{/t}</label>
		<p>{$msg->userMsg('error')}</p>
	</div>
	{/if}
	
	{if ($msg->check('Message.warn'))}
	<div class="message warn">
		<label>{t}Warning{/t}</label>
		<p>{$msg->userMsg('warn')}</p>
	</div>
	{/if}
	
	{if ($msg->check('Message.info'))}
	<div class="message info">
		<label>{t}Notice{/t}</label>
		<p>{$msg->userMsg('info')}</p>
	</div>
	{/if}
	
</div>
