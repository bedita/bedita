<?php /* Smarty version 2.6.18, created on 2009-08-04 17:49:52
         compiled from /home/ste/workspace/bedita/frontend/example2.com/views/pages/section.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'assign_concat', '/home/ste/workspace/bedita/frontend/example2.com/views/pages/section.tpl', 1, false),)), $this); ?>
<?php echo smarty_function_assign_concat(array('var' => 'sectionNick','0' => $this->_tpl_vars['section']['nickname'],'1' => ".tpl"), $this);?>


<?php echo smarty_function_assign_concat(array('var' => 'tplfile','0' => @VIEWS,'1' => "pages/",'2' => $this->_tpl_vars['sectionNick']), $this);?>


<?php if (file_exists ( $this->_tpl_vars['tplfile'] )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tplfile'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "generic_section.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>