<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:55
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/colophon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11969565504e10334f7b78-13849325%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c425b93b855f429ba5a0a0b0c3fbba92f9f8daf4' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/colophon.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11969565504e10334f7b78-13849325',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504e1033539049_96640667',
  'variables' => 
  array (
    'conf' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e1033539049_96640667')) {function content_504e1033539049_96640667($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>
<script type="text/javascript">
$(document).ready(function(){
	var cw = $("#cw").html();
	var ch = $("#ch").html();
	var rndm = Math.floor(Math.random()*2);
	if (rndm == 0) {
		$("#ch").html(cw);
		$("#cw").html(ch);
	}
});
</script>

<span class="belinks">
<a href="http://www.bedita.com/who-are-we" title="Chialab&Channelweb" target="besite">BEdita <?php echo $_smarty_tpl->tpl_vars['conf']->value->majorVersion;?>
 © </a>
	<strong id="ch"><a href="http://www.chialab.it" target="_blank">Chialab</a></strong> and <strong id="cw"><a href="http://www.channelweb.it" target="_blank"">ChannelWeb</a></strong> 
	2006-<?php echo smarty_modifier_date_format(time(),"%Y");?>

<br />
<a href="http://www.bedita.com" title="BEdita web site" target="besite">› www.bedita.com</a>
</span><?php }} ?>