<div id="messagesDiv">
	{if ($session->check('Message.error'))}
	<div class="message-error">
		<span id="error-img">&#160;&#160;&#160;</span> <span id="err-msg">{t}Error{/t}</span>
		<p>{$session->flash('error')}</p>
	</div>
	{/if}
	{if ($session->check('Message.warn'))}
	<div class="message-warn">
		<span id="warn-img">&#160;&#160;&#160;</span> <span id="warn-msg">{t}Warning{/t}</span>
		<p>{$session->flash('warn')}</p>
	</div>
	{/if}
	{if ($session->check('Message.info'))}
	<div class="message-info">
		<span id="info-img">&#160;&#160;&#160;</span> <span id="info-msg">{t}Info{/t}</span>
		<p>{$session->flash('info')}</p>
	</div>
	{/if}
</div>