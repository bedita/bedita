<?php /* Smarty version 2.6.18, created on 2009-08-05 10:33:29
         compiled from /home/ste/workspace/bedita/frontend/site.example.com/views/elements/show_comments.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', '/home/ste/workspace/bedita/frontend/site.example.com/views/elements/show_comments.tpl', 13, false),array('modifier', 'date_format', '/home/ste/workspace/bedita/frontend/site.example.com/views/elements/show_comments.tpl', 26, false),array('modifier', 'nl2br', '/home/ste/workspace/bedita/frontend/site.example.com/views/elements/show_comments.tpl', 28, false),)), $this); ?>
<?php if (empty ( $this->_tpl_vars['object'] ) && ! empty ( $this->_tpl_vars['section']['currentContent'] )): ?>
	<?php $this->assign('object', $this->_tpl_vars['section']['currentContent']); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['object']['comments'] != 'off'): ?>

	<?php if (! empty ( $this->_tpl_vars['object']['Comment'] )): ?>
		
		<?php $_from = $this->_tpl_vars['object']['Comment']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['fc_com'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fc_com']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['comment']):
        $this->_foreach['fc_com']['iteration']++;
?>
	
			<?php if (($this->_foreach['fc_com']['iteration']-1) == 0): ?>
				<h3 style="margin-top:30px;"><?php echo $this->_foreach['fc_com']['total']; ?>
&nbsp; 
				<?php if ($this->_foreach['fc_com']['total'] == 1): ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Comment<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php else: ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Comments<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?></h3>
			<?php endif; ?>
			
			<a name="comment-<?php echo $this->_tpl_vars['comment']['id']; ?>
"></a>
			<div class=commentContainer>
				<h3>
				<?php if (! empty ( $this->_tpl_vars['comment']['url'] )): ?>
					<a href="<?php echo $this->_tpl_vars['comment']['url']; ?>
" target="_blank"><?php echo $this->_tpl_vars['comment']['author']; ?>
</a>
				<?php else: ?>
					<?php echo $this->_tpl_vars['comment']['author']; ?>

				<?php endif; ?>
				</h3>
				
				<p><?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['created'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['conf']->datePattern) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['conf']->datePattern)); ?>
</p>
				
				<p><?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['description'])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</p>
			</div>
		<?php endforeach; endif; unset($_from); ?>
	
	<?php endif; ?>

	<?php if (! empty ( $this->_tpl_vars['showForm'] )): ?><?php echo $this->_tpl_vars['view']->element('form_comments'); ?>
<?php endif; ?>
	
<?php endif; ?>