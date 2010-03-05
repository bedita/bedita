{if !empty($json)}
	{if ($session->check('Message.error'))}
		{assign_associative var="msgArr" errorMsg=$session->flash('error')}
		{$javascript->object($msgArr)}
	{/if}
{else}
	<div style="padding-top:20px;">
		{if ($session->check('Message.error'))}
			<p>{$session->flash('error')}</p>
		{/if}
	</div>
{/if}