<?php if ($msg->check('Message.info')):?>
	<div class="message info">
		e($msg->userMsg('info'));
	</div>
<?php elseif ($msg->check('Message.error')): ?>
	<div class="message error">
		e($msg->userMsg('error'));
	</div>
<?php endif;?>