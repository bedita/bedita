<script type="text/javascript" charset="utf-8">
$(document).ready ( function () {literal} { {/literal}

{if ($session->check('Message.error'))}

	{literal}
	$(".secondacolonna .modules label").addClass("error").attr("title","error");
	$("#messagesDiv")
		.show()							// fade in msg
		.click( function() {
			$(this).fadeOut('slow');	// fade out msg on click
		});
	{/literal}

{elseif ($session->check('Message.warn'))}

	{literal}
		$("#messagesDiv")
			.show()															// fade in msg
			//.pause( {/literal} {$conf->msgPause} {literal} )				// pause 4 secs
			.animate({opacity: 1.0}, {/literal}{$conf->msgPause}{literal}) 	// pause
			.fadeOut(1000);													// fade out msg

	{/literal}
	
{elseif ($session->check('Message.info'))}
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

	{if ($session->check('Message.error'))}
	<div class="message error">
		<h2>{t}Error{/t}</h2>
		<p>{$session->flash('error')}</p>
	</div>
	{/if}
	
	{if ($session->check('Message.warn'))}
	<div class="message warn">
		<h2>{t}Warning{/t}</h2>
		<p>{$session->flash('warn')}</p>
	</div>
	{/if}
	
	{if ($session->check('Message.info'))}
	<div class="message info">
		<h2>{t}Notice{/t}</h2>
		<p>{$session->flash('info')}</p>
	</div>
	{/if}
	
</div>
