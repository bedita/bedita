<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 16:53:07
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/translations/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:358245267505344d359fd26-64675856%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4e399e5b081e2778d93133140fe1b545ca1309c7' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/translations/view.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '358245267505344d359fd26-64675856',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'view' => 0,
    'object_translation' => 0,
    'object_master' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_505344d36d7446_94754644',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_505344d36d7446_94754644')) {function content_505344d36d7446_94754644($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.datepicker",false);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('texteditor');?>


<style type="text/css">
	.mainhalf TEXTAREA, .mainhalf INPUT[type=text], .mainhalf TABLE.bordered { 
		width:320px !important;
	}
	.disabled { 
		opacity:0.6;	
	}
	.disabled TEXTAREA, .disabled INPUT[type=text] { 
		background-color:transparent;
	}
</style>

<script type="text/javascript">
$(document).ready(function(){
	openAtStart("#ttitle,#tlong_desc_langs_container");
	
	$(".tab2").click(function () {	
			var trigged = $(this).next().attr("rel") ;
			//$(this).BEtabstoggle();
			$("*[rel='"+trigged+"']").prev(".tab2").BEtabstoggle();
	});
	//$('textarea.autogrowarea').css("line-height","1.2em").autogrow();
});
</script>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_common_js');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="head">
	<?php if (!empty($_smarty_tpl->tpl_vars['object_translation']->value['title'])){?><h1><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_translation']->value['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp);?>
</h1><?php }?>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
translation of<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<h1 style="margin-top:0px"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_master']->value['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp);?>
</h1>

</div>

<?php $_smarty_tpl->tpl_vars['objIndex'] = new Smarty_variable(0, null, 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('fixed'=>true), 0);?>



<div class="mainfull" style="width:700px; padding:0px;">	

	<?php echo $_smarty_tpl->getSubTemplate ("inc/form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


</div>


<?php }} ?>