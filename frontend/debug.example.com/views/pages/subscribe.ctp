<?php if ($what == "newsletter" && !empty($mailgroups)):?>
	<?php e($this->element('subscribe_newsletter'));?>
<?php elseif ($what == "user"):?>
	<?php e($this->element('signup'));?>
<?php endif;?>