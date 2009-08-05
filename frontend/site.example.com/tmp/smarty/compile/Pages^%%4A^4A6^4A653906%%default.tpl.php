<?php /* Smarty version 2.6.18, created on 2009-08-05 10:32:33
         compiled from /home/ste/workspace/bedita/frontend/site.example.com/views/layouts/default.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/frontend/site.example.com/views/layouts/default.tpl', 4, false),)), $this); ?>
<?php echo $this->_tpl_vars['html']->docType('xhtml-trans'); ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title><?php if (isset ( $this->_tpl_vars['section']['currentContent']['title'] )): ?><?php echo $this->_tpl_vars['section']['currentContent']['title']; ?>
 | <?php endif; ?><?php echo ((is_array($_tmp=@$this->_tpl_vars['publication']['public_name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['publication']['title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['publication']['title'])); ?>
</title>

	<meta http-equiv="Content-Style-Type" content="text/css" />

	<link rel="icon" href="<?php echo $this->_tpl_vars['session']->webroot; ?>
favicon.ico" type="image/gif" />
	<link rel="shortcut icon" href="<?php echo $this->_tpl_vars['session']->webroot; ?>
favicon.gif" type="image/gif" />
	
	<meta name="description" content="<?php echo ((is_array($_tmp=@$this->_tpl_vars['section']['currentContent']['description'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['publication']['description']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['publication']['description'])); ?>
" />
	<meta name="author" content="<?php echo $this->_tpl_vars['publication']['creator']; ?>
" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	
    <!-- RTF dublin core dataset -->
    
    <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
    <meta name="DC.title" 		content="<?php echo ((is_array($_tmp=@$this->_tpl_vars['publication']['public_name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['publication']['title']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['publication']['title'])); ?>
" />
    <meta name="DC.description" content="<?php echo $this->_tpl_vars['publication']['description']; ?>
" />
	<meta name="DC.language" 	content="<?php echo $this->_tpl_vars['publication']['lang']; ?>
" />
    <meta name="DC.creator" 	content="<?php echo $this->_tpl_vars['publication']['creator']; ?>
" />
    <meta name="DC.publisher" 	content="<?php echo $this->_tpl_vars['publication']['publisher']; ?>
" />
    <meta name="DC.date" 		content="<?php echo $this->_tpl_vars['publication']['created']; ?>
" />
	<meta name="DC.modified" 	content="<?php echo $this->_tpl_vars['publication']['modified']; ?>
" />
	<meta name="DC.format" 		content="text/html" />
	<meta name="DC.identifier"  content="<?php echo $this->_tpl_vars['publication']['id']; ?>
" />
    <meta name="DC.rights" 		content="<?php echo $this->_tpl_vars['publication']['rights']; ?>
" />
 	<meta name="DC.license" 	content="<?php echo $this->_tpl_vars['publication']['license']; ?>
" />

    <!-- end -->

	<?php $_from = $this->_tpl_vars['feedNames']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feed']):
?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo $this->_tpl_vars['feed']['title']; ?>
" href="<?php echo $this->_tpl_vars['html']->url('/rss'); ?>
/<?php echo $this->_tpl_vars['feed']['nickname']; ?>
" />
	<?php endforeach; endif; unset($_from); ?>
	
	<?php echo $this->_tpl_vars['html']->css('beditaBase'); ?>

	<?php echo $this->_tpl_vars['html']->css('thickbox.BEfrontend'); ?>

	
	<?php echo $this->_tpl_vars['javascript']->link('jquery'); ?>

	<?php echo $this->_tpl_vars['javascript']->link("jquery.pngFix.pack"); ?>

	<?php echo $this->_tpl_vars['javascript']->link('bedita'); ?>

	<?php echo $this->_tpl_vars['javascript']->link("jquery.thickbox.BEfrontend"); ?>

	
</head>


<body>

<?php if (isset ( $this->_tpl_vars['conf']->staging ) && ( $this->_tpl_vars['conf']->staging )): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "./_BEdita_staging_toolbar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>


<?php echo $this->_tpl_vars['content_for_layout']; ?>



<?php if (empty ( $this->_tpl_vars['conf']->staging ) && ! empty ( $this->_tpl_vars['publication']['stats_code'] )): ?><?php echo $this->_tpl_vars['publication']['stats_code']; ?>
<?php endif; ?>
</body>
</html>