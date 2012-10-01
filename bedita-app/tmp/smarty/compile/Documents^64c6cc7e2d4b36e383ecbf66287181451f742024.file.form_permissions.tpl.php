<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_permissions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1345469289504dfd9c6ab669-12388229%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '64c6cc7e2d4b36e383ecbf66287181451f742024' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_permissions.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1345469289504dfd9c6ab669-12388229',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfd9c8576c7_58179839',
  'variables' => 
  array (
    'html' => 0,
    'conf' => 0,
    'permItem' => 0,
    'permKey' => 0,
    'el' => 0,
    'i' => 0,
    'perm' => 0,
    'objPermReverse' => 0,
    'permVal' => 0,
    'permLabel' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfd9c8576c7_58179839')) {function content_504dfd9c8576c7_58179839($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<script type="text/javascript">
var urlLoad = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/loadUsersGroupsAjax');?>
";
var permissionLoaded = false;
var permissions = new Array();

<?php  $_smarty_tpl->tpl_vars["permItem"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["permItem"]->_loop = false;
 $_smarty_tpl->tpl_vars["permKey"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->objectPermissions; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["permItem"]->key => $_smarty_tpl->tpl_vars["permItem"]->value){
$_smarty_tpl->tpl_vars["permItem"]->_loop = true;
 $_smarty_tpl->tpl_vars["permKey"]->value = $_smarty_tpl->tpl_vars["permItem"]->key;
?>
	permissions[<?php echo $_smarty_tpl->tpl_vars['permItem']->value;?>
] = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['permKey']->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
<?php } ?>

$(document).ready(function(){
	
	$("#permissionsTab").click(function() {
		if (!permissionLoaded) {
			loadUserGroupAjax(urlLoad);
		}
	});

	if ($("#permissionsTab h2").attr("class") == "open documents") {
		loadUserGroupAjax(urlLoad);
	}
	
	$("#cmdAddGroupPerm").click(function() {
		var name = $("#inputAddPermGroup").val();
		var type = "group";
		var perm = $("#selectGroupPermission").val();
		var index = $("#frmCustomPermissions").find("tr[id^='permTR_']:last").attr("id");
		
		if (index == undefined) {
			index = 0;
		} else {
			indexArr = index.split("_");
			index = parseInt(indexArr[1]) + 1;
		}
		
		var htmlBlock = "<tr id=\"permTR_" + index + "\"><td>" + name + "</td>" +
						"<td>" + permissions[perm] + "</td>" + 
						"<td>" +
						"<input type=\"hidden\" name=\"data[Permission]["+index+"][flag]\" value=\""+perm+"\"/>" + 
						"<input type=\"hidden\" name=\"data[Permission]["+index+"][switch]\" value=\""+type+"\"/>" +
						"<input type=\"hidden\" name=\"data[Permission]["+index+"][name]\" value=\""+name+"\"/>" +
						"<input type=\"button\" name=\"deletePerms\" value=\" x \"/>"+
						"</td></tr>";
		
		$("#frmCustomPermissions").find("tr:last").after(htmlBlock);
		refreshRemovePermButton();
	});

	refreshRemovePermButton();
});

function refreshRemovePermButton() {
	$("#frmCustomPermissions").find("input[type='button']").click(function() {
		$(this).parents("tr").remove();
	});
}

function loadUserGroupAjax(url) {
	$("#loaderug").show();
	$("#inputAddPermGroup").load(url, { itype:'group' }, function() {
		$("#loaderug").hide();
		permissionLoaded = true;
	});
}
</script>



<div class="tab" id="permissionsTab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Permissions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<fieldset id="permissions">
<div class="loader" id="loaderug"></div>

<table class="indexlist" border=0 id="frmCustomPermissions">
<?php if (!empty($_smarty_tpl->tpl_vars['el']->value['Permission'])){?>
<tr>
	<th style="width:190px"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	<th style="width:190px"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
permission<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	<th>&nbsp;</th>
</tr>

	<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['el']->value['Permission']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
	<?php $_smarty_tpl->tpl_vars["perm"] = new Smarty_variable($_smarty_tpl->tpl_vars['el']->value['Permission'][$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']], null, 0);?>
	<?php $_smarty_tpl->tpl_vars["i"] = new Smarty_variable($_smarty_tpl->getVariable('smarty')->value['section']['i']['index'], null, 0);?>
		
		<tr id="permTR_<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
">
			<td><?php echo $_smarty_tpl->tpl_vars['perm']->value['name'];?>
</td>
			<td>
			<?php $_smarty_tpl->tpl_vars["objPermReverse"] = new Smarty_variable(array_flip($_smarty_tpl->tpl_vars['conf']->value->objectPermissions), null, 0);?>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['objPermReverse']->value[$_smarty_tpl->tpl_vars['perm']->value['flag']];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</td>
			<td>
				<input type="hidden" name="data[Permission][<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
][flag]" value="<?php echo $_smarty_tpl->tpl_vars['perm']->value['flag'];?>
"/>
				<input type="hidden" name="data[Permission][<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
][switch]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['perm']->value['switch']);?>
"/>
				<input type="hidden" name="data[Permission][<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
][name]" value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['perm']->value['name']);?>
"/>
				<input type="button" name="deletePerms" value=" x "/>
			</td>
		</tr>	
		
	<?php endfor; endif; ?>
<?php }else{ ?>
<tr>
	<th style="width:190px"></th>
	<th style="width:190px"></th>
	<th>&nbsp;</th>
</tr>
<?php }?>
</table>

<table class="indexlist" border=0 id="selCustomPermissions">
<tr>
	<th style="width:190px"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add group<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</th>
	<th style="width:190px"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
permission<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	<th>&nbsp;</th>
</tr>

<tr id="addPermGroupTR" class="ignore">
	<td style="white-space:nowrap">
		<select id="inputAddPermGroup" name="name"></select>
	</td>

	<td>
		<select id="selectGroupPermission" name="groupPermission">
			<?php  $_smarty_tpl->tpl_vars["permVal"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["permVal"]->_loop = false;
 $_smarty_tpl->tpl_vars["permLabel"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->objectPermissions; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["permVal"]->key => $_smarty_tpl->tpl_vars["permVal"]->value){
$_smarty_tpl->tpl_vars["permVal"]->_loop = true;
 $_smarty_tpl->tpl_vars["permLabel"]->value = $_smarty_tpl->tpl_vars["permVal"]->key;
?>
			<option value="<?php echo $_smarty_tpl->tpl_vars['permVal']->value;?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['permLabel']->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
			<?php } ?>
		</select>
	</td>
	
	<td><input type="button" id="cmdAddGroupPerm" value=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 "/></td>
</tr>
</table>
</fieldset>

<?php }} ?>