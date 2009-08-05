<?php /* Smarty version 2.6.18, created on 2009-03-31 18:13:12
         compiled from /home/ste/workspace/bedita/bedita-front/views/pages/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'dump', '/home/ste/workspace/bedita/bedita-front/views/pages/index.tpl', 6, false),)), $this); ?>
<h3>Language: <?php echo $this->_tpl_vars['currLang']; ?>
</h3>
<h3>Frontend: [title] <?php echo $this->_tpl_vars['publication']['title']; ?>
 - [id] <?php echo $this->_tpl_vars['publication']['id']; ?>
 </h3>

<h3>Sections Tree</h3>
<pre>
<?php echo smarty_function_dump(array('var' => $this->_tpl_vars['sectionsTree']), $this);?>

</pre>