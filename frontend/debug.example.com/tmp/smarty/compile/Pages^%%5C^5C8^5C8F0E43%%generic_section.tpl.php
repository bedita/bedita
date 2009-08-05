<?php /* Smarty version 2.6.18, created on 2009-08-05 11:28:52
         compiled from generic_section.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'assign_associative', 'generic_section.tpl', 3, false),array('function', 'dump', 'generic_section.tpl', 7, false),)), $this); ?>
<h2>Default template</h2>

<?php echo smarty_function_assign_associative(array('var' => 'options','object' => $this->_tpl_vars['section']['currentContent'],'showForm' => true), $this);?>

<?php echo $this->_tpl_vars['view']->element('show_comments',$this->_tpl_vars['options']); ?>
	

<pre>
<?php echo smarty_function_dump(array('var' => $this->_tpl_vars['section']), $this);?>

</pre>