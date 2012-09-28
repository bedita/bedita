<script type="text/javascript" charset="utf-8">
$(document).ready ( function ()  { 
{if ($this->Session->check('Message.info'))}
	$("#messagesDiv").triggerMessage("info", {$conf->msgPause});
{/if}
{if ($this->Session->check('Message.warn'))}
	$("#messagesDiv").triggerMessage("warn", {$conf->msgPause});
{/if}	
{if ($this->Session->check('Message.error'))}
	$("#messagesDiv").triggerMessage("error");
{/if}

});
</script>

<div id="messagesDiv">

	{if $this->Session->check('Message.info')}
		{$this->Session->flash('info')}
	{/if}
	{if $this->Session->check('Message.warn')}
		{$this->Session->flash('warn')}
	{/if}
	{if $this->Session->check('Message.error')}
		{$this->Session->flash('error')}
	{/if}
	
</div>