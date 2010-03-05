<?php if ($session->check('Message.info')):?>
	<div class="message info">
		e($session->flash('info'));
	</div>
<?php elseif ($session->check('Message.error')): ?>
	<div class="message error">
		e($session->flash('error'));
	</div>
<?php endif;?>