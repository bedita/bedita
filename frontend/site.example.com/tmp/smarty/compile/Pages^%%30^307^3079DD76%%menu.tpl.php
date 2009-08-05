<?php /* Smarty version 2.6.18, created on 2009-08-05 10:33:23
         compiled from /home/ste/workspace/bedita/frontend/site.example.com/views/elements/menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/frontend/site.example.com/views/elements/menu.tpl', 23, false),)), $this); ?>
<div class="subdocs">

	<ul>		
	<?php if (! empty ( $this->_tpl_vars['section']['childContents'] ) && ( $this->_tpl_vars['section']['nickname'] != 'footer-docs' )): ?>
		<?php $_from = $this->_tpl_vars['section']['childContents']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object']):
?>
		<li><a href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
<?php echo $this->_tpl_vars['section']['nickname']; ?>
/<?php echo $this->_tpl_vars['object']['nickname']; ?>
" 
		<?php if ($this->_tpl_vars['section']['currentContent']['nickname'] == $this->_tpl_vars['object']['nickname']): ?>class="subon"<?php endif; ?>><?php echo $this->_tpl_vars['object']['title']; ?>
</a></li>
		<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
		
	<?php if (! empty ( $this->_tpl_vars['section']['childSections'] ) && ( $this->_tpl_vars['section']['id'] != $this->_tpl_vars['publication']['id'] )): ?>
		<?php $_from = $this->_tpl_vars['section']['childSections']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['subsection']):
?>
		<h1><a href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
<?php echo $this->_tpl_vars['subsection']['nickname']; ?>
"><?php echo $this->_tpl_vars['subsection']['title']; ?>
</a></h1>
		<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>
	</ul>

</div>

<?php if (( $this->_tpl_vars['section']['id'] != $this->_tpl_vars['publication']['id'] ) && ( $this->_tpl_vars['section']['nickname'] != 'footer-docs' )): ?>
<div class="breadcrumb">
	<h2>
		<a href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
" ><?php echo ((is_array($_tmp=@$this->_tpl_vars['publication']['public_name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['publication']['title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['publication']['title'])); ?>
</a>&nbsp;&gt;&nbsp; 
		<?php if (( ! empty ( $this->_tpl_vars['section']['pathSection'] ) )): ?>
		<?php $_from = $this->_tpl_vars['section']['pathSection']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sec']):
?>
		<a href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
<?php echo $this->_tpl_vars['sec']['nickname']; ?>
" ><?php echo $this->_tpl_vars['sec']['title']; ?>
</a>&nbsp;&gt;&nbsp;
		<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>
		<a href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
<?php echo $this->_tpl_vars['section']['nickname']; ?>
" class="subon" ><?php echo $this->_tpl_vars['section']['title']; ?>
</a>
	</h2>
</div>
<?php endif; ?>
	