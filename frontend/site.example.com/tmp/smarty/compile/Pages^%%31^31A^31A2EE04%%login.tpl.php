<?php /* Smarty version 2.6.18, created on 2009-08-04 17:48:19
         compiled from /home/ste/workspace/bedita/frontend/example2.com/views/pages/login.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/frontend/example2.com/views/pages/login.tpl', 20, false),array('block', 't', '/home/ste/workspace/bedita/frontend/example2.com/views/pages/login.tpl', 54, false),)), $this); ?>
<?php echo '<div class="top"><div class="lang"><ul class="footel" style="border:0; margin:0; padding:0;">'; ?><?php $_from = $this->_tpl_vars['conf']->frontendLangs; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['g']):
?><?php echo '<li><a '; ?><?php if ($this->_tpl_vars['currLang'] == $this->_tpl_vars['k']): ?><?php echo 'style="color:white;"'; ?><?php endif; ?><?php echo ' title="'; ?><?php echo $this->_tpl_vars['g']; ?><?php echo '" href="'; ?><?php echo $this->_tpl_vars['html']->url('/'); ?><?php echo 'lang/'; ?><?php echo $this->_tpl_vars['k']; ?><?php echo '">'; ?><?php echo $this->_tpl_vars['g']; ?><?php echo '</a></li>'; ?><?php endforeach; endif; unset($_from); ?><?php echo '</ul></div><div class="logo"><a title="'; ?><?php echo $this->_tpl_vars['publication']['public_name']; ?><?php echo '" href="'; ?><?php echo $this->_tpl_vars['html']->url('/'); ?><?php echo '"><img src="'; ?><?php echo $this->_tpl_vars['html']->webroot; ?><?php echo 'img/BElogo24.png" alt="" /></a></div><div class="strillo">'; ?><?php echo ((is_array($_tmp=@$this->_tpl_vars['publication']['public_name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['publication']['title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['publication']['title'])); ?><?php echo '</div><div class="illustrazione" style="margin-left:30px; "><img src="'; ?><?php echo $this->_tpl_vars['html']->webroot; ?><?php echo 'img/albero.png" /></div><div class="topG"></div></div><div class="headmenu"></div>'; ?>




<div class="main">

	<div class="content-main">
	
		<div class="textC">
			<form action="<?php echo $this->_tpl_vars['html']->here; ?>
" method="post">
			<label>username</label>
			<br />
			<input type="text" name="login[userid]" />
			<br />
			<label>password</label>
			<br />
			<input type="password" name="login[passwd]" />
			<br />
			<input type="hidden" name="backURL" value="<?php echo $this->_tpl_vars['beurl']->here(); ?>
"/>
			<input style="margin:10px 0px 10px 0px" type="submit" value="<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>submit<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>" />
			</form>	
		</div>
		
	</div>
	
</div>