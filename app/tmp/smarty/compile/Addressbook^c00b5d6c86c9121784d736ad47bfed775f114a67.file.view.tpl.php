<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:13
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/addressbook/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1024903845504ef5e11fe609-84699632%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c00b5d6c86c9121784d736ad47bfed775f114a67' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/addressbook/view.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1024903845504ef5e11fe609-84699632',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cssOptions' => 0,
    'html' => 0,
    'javascript' => 0,
    'currLang' => 0,
    'view' => 0,
    'object' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e1368145_30793560',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e1368145_30793560')) {function content_504ef5e1368145_30793560($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php echo smarty_function_assign_associative(array('var'=>"cssOptions",'inline'=>false),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->css("ui.datepicker",null,$_smarty_tpl->tpl_vars['cssOptions']->value);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->css("jquery.autocomplete",null,$_smarty_tpl->tpl_vars['cssOptions']->value);?>


<?php echo $_smarty_tpl->tpl_vars['javascript']->value->link("jquery/jquery.form",false);?>

<?php echo $_smarty_tpl->tpl_vars['javascript']->value->link("jquery/jquery.selectboxes.pack",false);?>


<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.sortable",true);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.datepicker",false);?>

<?php if ($_smarty_tpl->tpl_vars['currLang']->value!="eng"){?>
	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/i18n/ui.datepicker-".((string)$_smarty_tpl->tpl_vars['currLang']->value).".js",false);?>

<?php }?>
<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.autocomplete",false);?>



<script type="text/javascript">
	
	$(document).ready( function (){
	
		openAtStart("#card,#address,#properties");
		
		$('textarea.autogrowarea').css("line-height", "1.2em").autogrow();
		
		// prendiamolo da remoto, facciamo n file php con tutti gli array helpers per gli autocomplete?
		var data = "Sig Sigra Satrap SoS sarallapappa Mr Mrs Dott Prof Ing SA srl Spa sagl etc".split(" ");
		$("#vtitle").autocomplete(data);

	});
	
</script>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_common_js');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->set('method',$_smarty_tpl->tpl_vars['view']->value->action);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="head">
	
	<h1><?php if (!empty($_smarty_tpl->tpl_vars['object']->value)){?><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? "<i>[no title]</i>" : $tmp);?>
<?php }else{ ?><i>[<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
]</i><?php }?></h1>
	
</div>

<?php $_smarty_tpl->tpl_vars['objIndex'] = new Smarty_variable(0, null, 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('fixed'=>true), 0);?>


<div class="main">	
	
	<?php echo $_smarty_tpl->getSubTemplate ("inc/form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		
</div>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('menuright');?>




<?php }} ?>