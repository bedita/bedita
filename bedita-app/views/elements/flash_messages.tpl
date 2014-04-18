<script type="text/javascript" charset="utf-8">
$(document).ready(function() { 

{if !empty($id)}
	$("input[name=data\\[id\\]]").val({$id});
{/if}

{if ($session->check('Message.error'))}

	$(".secondacolonna .modules label").addClass("error").prop("title", "error");
	$("#messagesDiv").triggerMessage("error");

{elseif ($session->check('Message.warn'))}

	$("#messagesDiv").triggerMessage("warn", {$conf->msgPause});
	
{elseif ($session->check('Message.info'))}

	$("#messagesDiv").triggerMessage("info", {$conf->msgPause});

{/if}

});

</script>

{if $session->check('Message.info')}
	{$session->flash('info')}
{elseif $session->check('Message.warn')}
	{$session->flash('warn')}
{elseif $session->check('Message.error')}
	{$session->flash('error')}
{/if}
