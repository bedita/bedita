<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:56
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/menucommands.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1017164525504e1033543c55-78180351%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a5118deaed146c8e6afb37aec71d9de9e028074d' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/multimedia/inc/menucommands.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1017164525504e1033543c55-78180351',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e103378bbb4_07856416',
  'variables' => 
  array (
    'view' => 0,
    'html' => 0,
    'fixed' => 0,
    'session' => 0,
    'currentModule' => 0,
    'moduleName' => 0,
    'back' => 0,
    'categorySearched' => 0,
    'conf' => 0,
    'media_type' => 0,
    'cat' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e103378bbb4_07856416')) {function content_504e103378bbb4_07856416($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
?><?php $_smarty_tpl->tpl_vars['method'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['view']->value->action)===null||$tmp==='' ? 'index' : $tmp), null, 0);?>



<?php if (!empty($_smarty_tpl->tpl_vars['view']->value->action)&&$_smarty_tpl->tpl_vars['view']->value->action=="view"){?>
<script type="text/javascript">
	var urlView = '<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/multimedia/view/");?>
' ;

	$(document).ready(function() { 

		$("#collision").hide();
		var optionsForm = { 
			error:		showResponse,  // post-submit callback  
			success:		showResponse,  // post-submit callback  
			dataType:		'html',        // 'xml', 'script', or 'json' (expected server response type)
			url: "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/multimedia/saveAjax');?>
"
		} ;
	
		$("div.insidecol input[name='saveMedia']").click(function() { 
			if ( $('#concurrenteditors #editorsList').children().size() > 0 ) { 
				var answer = confirm("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
More users are editing this object. Continue?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
			    if (answer) { 
			    	$(".secondacolonna .modules label").addClass("submitForm");
			    	$('#updateForm').ajaxSubmit(optionsForm);
			    } 
			} else if ( $('.publishingtree input:checked').val() === undefined ) {	
				var answer = confirm("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
This content is not on publication tree. Continue?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
			    if (answer) { 
			    	$(".secondacolonna .modules label").addClass("submitForm");
			    	$('#updateForm').ajaxSubmit(optionsForm);
			    } 
			} else { 
		    	$(".secondacolonna .modules label").addClass("submitForm");
				$('#updateForm').ajaxSubmit(optionsForm);
			} 
    		return false;  
		} );

	} );

	function showResponse(data) { 
		$("#collision").html(data);
		$("#collision").show();
	} 
</script>
<?php }?>

<div class="secondacolonna <?php if (!empty($_smarty_tpl->tpl_vars['fixed']->value)){?>fixed<?php }?>">
	
	<?php if (!empty($_smarty_tpl->tpl_vars['view']->value->action)&&$_smarty_tpl->tpl_vars['view']->value->action!="index"){?>
		<?php $_smarty_tpl->tpl_vars["back"] = new Smarty_variable($_smarty_tpl->tpl_vars['session']->value->read("backFromView"), null, 0);?>
	<?php }else{ ?>
		<?php echo smarty_function_assign_concat(array('var'=>"back",1=>$_smarty_tpl->tpl_vars['html']->value->url('/'),2=>$_smarty_tpl->tpl_vars['currentModule']->value['url']),$_smarty_tpl);?>

	<?php }?>

	<div class="modules">
		<label class="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
" rel="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['currentModule']->value['label'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	</div> 
	
	<?php if (!empty($_smarty_tpl->tpl_vars['view']->value->action)&&$_smarty_tpl->tpl_vars['view']->value->action!="index"){?>
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 " name="saveMedia" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
clone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" name="delete" id="delBEObject" />
	</div>
	
		<?php echo $_smarty_tpl->tpl_vars['view']->value->element('prevnext');?>

	
	<?php }?>

	<?php $_smarty_tpl->tpl_vars['cat'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['categorySearched']->value)===null||$tmp==='' ? '' : $tmp), null, 0);?>

	<?php if ($_smarty_tpl->tpl_vars['view']->value->action=="index"){?>
		<ul class="menuleft insidecol catselector">
			<li><a href="javascript:void(0)" onClick="$('#mediatypes').slideToggle();"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Select by type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li>
				<ul id="mediatypes" <?php if (empty($_smarty_tpl->tpl_vars['categorySearched']->value)){?>style="display:none"<?php }?>>
					
					<?php  $_smarty_tpl->tpl_vars["media_type"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["media_type"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->mediaTypes; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["media_type"]->key => $_smarty_tpl->tpl_vars["media_type"]->value){
$_smarty_tpl->tpl_vars["media_type"]->_loop = true;
?>
					<li class="ico_<?php echo $_smarty_tpl->tpl_vars['media_type']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['cat']->value==$_smarty_tpl->tpl_vars['media_type']->value){?>on<?php }?>" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/multimedia');?>
/index/category:<?php echo $_smarty_tpl->tpl_vars['media_type']->value;?>
">
						<?php echo $_smarty_tpl->tpl_vars['media_type']->value;?>

					</li>
					<?php } ?>
					<li class="ico_all" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/multimedia');?>
">
						All
					</li>
				
				</ul>
		</ul>
	<?php }?>	



</div><?php }} ?>