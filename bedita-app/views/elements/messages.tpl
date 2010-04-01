<script type="text/javascript" charset="utf-8">
$(document).ready ( function () {literal} { {/literal}

{if ($session->check('Message.error'))}

	$(".secondacolonna .modules label").addClass("error").attr("title","error");
	$("#messagesDiv").triggerMessage("error");

{elseif ($session->check('Message.warn'))}

	$("#messagesDiv").triggerMessage("warn", {$conf->msgPause});
	
{elseif ($session->check('Message.info'))}

	$("#messagesDiv").triggerMessage("info", {$conf->msgPause});

{/if}

{literal}

});
{/literal}
</script>



<div id="messagesDiv">
	{if $session->flash('info')}
	{elseif $session->flash('warn')}
	{elseif $session->flash('error')}
	{/if}
</div>
