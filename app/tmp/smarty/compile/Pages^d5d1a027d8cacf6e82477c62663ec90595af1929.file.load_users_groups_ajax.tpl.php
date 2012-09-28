<?php /* Smarty version Smarty-3.1.11, created on 2012-09-10 16:52:06
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/pages/load_users_groups_ajax.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1598598334504dfe96805845-24299250%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd5d1a027d8cacf6e82477c62663ec90595af1929' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/pages/load_users_groups_ajax.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1598598334504dfe96805845-24299250',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'itemsList' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504dfe96903bf7_66207458',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfe96903bf7_66207458')) {function content_504dfe96903bf7_66207458($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_smarty_tpl->tpl_vars["key"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['itemsList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value){
$_smarty_tpl->tpl_vars["item"]->_loop = true;
 $_smarty_tpl->tpl_vars["key"]->value = $_smarty_tpl->tpl_vars["item"]->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</option><?php } ?><?php }} ?>