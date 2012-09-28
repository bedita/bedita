<script type="text/javascript" charset="utf-8">
$(document).ready ( function () { 

{if !empty($id)}
	$("input[name=data\\[id\\]]").attr('value', {$id});
{/if}

{if ($this->Session->check('Message.error'))}

	$(".secondacolonna .modules label").addClass("error").attr("title","error");
	$("#messagesDiv").triggerMessage("error");

{elseif ($this->Session->check('Message.warn'))}

	$("#messagesDiv").triggerMessage("warn", {$conf->msgPause});
	
{elseif ($this->Session->check('Message.info'))}

	$("#messagesDiv").triggerMessage("info", {$conf->msgPause});

{/if}

});

</script>

{if $this->Session->check('Message.info')}
	{$this->Session->flash('info')}
{elseif $this->Session->check('Message.warn')}
	{$this->Session->flash('warn')}
{elseif $this->Session->check('Message.error')}
	{$this->Session->flash('error')}
{/if}
