<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:53
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/translations/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:912442445504f16a22f0ad3-38006799%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2ea06e7fea7bae2acb32cf3093d320d42e7feaf6' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/translations/index.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '912442445504f16a22f0ad3-38006799',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504f16a273d657_57404314',
  'variables' => 
  array (
    'html' => 0,
    'view' => 0,
    'object_translation' => 0,
    'object_master' => 0,
    'conf' => 0,
    'val' => 0,
    'langSelected' => 0,
    'label' => 0,
    'statusSelected' => 0,
    'objectTypes' => 0,
    'key' => 0,
    'objectTypeIdSelected' => 0,
    'objectIdSelected' => 0,
    'translations' => 0,
    'beToolbar' => 0,
    'mtitle' => 0,
    'oid' => 0,
    'olang' => 0,
    'ot' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504f16a273d657_57404314')) {function content_504f16a273d657_57404314($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
?><script type="text/javascript">
var urlDelete = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('deleteTranslations/');?>
" ;
var message = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete the item?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" ;
var messageSelected = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete selected items?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" ;
var URLBase = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('index/');?>
" ;


$(document).ready(function() {

	$(".indexlist TD").not(".checklist").not(".noclick").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );

	$("#deleteSelected").bind("click", delObjects);
	$("a.delete").bind("click", function() {
		delObject($(this).attr("title"));
	});

	$("#changestatusSelected").bind("click",changeStatusTranslations);

});

function delObject(id) {
	if(!confirm(message)) return false ;
	$("#objects_selected").attr("value",id);
	$("#formObject").attr("action", urlDelete) ;
	$("#formObject").get(0).submit() ;
	return false ;
}
function delObjects() {
	if(!confirm(messageSelected)) return false ;
	var oToDel = "";
	var checkElems = document.getElementsByName('object_chk');
	for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) oToDel+= ","+checkElems[i].title; }
	oToDel = (oToDel=="") ? "" : oToDel.substring(1);
	$("#objects_selected").attr("value",oToDel);
	$("#formObject").attr("action", urlDelete) ;
	$("#formObject").get(0).submit() ;
	return false ;
}
function changeStatusTranslations() {
	var status = $("#newStatus").val();
	if(status != "") {
		var oToDel = "";
		var checkElems = document.getElementsByName('object_chk');
		for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) oToDel+= ","+checkElems[i].title; }
		oToDel = (oToDel=="") ? "" : oToDel.substring(1);
		$("#objects_selected").attr("value",oToDel);
		$("#formObject").attr("action", '<?php echo $_smarty_tpl->tpl_vars['html']->value->url('changeStatusTranslations/');?>
' + status) ;
		$("#formObject").get(0).submit() ;
		return false ;
	}
}

</script>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"index"), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"index",'fixed'=>true), 0);?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('toolbar');?>


<div class="mainfull">
	
	<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/translations/index');?>
" id="formObject">

	<input type="hidden" name="data[id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_translation']->value['id']['status'])===null||$tmp==='' ? '' : $tmp);?>
"/>
	<input type="hidden" name="data[master_id]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_master']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
"/>
	<input type="hidden" name="objects_selected" id="objects_selected"/>

	
<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
filters<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
<div>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Show translations in<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: &nbsp;
	<select name="data[translation_lang]">
		<option value=""></option>
	<?php  $_smarty_tpl->tpl_vars['label'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['label']->_loop = false;
 $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->langOptions; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['label']->key => $_smarty_tpl->tpl_vars['label']->value){
$_smarty_tpl->tpl_vars['label']->_loop = true;
 $_smarty_tpl->tpl_vars['val']->value = $_smarty_tpl->tpl_vars['label']->key;
?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['langSelected']->value==$_smarty_tpl->tpl_vars['val']->value){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</option>
	<?php } ?>
	</select>
	
	&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
with status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: &nbsp;
	<select name="data[translation_status]">
	<option value=""></option>
	<option value="on"<?php if ($_smarty_tpl->tpl_vars['statusSelected']->value=='on'){?> selected="selected"<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
	<option value="off"<?php if ($_smarty_tpl->tpl_vars['statusSelected']->value=='off'){?> selected="selected"<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
off<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
	<option value="draft"<?php if ($_smarty_tpl->tpl_vars['statusSelected']->value=='draft'){?> selected="selected"<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
draft<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
	<option value="required"<?php if ($_smarty_tpl->tpl_vars['statusSelected']->value=='required'){?> selected="selected"<?php }?>><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
required<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
	</select>
	
	&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
for object type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: &nbsp;
	<select name="data[translation_object_type_id]">
	<option value=""></option>
	<?php  $_smarty_tpl->tpl_vars["objectTypes"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["objectTypes"]->_loop = false;
 $_smarty_tpl->tpl_vars["key"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->objectTypes; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["objectTypes"]->key => $_smarty_tpl->tpl_vars["objectTypes"]->value){
$_smarty_tpl->tpl_vars["objectTypes"]->_loop = true;
 $_smarty_tpl->tpl_vars["key"]->value = $_smarty_tpl->tpl_vars["objectTypes"]->key;
?>
	<?php if (!empty($_smarty_tpl->tpl_vars['objectTypes']->value['model'])&&is_numeric($_smarty_tpl->tpl_vars['key']->value)){?>
		<option value="<?php echo $_smarty_tpl->tpl_vars['objectTypes']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['objectTypes']->value['module_name'];?>
"<?php if ($_smarty_tpl->tpl_vars['objectTypeIdSelected']->value==$_smarty_tpl->tpl_vars['objectTypes']->value['id']){?> selected="selected"<?php }?>> <?php echo $_smarty_tpl->tpl_vars['objectTypes']->value['name'];?>
</option>
	<?php }?>
	<?php } ?>
	</select>

	&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
of master id<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:&nbsp;
	<input type="text" name="data[translation_object_id]" style="width:25px"
	value="<?php echo $_smarty_tpl->tpl_vars['objectIdSelected']->value;?>
"/>
	&nbsp;<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
go<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
	
<?php if (!empty($_smarty_tpl->tpl_vars['translations']->value)){?>
	<hr />
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Go to page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changePageSelect('pagSelectBottom');?>
 
		&nbsp;&nbsp;&nbsp;
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Dimensions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->changeDimSelect('selectTop');?>
 &nbsp;
<?php }?>
	
	</div>
	<table class="indexlist">
	<?php $_smarty_tpl->_capture_stack[0][] = array("theader", null, null); ob_start(); ?>
		<tr>
			<th></th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('title','master title');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('LangText.title','title');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('object_type_id','type');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('LangText.lang','language');?>
</th>
			<th><?php echo $_smarty_tpl->tpl_vars['beToolbar']->value->order('LangText.status','Status');?>
</th>
		</tr>
	<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

		<?php echo Smarty::$_smarty_vars['capture']['theader'];?>
	
	
		<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['name'] = "i";
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['translations']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total']);
?>
	

		<?php $_smarty_tpl->tpl_vars["oid"] = new Smarty_variable($_smarty_tpl->tpl_vars['translations']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['LangText']['object_id'], null, 0);?>
		<?php $_smarty_tpl->tpl_vars["olang"] = new Smarty_variable($_smarty_tpl->tpl_vars['translations']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['LangText']['lang'], null, 0);?>
		<?php $_smarty_tpl->tpl_vars["ot"] = new Smarty_variable($_smarty_tpl->tpl_vars['translations']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['BEObject']['object_type_id'], null, 0);?>
		<?php $_smarty_tpl->tpl_vars["mtitle"] = new Smarty_variable($_smarty_tpl->tpl_vars['translations']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['BEObject']['title'], null, 0);?>
		
		<tr class="obj <?php echo $_smarty_tpl->tpl_vars['translations']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['LangText']['status'];?>
">
			<td class="checklist">
				<input  type="checkbox" name="object_chk" class="objectCheck" title="<?php echo $_smarty_tpl->tpl_vars['translations']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['LangText']['id'];?>
" />
			</td>
			<td>
				<?php echo smarty_modifier_truncate((($tmp = @$_smarty_tpl->tpl_vars['mtitle']->value)===null||$tmp==='' ? '<i>[no title]</i>' : $tmp),38,true);?>
 &nbsp;
			</td>
			<td><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('view/');?>
<?php echo $_smarty_tpl->tpl_vars['oid']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['olang']->value;?>
"><?php echo smarty_modifier_truncate((($tmp = @$_smarty_tpl->tpl_vars['translations']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['LangText']['title'])===null||$tmp==='' ? '<i>[no title]</i>' : $tmp),38,true);?>
</a></td>
			<td>
				<span class="listrecent <?php echo mb_strtolower($_smarty_tpl->tpl_vars['conf']->value->objectTypes[$_smarty_tpl->tpl_vars['ot']->value]['name'], 'UTF-8');?>
">&nbsp;</span>
				<?php echo $_smarty_tpl->tpl_vars['conf']->value->objectTypes[$_smarty_tpl->tpl_vars['ot']->value]['model'];?>

			</td>
			<td><?php echo $_smarty_tpl->tpl_vars['olang']->value;?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['translations']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['LangText']['status'];?>
</td>

		</tr>
		<?php endfor; else: ?>
			<tr><td colspan="100" class="noclick" style="padding:30px"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No items found<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td></tr>
		<?php endif; ?>

<?php if (($_smarty_tpl->getVariable('smarty')->value['section']['i']['total'])>=10){?>
	<?php echo Smarty::$_smarty_vars['capture']['theader'];?>

<?php }?>


</table>

<br />

<?php if (!empty($_smarty_tpl->tpl_vars['translations']->value)){?>
	
	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Operations on above records<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<div>
		<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
(un)select all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
		<hr />
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
change status to:<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
	<select style="width:75px" id="newStatus" data="newStatus">
									<option value=""> -- </option>
									<option value="on"> ON </option>
									<option value="off"> OFF </option>
									<option value="draft"> DRAFT </option>
									<option value="required"> REQUIRED </option>
								</select>
				<input id="changestatusSelected" type="button" value=" ok " />
		<hr />
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete selected items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;<input id="deleteSelected" type="button" value=" ok "/>
		<hr />
	</div>
<?php }?>

</form>
</div><?php }} ?>