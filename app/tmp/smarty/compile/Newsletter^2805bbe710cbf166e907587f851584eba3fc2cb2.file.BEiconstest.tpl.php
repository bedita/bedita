<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:12:49
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/BEiconstest.tpl" */ ?>
<?php /*%%SmartyHeaderCode:192561195850534971a513d1-36563769%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2805bbe710cbf166e907587f851584eba3fc2cb2' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/BEiconstest.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '192561195850534971a513d1-36563769',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'moduleName' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_50534971aa9585_53142057',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50534971aa9585_53142057')) {function content_50534971aa9585_53142057($_smarty_tpl) {?><script type="text/javascript">
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