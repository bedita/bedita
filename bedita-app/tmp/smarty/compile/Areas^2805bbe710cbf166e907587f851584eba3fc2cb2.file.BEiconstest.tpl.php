<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:39
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/BEiconstest.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8601302350535c525b4145-23453391%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2805bbe710cbf166e907587f851584eba3fc2cb2' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/BEiconstest.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8601302350535c525b4145-23453391',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50535c525dd733_82034929',
  'variables' => 
  array (
    'moduleName' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c525dd733_82034929')) {function content_50535c525dd733_82034929($_smarty_tpl) {?><script type="text/javascript">
$(document).ready(function(){
	$(".icons LI").css("cursor","pointer").mouseover(function() {
		var myclass = $(this).attr("rel");
		$(".secondacolonna .modules label").removeClass().addClass("<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
").addClass(""+myclass+"");
	});
});
</script>

<ul class="icons">
	<li>ecco le varie icone di stato:</li>
	<li rel="readonly">Readonly</li>
	<li rel="fixedobject">Fixed</li>
	<li rel="lock">Locked</li>
	<li rel="future">Future</li>
	<li rel="trashed">Trashed</li>
	<li rel="concurrentuser">Concurrentuser</li>
	<li rel="alert">Alert</li>
	<li rel="error">Error</li>

	<li rel="unsent">Unsent</li>
	<li rel="pending">Pending</li>
	<li rel="pendingAlert">Pending Alert</li>
	<li rel="sent">Sent</li>
	
	<li rel="save">Save</li>
</ul>

<input type="button" title="Io sono un test per la modale" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
testmodal.html'" class="modalbutton" value="modal test example" /><?php }} ?>