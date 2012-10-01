<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:04
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/home/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1664591163504dfcac1530f6-30419032%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '056679ce5d544c02c594af46c92ce16c972267e5' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/home/login.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1664591163504dfcac1530f6-30419032',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfcac45b749_14053681',
  'variables' => 
  array (
    'html' => 0,
    'conf' => 0,
    'view' => 0,
    'beurl' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfcac45b749_14053681')) {function content_504dfcac45b749_14053681($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php echo $_smarty_tpl->tpl_vars['html']->value->script("form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.cmxforms",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.metadata",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.validate",false);?>


<script type="text/javascript">
<!--
$.validator.setDefaults({ 
	/*submitHandler: function() { alert("submitted!"); },*/
	success: function(label) { label.html("&nbsp;").addClass("checked");}
});
$().ready(function() { 
	$("#loginform").validate();
	$("#userid").focus();
});
//-->
</script>

	
<div class="primacolonna">
	 <div class="modules"><label class="bedita" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['conf']->value->projectName)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['conf']->value->userVersion : $tmp);?>
</label></div>
	 
	 
	<div class="insidecol colophon">	
	
		<?php echo $_smarty_tpl->tpl_vars['view']->value->element('colophon');?>

	
	</div>
	 
</div>


<div class="secondacolonna">

	<div class="modules">
	   <label class="admin"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Login<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
	</div> 

</div>


<div style="width:180px; margin-left:310px; padding-top:25px;">
<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/authentications/login');?>
" method="post" name="loginForm" id="loginForm" class="cmxform" style="padding-left:5px;">
	<fieldset>
		<input type="hidden" name="data[login][URLOK]" value="<?php echo $_smarty_tpl->tpl_vars['beurl']->value->here();?>
" id="loginURLOK" />
		
		<label class="block" id="luserid" for="userid"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Username<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
		<input class="big" tabindex="1" style="width:103px" type="text" name="data[login][userid]" id="userid" class="{ required:true}" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Username is required<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
		<label class="block" id="lpasswd" for="passwd"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
		<input class="big" tabindex="2" style="width:103px; margin-bottom:10px;" type="password" name="data[login][passwd]" id="passwd" class="{ required:true}" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Password is required<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
		
		<input class="bemaincommands" tabindex="2" type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Enter<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
	</fieldset>
	</form>
</div>

<div class="quartacolonna" style="border-left:1px solid gray; padding:120px 0px 0px 10px; width:420px; left:440px; top:20px;">

	<label class="block"><a href='javascript:void(0)' onClick="$('#pswforget').toggle('fast')"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Forgotten username or password?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></label>
	<div id="pswforget" style="display:none">
		<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/authentications/recoverPassword');?>
">
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Write your email here<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:&nbsp;
		<br />
		<input class="big" style="width:153px" type="text" name="data[email]"/>
		<input class="bemaincommands" type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Send<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>

		<hr />
		<?php if (isset($_smarty_tpl->tpl_vars['conf']->value->projectAdmin)){?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
or<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <label><a href="mailto:<?php echo $_smarty_tpl->tpl_vars['conf']->value->projectAdmin;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contact the project admin<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></label><?php }?>
	</div>

</div>


<?php }} ?>