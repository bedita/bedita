<?php /* Smarty version 2.6.18, created on 2009-03-31 18:17:22
         compiled from /home/ste/workspace/bedita/bedita-front/views/layouts/default.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/bedita-front/views/layouts/default.tpl', 4, false),)), $this); ?>
<?php echo $this->_tpl_vars['html']->docType('xhtml-trans'); ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title><?php echo ((is_array($_tmp=@$this->_tpl_vars['publication']['public_name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['publication']['title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['publication']['title'])); ?>
<?php if (! empty ( $this->_tpl_vars['section'] )): ?> | <?php echo $this->_tpl_vars['section']['title']; ?>
<?php endif; ?></title>
	<link rel="icon" href="<?php echo $this->_tpl_vars['session']->webroot; ?>
favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $this->_tpl_vars['session']->webroot; ?>
favicon.ico" type="image/x-icon" />

	<meta name="author" content="" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
<?php $_from = $this->_tpl_vars['feedNames']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feed']):
?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo $this->_tpl_vars['feed']['title']; ?>
" href="<?php echo $this->_tpl_vars['html']->url('/rss'); ?>
/<?php echo $this->_tpl_vars['feed']['nickname']; ?>
" />
<?php endforeach; endif; unset($_from); ?>

<?php echo $this->_tpl_vars['content_for_layout']; ?>


<div id="footerPage">
</div>

<?php if (empty ( $this->_tpl_vars['conf']->staging ) && ! empty ( $this->_tpl_vars['publication']['stats_code'] )): ?><?php echo $this->_tpl_vars['publication']['stats_code']; ?>
<?php endif; ?>
</body>
</html>