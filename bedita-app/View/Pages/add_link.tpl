{if ($this->Session->check('Message.error'))}
	<div class="message-error">
		<div id="error-img"><span>&nbsp;</span></div>
		<p>{$this->Session->flash('error')|capitalize}</p>
	</div>
{else}
{$view->element('form_link_item')}
{/if}