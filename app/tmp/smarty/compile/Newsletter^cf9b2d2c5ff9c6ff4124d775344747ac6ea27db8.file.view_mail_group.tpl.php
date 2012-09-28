<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:48
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/view_mail_group.tpl" */ ?>
<?php /*%%SmartyHeaderCode:97780890350534970c9feb2-60437646%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cf9b2d2c5ff9c6ff4124d775344747ac6ea27db8' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/view_mail_group.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97780890350534970c9feb2-60437646',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'item' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50534970e22db2_94209629',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50534970e22db2_94209629')) {function content_50534970e22db2_94209629($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.datepicker",false);?>


<script type="text/javascript">
<!--
var urlListSubscribers = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/listSubscribers');?>
";

function initSubscribers() {

	$("#paginateSubscribers a, #orderSubscribers a").each(function() {
		searched = "viewMailGroup";
		specificParams = $(this).attr("href");
		position = specificParams.indexOf(searched);
		if (position == -1) {
			searched = "listSubscribers";
			position = specificParams.indexOf(searched);
		}
		position += searched.length;
		specificParams = specificParams.substr(position);
		$(this).attr("rel", urlListSubscribers + specificParams).attr("href", "javascript: void(0);");
	});
	
	$("#paginateSubscribers a, #orderSubscribers a").click(function() {
		$("#loaderListSubscribers").show();
		$("#subscribers").load($(this).attr("rel"), function() {
			$("#loaderListSubscribers").hide();
			initSubscribers();
		});
	});
}

// get form params and perform a post action
function submitSubscribers(url) {
	$("#loaderListSubscribers").show();
	var arrVal = new Array();
	$("input.objectCheck:checked").each(function(index) {
		arrVal[index] = $(this).val();
	});
	
	$.post(url,
		{
			'objects_selected[]': arrVal,
			'operation': $("select[name=operation]").val(),
			'destination': $("select[name=destination]").val(),
			'newStatus': $("select[name=newStatus]").val()
		},
		function(htmlcode) {
			$("#subscribers").html(htmlcode);
			$("#loaderListSubscribers").hide();
			initSubscribers();
		}	
	);
}

$(document).ready(function() {
	
	openAtStart("#details,#divSubscribers,#addsubscribers");

	initSubscribers();
	
	$("#assocCard").click( function() {
		submitSubscribers("<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/addCardToGroup/');?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
");		
	});
	
	$("#changestatusSelected").click( function() {
		submitSubscribers("<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/changeCardStatus/');?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
");
	});

	$("#deleteSelected").bind("click", function() {
		if(!confirm("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Do you want unsubscribe selected items?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
")) 
			return false ;	
		submitSubscribers("<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/unlinkCard/');?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
");
	});
});

//-->
</script>

<?php $_smarty_tpl->tpl_vars["delparam"] = new Smarty_variable("/newsletter/deleteMailGroups", null, 0);?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('form_common_js');?>


<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"mailgroups"), 0);?>


<div class="head">
	
	<h1><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['group_name'])===null||$tmp==='' ? "New List" : $tmp);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h1>
	
</div>

<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('method'=>"viewmailgroup",'fixed'=>true), 0);?>


<div class="main">	

<form method="post" id="updateForm" action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('saveMailGroups');?>
">	

<?php echo $_smarty_tpl->getSubTemplate ("inc/list_details.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/form_subscribers.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>



<?php echo $_smarty_tpl->getSubTemplate ("inc/list_config_messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


</form>	
	
</div>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('menuright');?>
<?php }} ?>