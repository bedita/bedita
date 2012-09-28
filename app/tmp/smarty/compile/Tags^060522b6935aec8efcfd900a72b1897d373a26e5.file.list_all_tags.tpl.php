<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 12:46:20
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/tags/list_all_tags.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1810534688504f167c7503a9-82775852%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '060522b6935aec8efcfd900a72b1897d373a26e5' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/tags/list_all_tags.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1810534688504f167c7503a9-82775852',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'listTags' => 0,
    'tag' => 0,
    'href' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504f167c867fc3_67286006',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f167c867fc3_67286006')) {function content_504f167c867fc3_67286006($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#listTags a").bind("click", function() {
			var sep = ", ";
			if ($("#tagsArea").val() == "") {
				sep = "";
			}
			// check if tag already exists in textarea
			var tagInTextArea = false;
			var words = $("#tagsArea").val().split(",");
			for (i=0; i<words.length; i++) {
				if (jQuery.trim(words[i]) == jQuery.trim($(this).text())) {
					var tagInTextArea = true;
					break;
				}
			}
			if (!tagInTextArea) {
				$("#tagsArea").val(
					$("#tagsArea").val() 
					+ sep 
					+ jQuery.trim($(this).text())
				);
			}
		});
	});
</script>

<div id="listTags" class="tag graced" style="text-align:justify; line-height:1.5em;">
<?php if (!empty($_smarty_tpl->tpl_vars['listTags']->value)){?>
	<?php  $_smarty_tpl->tpl_vars["tag"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["tag"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['listTags']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["tag"]->key => $_smarty_tpl->tpl_vars["tag"]->value){
$_smarty_tpl->tpl_vars["tag"]->_loop = true;
?>
		<span class="obj <?php echo $_smarty_tpl->tpl_vars['tag']->value['status'];?>
">
			<a title="<?php echo $_smarty_tpl->tpl_vars['tag']->value['weight'];?>
" class="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['tag']->value['class'])===null||$tmp==='' ? '' : $tmp);?>
" href="<?php if (!empty($_smarty_tpl->tpl_vars['href']->value)){?><?php echo $_smarty_tpl->tpl_vars['html']->value->url('/tags/view/');?>
<?php echo $_smarty_tpl->tpl_vars['tag']->value['id'];?>
<?php }else{ ?>javascript: void(0);<?php }?>"><?php echo $_smarty_tpl->tpl_vars['tag']->value['label'];?>
</a>
		</span>
	<?php } ?>
<?php }else{ ?>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No tags found.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
</div>

				<?php }} ?>