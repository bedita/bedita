<?php /* Smarty version 2.6.18, created on 2009-08-05 11:27:57
         compiled from /home/ste/workspace/bedita/frontend/basic.example.com/views/elements/form_search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/frontend/basic.example.com/views/elements/form_search.tpl', 2, false),array('block', 't', '/home/ste/workspace/bedita/frontend/basic.example.com/views/elements/form_search.tpl', 3, false),)), $this); ?>
<form action="<?php echo $this->_tpl_vars['html']->url('/search'); ?>
" method="post">
	<input type="text" name="searchstring" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['stringSearched'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
"/>
	<input type="submit" value="<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>search<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"/>
</form>