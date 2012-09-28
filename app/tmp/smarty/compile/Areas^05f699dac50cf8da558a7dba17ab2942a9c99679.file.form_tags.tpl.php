<?php /* Smarty version Smarty-3.1.11, created on 2012-09-19 15:31:30
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_tags.tpl" */ ?>
<?php /*%%SmartyHeaderCode:119403898150535c6895fa74-13812221%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '05f699dac50cf8da558a7dba17ab2942a9c99679' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_tags.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '119403898150535c6895fa74-13812221',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50535c68a02942_70084914',
  'variables' => 
  array (
    'html' => 0,
    'object' => 0,
    'tag' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c68a02942_70084914')) {function content_50535c68a02942_70084914($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><script type="text/javascript">
<!--
$(document).ready(function(){
	
	var showTagsFirst = false;
	var showTags = false;
	$("#callTags").bind("click", function() {
		if (!showTagsFirst) {
			$("#loadingTags").show();
			$("#listExistingTags").load("<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/tags/listAllTags');?>
", function() {
				$("#loadingTags").slideUp("fast");
				$("#listExistingTags").slideDown("fast");
				$("#callTags").text("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Hide system tags<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
				showTagsFirst = true;
				showTags = true;
			});
		} else {
			if (showTags) {
				$("#listExistingTags").slideUp("fast");
				$("#callTags").text("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Show system tags<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
			} else {
				$("#listExistingTags").slideDown("fast");
				$("#callTags").text("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Hide system tags<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
			}
			showTags = !showTags;
		}
	});	
});
//-->
</script>


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tags<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="tags">

	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add comma separated words<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
	<br/>
	
	<textarea name="tags" class="autogrowarea" style="display:block; margin-bottom:10px; width:470px" id="tagsArea"><?php if (!empty($_smarty_tpl->tpl_vars['object']->value['Tag'])){?><?php  $_smarty_tpl->tpl_vars["tag"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["tag"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['object']->value['Tag']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars["tag"]->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars["tag"]->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars["tag"]->key => $_smarty_tpl->tpl_vars["tag"]->value){
$_smarty_tpl->tpl_vars["tag"]->_loop = true;
 $_smarty_tpl->tpl_vars["tag"]->iteration++;
 $_smarty_tpl->tpl_vars["tag"]->last = $_smarty_tpl->tpl_vars["tag"]->iteration === $_smarty_tpl->tpl_vars["tag"]->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["ft"]['last'] = $_smarty_tpl->tpl_vars["tag"]->last;
?><?php echo $_smarty_tpl->tpl_vars['tag']->value['label'];?>
<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['ft']['last']){?>, <?php }?><?php } ?><?php }?></textarea>
	
	<a class="BEbutton" id="callTags" href="javascript:void(0);">
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Show system tags<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	</a>
	
	<div id="loadingTags" class="generalLoading" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Loading data<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">&nbsp;</div>
	
	<div id="listExistingTags" class="tag graced" style="display: none; margin-top:5px; text-align:justify;"></div>

</fieldset><?php }} ?>