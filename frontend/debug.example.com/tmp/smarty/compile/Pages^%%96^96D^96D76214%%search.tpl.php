<?php /* Smarty version 2.6.18, created on 2009-08-05 11:28:14
         compiled from /home/ste/workspace/bedita/frontend/basic.example.com/views/pages/search.tpl */ ?>
<div><?php echo $this->_tpl_vars['view']->element('form_search'); ?>
</div>
<div>
	<?php echo $this->_tpl_vars['beToolbar']->init($this->_tpl_vars['searchResult']['toolbar']); ?>

	<?php echo $this->_tpl_vars['beToolbar']->first(); ?>
&nbsp;&nbsp; 
	<?php echo $this->_tpl_vars['beToolbar']->prev(); ?>
&nbsp;&nbsp;
	
	<?php echo $this->_tpl_vars['beToolbar']->current(); ?>
 / <?php echo $this->_tpl_vars['beToolbar']->pages(); ?>
&nbsp;&nbsp;
	
	<?php echo $this->_tpl_vars['beToolbar']->next(); ?>
&nbsp;&nbsp;
	<?php echo $this->_tpl_vars['beToolbar']->last(); ?>
&nbsp;&nbsp;
	
	<ul>
	<?php $_from = $this->_tpl_vars['searchResult']['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object']):
?>
		<li><a href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
<?php echo $this->_tpl_vars['object']['nickname']; ?>
"><?php echo $this->_tpl_vars['object']['title']; ?>
</a></li>
	<?php endforeach; endif; unset($_from); ?>
	</ul>	
</div>	


