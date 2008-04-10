{if ($msg->check('Message.error'))}
	<div class="message-error">
		<div id="error-img"><span>&nbsp;</span></div>
		<p>{$msg->userMsg('error')|capitalize}</p>
	</div>
{else}
{include file="../pages/form_link_item.tpl"}
{/if}