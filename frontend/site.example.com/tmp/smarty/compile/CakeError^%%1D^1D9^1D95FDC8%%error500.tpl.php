<?php /* Smarty version 2.6.18, created on 2009-08-04 17:48:58
         compiled from /home/ste/workspace/bedita/frontend/example2.com/views/errors/error500.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/frontend/example2.com/views/errors/error500.tpl', 5, false),)), $this); ?>
<h2>Error 500 - Internal Server Error </h2>

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