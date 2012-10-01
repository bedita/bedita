<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:15
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/tree.tpl" */ ?>
<?php /*%%SmartyHeaderCode:574646136504dfcc8377ab4-78920934%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b4184c1a57666ea8525a1dd5fd865ddf54bcc79c' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/tree.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '574646136504dfcc8377ab4-78920934',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfcc83e92a5_37266924',
  'variables' => 
  array (
    'cssOptions' => 0,
    'html' => 0,
    'checkbox' => 0,
    'tree' => 0,
    'parents' => 0,
    'beTree' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfcc83e92a5_37266924')) {function content_504dfcc83e92a5_37266924($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
?><?php echo smarty_function_assign_associative(array('var'=>"cssOptions",'inline'=>false),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->css('../js/jquery/treeview/jquery.treeview',null,$_smarty_tpl->tpl_vars['cssOptions']->value);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/treeview/jquery.treeview",false);?>


<script type="text/javascript">
$(document).ready(function(){ 
	// third example
	$(".menutree").treeview({ 
		animated: "normal",
		collapsed: true,
		unique: false,
		persist: "cookie"
	});

$(".menutree input:checked").parent().css("background-color","#dedede").parents("ul, li").show();

});
</script>

<?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value)){?>			
	
	
	<input type='hidden' name='data[destination]' value=''/>
	<?php echo $_smarty_tpl->tpl_vars['beTree']->value->view($_smarty_tpl->tpl_vars['tree']->value,"checkbox",$_smarty_tpl->tpl_vars['parents']->value);?>

	
<?php }else{ ?>
		
	<?php echo $_smarty_tpl->tpl_vars['beTree']->value->view($_smarty_tpl->tpl_vars['tree']->value);?>

	
<?php }?><?php }} ?>