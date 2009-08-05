<?php /* Smarty version 2.6.18, created on 2009-08-05 10:33:24
         compiled from /home/ste/workspace/bedita/frontend/site.example.com/views/errors/error404.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/frontend/site.example.com/views/errors/error404.tpl', 25, false),)), $this); ?>
<?php echo $this->_tpl_vars['html']->docType('xhtml-trans'); ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | Error 404 - Missing page</title>
	<?php echo $this->_tpl_vars['html']->css('beditaBase'); ?>


</head>
<body>

<div class="top">

	<div class="logo">
		<a title="<?php echo $this->_tpl_vars['publication']['public_name']; ?>
" href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
"><img src="<?php echo $this->_tpl_vars['html']->webroot; ?>
img/BElogo24.png" alt="" /></a>
	</div>

	<div class="moduli" style="font-size:0.8em">
		<h1>Error 404</h1>
		Missing Page
	</div>
		
</div>

<?php if ($this->_tpl_vars['conf']->debug >= 1): ?> 
<pre>
ErrorType:	<?php echo ((is_array($_tmp=@$this->_tpl_vars['errorType'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>

Details: 	<?php echo ((is_array($_tmp=@$this->_tpl_vars['details'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>

Result: 	<?php echo ((is_array($_tmp=@$this->_tpl_vars['result'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>


Action: 	<?php echo ((is_array($_tmp=@$this->_tpl_vars['action'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>

Controller: <?php echo ((is_array($_tmp=@$this->_tpl_vars['controller'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>

File: 		<?php echo ((is_array($_tmp=@$this->_tpl_vars['file'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>

Title: 		<?php echo ((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>


</pre>
<?php endif; ?>

<?php echo $this->_tpl_vars['view']->element('footer'); ?>


</body>