<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:56
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/file_item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2034337738504e1033eaaf10-34645822%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '265a493319650d5a9a9a92b3ba5bd8f4dae42d3a' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/file_item.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2034337738504e1033eaaf10-34645822',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e10342b69a2_37501107',
  'variables' => 
  array (
    'item' => 0,
    'object' => 0,
    'html' => 0,
    'thumbHeight' => 0,
    'linkUrl' => 0,
    'thumbWidth' => 0,
    'fileName' => 0,
    'params' => 0,
    'htmlAttr' => 0,
    'beEmbedMedia' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e10342b69a2_37501107')) {function content_504e10342b69a2_37501107($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_modifier_filesize')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/modifier.filesize.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>

<?php if (empty($_smarty_tpl->tpl_vars['item']->value)){?> <?php $_smarty_tpl->tpl_vars["item"] = new Smarty_variable($_smarty_tpl->tpl_vars['object']->value, null, 0);?> <?php }?>

<?php $_smarty_tpl->tpl_vars["thumbWidth"] = new Smarty_variable(130, null, 0);?>
<?php $_smarty_tpl->tpl_vars["thumbHeight"] = new Smarty_variable(98, null, 0);?>
<?php $_smarty_tpl->tpl_vars["fileName"] = new Smarty_variable((($tmp = @(($tmp = @$_smarty_tpl->tpl_vars['item']->value['filename'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['item']->value['name'] : $tmp))===null||$tmp==='' ? '' : $tmp), null, 0);?>
<?php echo smarty_function_assign_concat(array('var'=>"linkUrl",1=>$_smarty_tpl->tpl_vars['html']->value->url('/multimedia/view/'),2=>$_smarty_tpl->tpl_vars['item']->value['id']),$_smarty_tpl);?>


<div style="overflow:hidden; height:<?php echo $_smarty_tpl->tpl_vars['thumbHeight']->value;?>
px" class="imagebox"><a href="<?php echo $_smarty_tpl->tpl_vars['linkUrl']->value;?>
"><?php if (strtolower((($tmp = @$_smarty_tpl->tpl_vars['item']->value['ObjectType']['name'])===null||$tmp==='' ? '' : $tmp))=="image"){?><?php echo smarty_function_assign_associative(array('var'=>"params",'width'=>$_smarty_tpl->tpl_vars['thumbWidth']->value,'height'=>$_smarty_tpl->tpl_vars['thumbHeight']->value,'longside'=>false,'mode'=>"fill",'modeparam'=>"000000",'type'=>null,'upscale'=>false),$_smarty_tpl);?>
<?php echo smarty_function_assign_associative(array('var'=>"htmlAttr",'alt'=>$_smarty_tpl->tpl_vars['item']->value['title'],'title'=>(($tmp = @$_smarty_tpl->tpl_vars['item']->value['name'])===null||$tmp==='' ? '' : $tmp)),$_smarty_tpl);?>
<?php if (!empty($_smarty_tpl->tpl_vars['fileName']->value)){?><?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['item']->value,$_smarty_tpl->tpl_vars['params']->value,$_smarty_tpl->tpl_vars['htmlAttr']->value);?>
<?php }else{ ?><img  alt="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['mediatype'])===null||$tmp==='' ? 'notype' : $tmp);?>
" title="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['mediatype'])===null||$tmp==='' ? 'notype' : $tmp);?>
 | <?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
" src="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconset/88px/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['mediatype'])===null||$tmp==='' ? 'notype' : $tmp);?>
.png" /><?php }?><?php }elseif(strtolower((($tmp = @$_smarty_tpl->tpl_vars['item']->value['ObjectType']['name'])===null||$tmp==='' ? '' : $tmp))=="video"){?><?php echo smarty_function_assign_associative(array('var'=>"params",'presentation'=>"thumb"),$_smarty_tpl);?>
<?php if (!empty($_smarty_tpl->tpl_vars['item']->value['provider'])){?><?php echo smarty_function_assign_associative(array('var'=>"htmlAttr",'width'=>$_smarty_tpl->tpl_vars['conf']->value->media['video']['thumbWidth'],'height'=>$_smarty_tpl->tpl_vars['conf']->value->media['video']['thumbHeight'],'alt'=>$_smarty_tpl->tpl_vars['item']->value['title'],'title'=>$_smarty_tpl->tpl_vars['item']->value['name']),$_smarty_tpl);?>
<?php }else{ ?><?php $_smarty_tpl->tpl_vars["htmlAttr"] = new Smarty_variable(null, null, 0);?><?php }?><?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['item']->value,$_smarty_tpl->tpl_vars['params']->value,$_smarty_tpl->tpl_vars['htmlAttr']->value);?>
<?php }else{ ?><?php echo smarty_function_assign_associative(array('var'=>"params",'presentation'=>"thumb"),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['item']->value,$_smarty_tpl->tpl_vars['params']->value);?>
<?php }?></a></div><ul class="info_file_item bordered" style="line-height:1em;"><li style="line-height:1.2em; height:1.2em; overflow:hidden"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp);?>
</li><?php if (strtolower((($tmp = @$_smarty_tpl->tpl_vars['item']->value['ObjectType']['name'])===null||$tmp==='' ? '' : $tmp))=="image"){?><li style="line-height:1.2em; height:1.2em; overflow:hidden"><?php echo $_smarty_tpl->tpl_vars['item']->value['width'];?>
x<?php echo $_smarty_tpl->tpl_vars['item']->value['height'];?>
px, <?php echo smarty_modifier_filesize((($tmp = @$_smarty_tpl->tpl_vars['item']->value['file_size'])===null||$tmp==='' ? 0 : $tmp));?>
</li><?php }else{ ?><li style="line-height:1.2em; height:1.2em; white-space:nowrap; overflow:hidden"><?php echo $_smarty_tpl->tpl_vars['item']->value['mime_type'];?>
 <?php echo smarty_modifier_filesize((($tmp = @$_smarty_tpl->tpl_vars['item']->value['file_size'])===null||$tmp==='' ? 0 : $tmp));?>
</li><?php }?><li style="line-height:1.2em; height:1.2em; overflow:hidden"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['item']->value['created'],'%b %e, %Y');?>
</li></ul><?php }} ?>