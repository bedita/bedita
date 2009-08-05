<?php /* Smarty version 2.6.18, created on 2009-08-05 10:33:23
         compiled from /home/ste/workspace/bedita/frontend/site.example.com/views/elements/footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/frontend/site.example.com/views/elements/footer.tpl', 13, false),array('modifier', 'date_format', '/home/ste/workspace/bedita/frontend/site.example.com/views/elements/footer.tpl', 13, false),)), $this); ?>
<div class="footer">
	
	<ul class="footel" style="width:140px; border:0px; margin-top:2px;">
		<?php $_from = $this->_tpl_vars['conf']->frontendLangs; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['g']):
?>
			<li>
				<a <?php if ($this->_tpl_vars['currLang'] == $this->_tpl_vars['k']): ?>style="color:white;"<?php endif; ?> title="<?php echo $this->_tpl_vars['g']; ?>
" href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
lang/<?php echo $this->_tpl_vars['k']; ?>
"><?php echo $this->_tpl_vars['g']; ?>
</a>
			</li>
		<?php endforeach; endif; unset($_from); ?>

	</ul>
	
	<ul class="footel" style="border:0px; width:140px;">
		<li><?php echo ((is_array($_tmp=@$this->_tpl_vars['publication']['public_name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['publication']['title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['publication']['title'])); ?>
<br /><?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
</li>
	</ul>
</div>

