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
	{if $session->flash('info')}
	{elseif $session->flash('warn')}
	{elseif $session->flash('error')}
	{/if}
</div>
