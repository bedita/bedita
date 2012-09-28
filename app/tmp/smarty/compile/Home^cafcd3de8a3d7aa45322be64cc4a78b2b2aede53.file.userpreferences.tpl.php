<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:15
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/home/inc/userpreferences.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1801287750504dfcc845f1d2-47457796%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cafcd3de8a3d7aa45322be64cc4a78b2b2aede53' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/home/inc/userpreferences.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1801287750504dfcc845f1d2-47457796',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfcc865af52_65897998',
  'variables' => 
  array (
    'html' => 0,
    'BEAuthUser' => 0,
    'conf' => 0,
    'key' => 0,
    'item' => 0,
    'userdetail' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfcc865af52_65897998')) {function content_504dfcc865af52_65897998($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/home/editProfile');?>
" method="post">
<table class="condensed">
<tr>
	<td><label class="simple" id="lrealname" for="realname"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label></td>
	<td>
	<input type="hidden" name="data[User][id]" value="<?php echo $_smarty_tpl->tpl_vars['BEAuthUser']->value['id'];?>
"/>
	<input type="hidden" name="data[User][userid]" value="<?php echo $_smarty_tpl->tpl_vars['BEAuthUser']->value['userid'];?>
"/>
	<input type="hidden" name="data[User][valid]" value="<?php echo $_smarty_tpl->tpl_vars['BEAuthUser']->value['valid'];?>
"/>
	<input type="text" id="realname"  name="data[User][realname]" value="<?php echo $_smarty_tpl->tpl_vars['BEAuthUser']->value['realname'];?>
"  />
	</td>
</tr>
<tr>
	<td><label class="simple" id="lemail" for="email"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label></td>
	<td><input type="text" id="email" name="data[User][email]" value="<?php echo $_smarty_tpl->tpl_vars['BEAuthUser']->value['email'];?>
" class="{ email:true}" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Use a valid email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/></td>
</tr>

<tr>
	<td><label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
language<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label></td>
	<td>
	<select name="data[User][lang]">
		<option value="">--</option>
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->langsSystem; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['key']->value==$_smarty_tpl->tpl_vars['BEAuthUser']->value['lang']){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</option>
	<?php } ?>
	</select>
	</td>
</tr>

<tr><td colspan=2><hr /></td></tr>

<tr>
	<td><label class="simple"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
old psw<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label></td>
	<td><input type="password" name="oldpwd" value="" id="oldpwd" class="<?php if (isset($_smarty_tpl->tpl_vars['userdetail']->value)){?>{ password:true}<?php }else{ ?>{ required:true,password:true}<?php }?>"/></td>
</tr>
<tr>
	<td><label class="simple"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
new psw<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label></td>
	<td><input type="password" name="pwd" value="" id="pwd" class="<?php if (isset($_smarty_tpl->tpl_vars['userdetail']->value)){?>{ password:true}<?php }else{ ?>{ required:true,password:true}<?php }?>"></td>
</tr>
<tr>
	<td><label class="simple"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
new again<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label></td>
	<td><input type="password" name="data[User][passwd]" value="" class="{ equalTo:'#pwd'}" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Passwords should be equal<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/></td>
</tr>

<tr><td colspan=2><hr /></td></tr>

</table>

<script type="text/javascript">
$(document).ready(function(){
$(".checko").change(function(){
	var target = $(this).attr('rel');
	if ($(this).is(':checked'))	{
	  	$('#'+target).show().val(['all']);
	} else {
		$('#'+target).hide().val(['never']);
	}
});
});
</script>

<table class="condensed">
<tr>
	<td colspan=2><label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
notify me by email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label></td>
</tr>
<tr>
	<td>
		<input class="checko" name="comments" value="1" rel="usercomments" type="checkbox" <?php if (!empty($_smarty_tpl->tpl_vars['BEAuthUser']->value['comments'])&&($_smarty_tpl->tpl_vars['BEAuthUser']->value['comments']!="never")){?> checked<?php }?>>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
new comments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	</td>
	<td>
		<select id="usercomments" name="data[User][comments]" <?php if (empty($_smarty_tpl->tpl_vars['BEAuthUser']->value['comments'])||($_smarty_tpl->tpl_vars['BEAuthUser']->value['comments']=="never")){?>style="display:none"<?php }?>>
			<option value="mine"<?php if ($_smarty_tpl->tpl_vars['BEAuthUser']->value['comments']=="mine"){?> selected<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
on my stuff only<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			<option value="all"<?php if ($_smarty_tpl->tpl_vars['BEAuthUser']->value['comments']=="all"){?> selected<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		<input class="checko" name="notes" value="1" rel="usernotes" type="checkbox" <?php if (!empty($_smarty_tpl->tpl_vars['BEAuthUser']->value['notes'])&&($_smarty_tpl->tpl_vars['BEAuthUser']->value['notes']!="never")){?> checked<?php }?>>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
new notes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
	<td>
		<select id="usernotes" name="data[User][notes]" <?php if (empty($_smarty_tpl->tpl_vars['BEAuthUser']->value['notes'])||($_smarty_tpl->tpl_vars['BEAuthUser']->value['notes']=="never")){?>style="display:none"<?php }?>> 
			<option value="mine"<?php if ($_smarty_tpl->tpl_vars['BEAuthUser']->value['notes']=="mine"){?> selected<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
on my stuff only<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			<option value="all"<?php if ($_smarty_tpl->tpl_vars['BEAuthUser']->value['notes']=="all"){?> selected<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan=2>
		<input type="checkbox" name="data[User][notify_changes]" value="1"<?php if ($_smarty_tpl->tpl_vars['BEAuthUser']->value['notify_changes']==1){?> checked<?php }?>>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
changes on my contents<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	</td>
</tr>
</table>
<hr />

<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
save profile<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />
</form>
<?php }} ?>