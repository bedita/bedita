<?php /* Smarty version Smarty-3.1.11, created on 2012-09-10 16:47:52
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_file_item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:65529067504dfd98e83295-53090519%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f8b188fc6d1a508508d9f4f5e540dc85f6d8a6b1' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_file_item.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '65529067504dfd98e83295-53090519',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'relation' => 0,
    'html' => 0,
    'item' => 0,
    'thumbWidth' => 0,
    'thumbHeight' => 0,
    'params' => 0,
    'htmlAttr' => 0,
    'beEmbedMedia' => 0,
    'conf' => 0,
    'myStyle' => 0,
    'attributes' => 0,
    'linkUrl' => 0,
    'session' => 0,
    'priority' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504dfd99289cd9_38652062',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd99289cd9_38652062')) {function content_504dfd99289cd9_38652062($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_function_array_add')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.array_add.php';
if (!is_callable('smarty_modifier_escape')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.escape.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php $_smarty_tpl->tpl_vars["thumbWidth"] = new Smarty_variable(130, null, 0);?>
<?php $_smarty_tpl->tpl_vars["thumbHeight"] = new Smarty_variable(98, null, 0);?>
<?php if (empty($_smarty_tpl->tpl_vars['relation']->value)){?>
	<?php $_smarty_tpl->tpl_vars['relation'] = new Smarty_variable("attach", null, 0);?>
<?php }?>

<?php echo smarty_function_assign_concat(array('var'=>"linkUrl",1=>$_smarty_tpl->tpl_vars['html']->value->url('/multimedia/view/'),2=>$_smarty_tpl->tpl_vars['item']->value['id']),$_smarty_tpl);?>


<?php echo smarty_function_assign_concat(array('var'=>"imageAltAttribute",1=>"alt='",2=>$_smarty_tpl->tpl_vars['item']->value['title'],3=>"'"),$_smarty_tpl);?>


<?php echo smarty_function_assign_associative(array('var'=>"params",'presentation'=>"thumb",'width'=>$_smarty_tpl->tpl_vars['thumbWidth']->value,'height'=>$_smarty_tpl->tpl_vars['thumbHeight']->value,'longside'=>false,'mode'=>"fill",'modeparam'=>"000000",'type'=>null,'upscale'=>false),$_smarty_tpl);?>

<?php echo smarty_function_assign_associative(array('var'=>"htmlAttr",'alt'=>$_smarty_tpl->tpl_vars['item']->value['title'],'title'=>$_smarty_tpl->tpl_vars['item']->value['name']),$_smarty_tpl);?>


<script type="text/javascript">
$(document).ready(function(){
	$(".info_file_item").change(function() {
		$(this).parents(".multimediaitem").css("background-color","gold").find(".mod").val(1);
	})
});
</script>

<input type="hidden" class="media_nickname" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['nickname'];?>
" /><input type="hidden" name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['relation']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][id]" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" /><input type="hidden" class="mod" name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['relation']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][modified]" value="0" /><div style="width:<?php echo $_smarty_tpl->tpl_vars['thumbWidth']->value;?>
px; height:<?php echo $_smarty_tpl->tpl_vars['thumbHeight']->value;?>
px" class="imagebox"><?php if (strtolower($_smarty_tpl->tpl_vars['item']->value['ObjectType']['name'])=="image"){?><?php if (empty($_smarty_tpl->tpl_vars['item']->value['file_size'])){?><?php if (($_smarty_tpl->tpl_vars['thumbHeight']->value<$_smarty_tpl->tpl_vars['item']->value['height'])||($_smarty_tpl->tpl_vars['thumbWidth']->value<$_smarty_tpl->tpl_vars['item']->value['width'])){?><?php echo smarty_function_array_add(array('var'=>"htmlAttr",'width'=>$_smarty_tpl->tpl_vars['thumbWidth']->value,'height'=>$_smarty_tpl->tpl_vars['thumbHeight']->value),$_smarty_tpl);?>
<?php }?><?php }?><?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['item']->value,$_smarty_tpl->tpl_vars['params']->value,$_smarty_tpl->tpl_vars['htmlAttr']->value);?>
<?php }elseif(((($tmp = @$_smarty_tpl->tpl_vars['item']->value['provider'])===null||$tmp==='' ? false : $tmp))){?><?php echo smarty_function_assign_concat(array('var'=>"myStyle",1=>"width:",2=>$_smarty_tpl->tpl_vars['conf']->value->media['video']['thumbWidth'],3=>"px; ",4=>"height:",5=>$_smarty_tpl->tpl_vars['conf']->value->media['video']['thumbHeight'],6=>"px;"),$_smarty_tpl);?>
<?php echo smarty_function_assign_associative(array('var'=>"attributes",'style'=>$_smarty_tpl->tpl_vars['myStyle']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['item']->value,$_smarty_tpl->tpl_vars['params']->value,$_smarty_tpl->tpl_vars['attributes']->value);?>
<?php }elseif(strtolower($_smarty_tpl->tpl_vars['item']->value['ObjectType']['name'])=="audio"){?><a href="<?php echo $_smarty_tpl->tpl_vars['linkUrl']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['session']->value->webroot;?>
img/iconset/88px/audio.png" /></a><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['item']->value,$_smarty_tpl->tpl_vars['params']->value);?>
<?php }?></div><label class="evidence"><input type="text" class="priority" style="text-align:left; margin-left:0px;"name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['relation']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][priority]" value="<?php echo (($tmp = @(($tmp = @$_smarty_tpl->tpl_vars['item']->value['priority'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['priority']->value : $tmp))===null||$tmp==='' ? 1 : $tmp);?>
" size="3" maxlength="3"/></label><ul class="info_file_item"><li><input class="info_file_item" style="border:0px;" type="text" value="<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['title'], 'htmlall', 'UTF-8'))===null||$tmp==='' ? '' : $tmp);?>
"name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['relation']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][title]" /></li><li><textarea class="info_file_item" style="width:100%; border:0px; border-bottom:0px solid silver;"name="data[RelatedObject][<?php echo $_smarty_tpl->tpl_vars['relation']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][description]"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['description'])===null||$tmp==='' ? '' : $tmp);?>
</textarea><br /><table style="width:100%; margin-top:5px" class="ultracondensed"><tr><td><a title="info" rel="<?php echo $_smarty_tpl->tpl_vars['linkUrl']->value;?>
 .multimediaiteminside" style="padding:2px 6px 2px 6px !important" class="BEbutton modalbutton"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td><td><a title="edit" href="<?php echo $_smarty_tpl->tpl_vars['linkUrl']->value;?>
" style="padding:2px 6px 2px 6px !important" class="BEbutton" target="_blank"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
edit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td><td><a title="remove" href="javascript: void(0);" style="padding:2px 6px 2px 6px !important" class="BEbutton" onclick="removeItem('item_<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
')"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
x<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td></tr></table></li></ul>
<?php }} ?>