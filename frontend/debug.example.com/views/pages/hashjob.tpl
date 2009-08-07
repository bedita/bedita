{if ($msg->check('Message.info'))}	
	<div class="message info">
		{$msg->userMsg('info')}
	</div>
{elseif ($msg->check('Message.error'))}
	<div class="message error">
		{$msg->userMsg('error')}
	</div>
{/if}