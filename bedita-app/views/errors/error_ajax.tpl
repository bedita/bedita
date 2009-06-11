{if !empty($json)}
	{if ($msg->check('Message.error'))}
		{assign_associative var="msgArr" errorMsg=$msg->userMsg('error')}
		{$javascript->object($msgArr)}
	{/if}
{else}
	<div style="padding-top:20px;">
		{if ($msg->check('Message.error'))}
			<p>{$msg->userMsg('error')}</p>
		{/if}
	</div>
{/if}