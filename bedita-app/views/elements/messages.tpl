{if $smarty.const.BACKEND_APP}
<script type="text/javascript" charset="utf-8">
$(document).ready ( function ()  { 
{if ($session->check('Message.info'))}
	$("#messagesDiv").triggerMessage("info", {$conf->msgPause});
{/if}
{if ($session->check('Message.warn'))}
	$("#messagesDiv").triggerMessage("warn", {$conf->msgPause});
{/if}	
{if ($session->check('Message.error'))}
	$("#messagesDiv").triggerMessage("error");
{/if}

});
</script>
{/if}

<div id="messagesDiv">

	{if $session->check('Message.info')}
		{$session->flash('info')}
	{/if}
	{if $session->check('Message.warn')}
		{$session->flash('warn')}
	{/if}
	{if $session->check('Message.error')}
		{$session->flash('error')}
	{/if}
	
</div>