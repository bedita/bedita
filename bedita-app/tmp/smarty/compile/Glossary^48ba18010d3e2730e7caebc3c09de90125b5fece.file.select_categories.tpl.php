<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:37:15
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/select_categories.tpl" */ ?>
<?php /*%%SmartyHeaderCode:952647757504f145b276774-83088203%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '48ba18010d3e2730e7caebc3c09de90125b5fece' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/select_categories.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '952647757504f145b276774-83088203',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categories' => 0,
    'categorySearched' => 0,
    'cat_id' => 0,
    'html' => 0,
    'currentModule' => 0,
    'view' => 0,
    'cat_label' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f145b2ff579_82244571',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f145b2ff579_82244571')) {function content_504f145b2ff579_82244571($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php if (!empty($_smarty_tpl->tpl_vars['categories']->value)){?>
<ul class="menuleft insidecol catselector">
	<li><a href="javascript:void(0)" onClick="$('#groups').slideToggle();"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Select by category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
	<ul id="groups" <?php if ((empty($_smarty_tpl->tpl_vars['categorySearched']->value))){?>style="display:none"<?php }?>>
		<?php  $_smarty_tpl->tpl_vars['cat_label'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cat_label']->_loop = false;
 $_smarty_tpl->tpl_vars['cat_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cat_label']->key => $_smarty_tpl->tpl_vars['cat_label']->value){
$_smarty_tpl->tpl_vars['cat_label']->_loop = true;
 $_smarty_tpl->tpl_vars['cat_id']->value = $_smarty_tpl->tpl_vars['cat_label']->key;
?>
		<li <?php if ((((($tmp = @$_smarty_tpl->tpl_vars['categorySearched']->value)===null||$tmp==='' ? '' : $tmp))==$_smarty_tpl->tpl_vars['cat_id']->value)){?>class="on"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/<?php echo $_smarty_tpl->tpl_vars['view']->value->action;?>
/category:<?php echo $_smarty_tpl->tpl_vars['cat_id']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['cat_label']->value;?>
</a></li>
		<?php } ?>
	</ul>
</ul>
<?php }?><?php }} ?>