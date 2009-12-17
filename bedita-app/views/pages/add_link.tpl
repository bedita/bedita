{if ($msg->check('Message.error'))}
	<div class="message-error">
		<div id="error-img"><span>&nbsp;</span></div>
		<p>{$msg->userMsg('error')|capitalize}</p>
	</div>
{else}
{$view->element('form_link_item')}
{/if}