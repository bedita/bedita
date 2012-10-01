<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/prevnext.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1043398225504ef6d9c62f84-79110441%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '702c23b7825bb25cd0a0a5a0ee7ffc3c5693ffeb' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/prevnext.tpl',
      1 => 1346861103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1043398225504ef6d9c62f84-79110441',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504ef6d9d61913_53873842',
  'variables' => 
  array (
    'session' => 0,
    'object' => 0,
    'prevNext' => 0,
    'html' => 0,
    'currentModule' => 0,
    'key' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef6d9d61913_53873842')) {function content_504ef6d9d61913_53873842($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['session']->value->read("prevNext")&&!empty($_smarty_tpl->tpl_vars['object']->value['id'])){?>
<?php $_smarty_tpl->tpl_vars["prevNext"] = new Smarty_variable($_smarty_tpl->tpl_vars['session']->value->read("prevNext"), null, 0);?>
<div class="listobjnav">
	<?php if ((($tmp = @$_smarty_tpl->tpl_vars['prevNext']->value[$_smarty_tpl->tpl_vars['object']->value['id']]['prev'])===null||$tmp==='' ? '' : $tmp)){?>
	<a title="prev" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/view/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['prevNext']->value[$_smarty_tpl->tpl_vars['object']->value['id']]['prev'])===null||$tmp==='' ? '' : $tmp);?>
">
		‹
	</a>
	<?php }else{ ?> ‹ <?php }?>

	<?php if ((($tmp = @$_smarty_tpl->tpl_vars['prevNext']->value[$_smarty_tpl->tpl_vars['object']->value['id']]['next'])===null||$tmp==='' ? '' : $tmp)){?>
	<a title="next" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['url'];?>
/view/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['prevNext']->value[$_smarty_tpl->tpl_vars['object']->value['id']]['next'])===null||$tmp==='' ? '' : $tmp);?>
">
		›
	</a> 
	<?php }else{ ?> › <?php }?>

	<div style="margin-top:5px; color:#666 !important; font-size:10px !important; text-align:center">
		<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['prevNext']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['c']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['c']['iteration']++;
?>
			<?php if (($_smarty_tpl->tpl_vars['key']->value==$_smarty_tpl->tpl_vars['object']->value['id'])){?><?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['c']['iteration'];?>
 / <?php echo count($_smarty_tpl->tpl_vars['prevNext']->value);?>
<?php }?>
		<?php } ?>
	</div>
	
</div>

<?php }?>
<?php }} ?>