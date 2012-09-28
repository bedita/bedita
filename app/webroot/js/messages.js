/**
 * Messages js file
 * @author ChannelWeb srl
 */

<script type="text/javascript" charset="utf-8">
{if ($session->check('Message.info'))}

	{literal}
	$(document).ready ( function () {
		$("#messagesDiv")
			.fadeIn('slow')										// fade in msg
			.click( function() {
				$(this).fadeOut('slow');
			});
	});
	{/literal}

{elseif ($session->check('Message.warn'))}


{elseif ($session->check('Message.error'))}

	{literal}
	$(document).ready ( function () {
		$("#messagesDiv")
			.hide()												// hide
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
