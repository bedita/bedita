<script type="text/javascript" charset="utf-8">
$(document).ready ( function () {literal} { {/literal}

{if !empty($id)}
	$("input[name=data\\[id\\]]").attr('value', {$id});
{/if}

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

{if $session->check('Message.info')}
	{$session->flash('info')}
{elseif $session->check('Message.warn')}
	{$session->flash('warn')}
{elseif $session->check('Message.error')}
	{$session->flash('error')}
{/if}
