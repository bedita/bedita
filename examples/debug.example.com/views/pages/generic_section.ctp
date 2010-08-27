<?php 
e($this->element('header'));

e($this->element('form_search'));
?>

<?php if (!empty($section)): ?>
	<hr/>
	<h3><?php __("current section", false); ?> : $section</h3>
	<a href="javascript:void(0)" class="open-close-link"><?php __("show/hide", false); ?></a>
	<div style="display: none">
	<?php pr($section); ?>
	</div>
<?php endif; ?>

<hr/>
<h3><?php __("publication", false); ?> : $publication</h3>
<a href="javascript:void(0)" class="open-close-link"><?php __("show/hide", false); ?></a>
<div style="display: none">
<?php pr($publication); ?>
</div>

<hr/>
<?php if (empty($BEAuthUser)): ?>
	<h3><?php  __("user not logged", false); echo ": <a href=\"" . $html->url('/login') . "\">" . __("login", true) . "</a>";?></h3>
<?php else: ?>
	<h3><?php __("user logged", false); ?> : $BEAuthUser</h3>
	<a href="javascript:void(0)" class="open-close-link"><?php __("show/hide", false); ?></a>
	<div style="display: none">
	<?php pr($BEAuthUser); ?>
	<a href="<?php $html->url('/logout'); ?>"><?php __("logout", true); ?></a> 
	</div>
<?php endif; ?>

<hr/>
<h3><?php __("session data", false); ?>: $session-&gt;read()</h3>
<a href="javascript:void(0)" class="open-close-link"><?php __("show/hide", false); ?></a>
<div style="display: none">
<?php pr($session->read()); ?>
</div>

<hr/>
<h3><?php __("configuration", false);?> : $conf</h3>
<a href="javascript:void(0)" class="open-close-link"><?php __("show/hide", false); ?></a>
<div style="display: none">
pr($conf);
</div>

<hr/>
<h3><?php __("template variables available", false);?>:</h3>
<a href="javascript:void(0)" class="open-close-link"><?php __("show/hide", false); ?></a>
<div style="display: none">
<?php pr($this->viewVars); ?>
</div>

<?php e($this->element('footer')); ?>