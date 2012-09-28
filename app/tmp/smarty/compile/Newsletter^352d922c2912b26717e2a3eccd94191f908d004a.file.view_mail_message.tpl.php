<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:13:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/view_mail_message.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19016911555053497db77186-09572275%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '352d922c2912b26717e2a3eccd94191f908d004a' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/view_mail_message.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19016911555053497db77186-09572275',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cssOptions' => 0,
    'html' => 0,
    'currLang' => 0,
    'view' => 0,
    'object' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497dd3bed1_14889200',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497dd3bed1_14889200')) {function content_5053497dd3bed1_14889200($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php echo smarty_function_assign_associative(array('var'=>"cssOptions",'inline'=>false),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->css("ui.datepicker",null,$_smarty_tpl->tpl_vars['cssOptions']->value);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->css("jquery.timepicker.css",null,$_smarty_tpl->tpl_vars['cssOptions']->value);?>


<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.selectboxes.pack",false);?>


<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.sortable",true);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.datepicker",false);?>

<?php if ($_smarty_tpl->tpl_vars['currLang']->value!="eng"){?>
	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/i18n/ui.datepicker-".((string)$_smarty_tpl->tpl_vars['currLang']->value).".js",false);?>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.placement.below");?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.timepicker-list");?>


<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.validate");?>


<script type="text/javascript">
	$(document).ready(function () {
		openAtStart("#contents, #invoice");
		$("#timeStart, #timeEnd").timePicker({ startTime: "00:00", endTime: "23:30" });
		$("#updateForm").validate();
	});
</script>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_common_js');?>



<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"view"), 0);?>


<div class="head">
	
	<h1><?php if (!empty($_smarty_tpl->tpl_vars['object']->value)){?><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? "<i>[no title]</i>" : $tmp);?>
<?php }else{ ?><i>[<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
]</i><?php }?></h1>
	
</div>

<?php $_smarty_tpl->tpl_vars['objIndex'] = new Smarty_variable(0, null, 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"view",'fixed'=>true), 0);?>


<div class="main">	
	
	<?php echo $_smarty_tpl->getSubTemplate ("inc/form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		
</div>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('menuright');?>
<?php }} ?>