<?php /* Smarty version 2.6.18, created on 2009-08-05 11:27:57
         compiled from /home/ste/workspace/bedita/frontend/basic.example.com/views/pages/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'dump', '/home/ste/workspace/bedita/frontend/basic.example.com/views/pages/index.tpl', 8, false),)), $this); ?>
<h3>Language: <?php echo $this->_tpl_vars['currLang']; ?>
</h3>
<h3>Frontend: [title] <?php echo $this->_tpl_vars['publication']['title']; ?>
 - [id] <?php echo $this->_tpl_vars['publication']['id']; ?>
 </h3>

<?php echo $this->_tpl_vars['view']->element('form_search'); ?>


<h3>Sections Tree</h3>
<pre>
<?php echo smarty_function_dump(array('var' => $this->_tpl_vars['sectionsTree']), $this);?>

</pre>