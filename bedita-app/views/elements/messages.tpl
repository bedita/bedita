<script type="text/javascript" charset="utf-8">
$(document).ready ( function () {literal} { {/literal}

{if ($msg->check('Message.error'))}

	{literal}
	$(".secondacolonna .modules label").addClass("error").attr("title","error");
	$("#messagesDiv")
		.show()							// fade in msg
		.click( function() {
			$(this).fadeOut('slow');	// fade out msg on click
		});
	{/literal}

{elseif ($msg->check('Message.warn'))}

	{literal}
		$("#messagesDiv")
			.show()															// fade in msg
			//.pause( {/literal} {$conf->msgPause} {literal} )				// pause 4 secs
			.animate({opacity: 1.0}, {/literal}{$conf->msgPause}{literal}) 	// pause
			.fadeOut(1000);													// fade out msg

	{/literal}
	
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
			//$(this).addClass('messagesDivOver');
			$(this).fadeOut('slow')
		},
		function() {
			//$(this).removeClass('messagesDivOver');
		}
	);
})
{/literal}
</script>



<div id="messagesDiv">

	{if ($msg->check('Message.error'))}
	<div class="message error">
		<h2>{t}Error{/t}</h2>
		<p>{$msg->userMsg('error')}</p>
	</div>
	{/if}
	
	{if ($msg->check('Message.warn'))}
	<div class="message warn">
		<h2>{t}Warning{/t}</h2>
		<p>{$msg->userMsg('warn')}</p>
	</div>
	{/if}
	
	{if ($msg->check('Message.info'))}
	<div class="message info">
		<h2>{t}Notice{/t}</h2>
		<p>{$msg->userMsg('info')}</p>
	</div>
	{/if}
	
</div>
