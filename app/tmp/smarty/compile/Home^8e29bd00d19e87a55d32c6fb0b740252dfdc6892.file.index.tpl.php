<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:36:14
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/home/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1358490229504dfcc7850c18-47083222%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8e29bd00d19e87a55d32c6fb0b740252dfdc6892' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/home/index.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1358490229504dfcc7850c18-47083222',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_504dfcc7e47d47_74478276',
  'variables' => 
  array (
    'html' => 0,
    'view' => 0,
    'BEAuthUser' => 0,
    'options' => 0,
    'lastMod' => 0,
    'item' => 0,
    'conf' => 0,
    'type' => 0,
    'leafs' => 0,
    'key' => 0,
    'tree' => 0,
    'beTree' => 0,
    'lastNotes' => 0,
    'note' => 0,
    'lastComments' => 0,
    'cmt' => 0,
    'lastModBYUser' => 0,
    'connectedUser' => 0,
    'moduleList' => 0,
    'usrdata' => 0,
    'usr' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504dfcc7e47d47_74478276')) {function content_504dfcc7e47d47_74478276($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.truncate.php';
if (!is_callable('smarty_block_bedev')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.bedev.php';
?><script type="text/javascript">
<!--
var urlToSearch = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/home/search');?>
" 

function loadSearch() {
	$("#searchResult").load(urlToSearch, { searchstring: $("input[name='searchstring']").val() }, function() {
		//			
	});
}

$(document).ready(function() {
	
	$("#searchButton").click(function() {
		loadSearch();
	});
	
	$("input[name='searchstring']").keypress(function(event) {
		if (event.keyCode == 13 && $(this).val() != "") {
			event.preventDefault();
			loadSearch();
		}
	});
	
});

    $(document).ready(function(){	
		openAtStart("#search, #allrecent, #lastnotes, #lastcomments, #recent, #userpreferences");
    });
	
//-->
</script>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


	<div class="welcome" style="position:absolute; top:20px; left:180px">
		
		<h1><span a style="font-size:0.6em"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
welcome<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
			<a style="font-size:0.8em" href="javascript:void(0)" onClick="$('#userpreferences').prev('.tab').click();"><?php echo $_smarty_tpl->tpl_vars['BEAuthUser']->value['realname'];?>
</a>
		</h1>
	</div>
	
<div class="dashboard">
	
	<span class="hometree">
	<?php echo smarty_function_assign_associative(array('var'=>"options",'home'=>true),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('tree',$_smarty_tpl->tpl_vars['options']->value);?>

	</span>

	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<div id="search">
		<form action="">
			
			<input type="text" style="width:210px" name="searchstring" id="searchstring" value=""/>
			&nbsp;<input id="searchButton" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
go<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />
			<hr />
		</form>
		<div id="searchResult"></div>	
	</div>


	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all recent items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<ul id="allrecent" class="bordered smallist">
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lastMod']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<li>
			<span class="listrecent <?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
">&nbsp;&nbsp;</span>
			&nbsp;<a class="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['BEObject']['status'])===null||$tmp==='' ? '' : $tmp);?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
 | <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
modified on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['item']->value['BEObject']['modified'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
/view/<?php echo $_smarty_tpl->tpl_vars['item']->value['BEObject']['id'];?>
">
				<?php echo (($tmp = @smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['item']->value['BEObject']['title']),36,"~",true))===null||$tmp==='' ? '<i>[no title]</i>' : $tmp);?>
</a></li>
	<?php } ?>
	</ul>
	
</div>

<div class="dashboard first">

	<?php if (!empty($_smarty_tpl->tpl_vars['html']->value->params['named']['id'])){?>
	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 in nomesezione (<?php echo $_smarty_tpl->tpl_vars['html']->value->params['named']['id'];?>
)</h2></div>
	<ul id="allrecent" class="bordered smallist">
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lastMod']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<li>
			<span class="listrecent <?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
">&nbsp;&nbsp;</span>
			&nbsp;<a class="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['BEObject']['status'])===null||$tmp==='' ? '' : $tmp);?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
 | <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
modified on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['item']->value['BEObject']['modified'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
/view/<?php echo $_smarty_tpl->tpl_vars['item']->value['BEObject']['id'];?>
">
				<?php echo (($tmp = @smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['item']->value['BEObject']['title']),36,"~",true))===null||$tmp==='' ? '<i>[no title]</i>' : $tmp);?>
</a></li>
	<?php } ?>
	</ul>
	<?php }?>
	
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('bedev', array()); $_block_repeat=true; echo smarty_block_bedev(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
quick item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<div id="new" class="bordered smallist">
		<form>
			<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
			<input type="text" style="width:250px">
			<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Text<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
			<textarea style="width:250px"></textarea>
			<label ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Object type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>		
			<select style="width:250px">
			<?php $_smarty_tpl->tpl_vars['leafs'] = new Smarty_variable($_smarty_tpl->tpl_vars['conf']->value->objectTypes['leafs'], null, 0);?>
			<?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['conf']->value->objectTypes; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value){
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['type']->key;
?>	
				<?php if ((in_array($_smarty_tpl->tpl_vars['type']->value['id'],$_smarty_tpl->tpl_vars['leafs']->value['id'])&&is_numeric($_smarty_tpl->tpl_vars['key']->value))){?>
				<option <?php if (($_smarty_tpl->tpl_vars['type']->value['name']=='document')){?>selected="selected"<?php }?>>	
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['type']->value['model'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				</option>
				<?php }?>
			<?php } ?>
			</select>
			
			<br />
			<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Position<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
			<select style="width:250px">
				<?php echo $_smarty_tpl->tpl_vars['beTree']->value->option($_smarty_tpl->tpl_vars['tree']->value);?>

			</select>
			<hr />
			<input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
publish<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/> <input type="submit" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
save draft<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
		</form>
	</div>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_bedev(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last notes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<ul id="lastnotes" class="bordered">
		<?php  $_smarty_tpl->tpl_vars["note"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["note"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lastNotes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["note"]->key => $_smarty_tpl->tpl_vars["note"]->value){
$_smarty_tpl->tpl_vars["note"]->_loop = true;
?>
			<li><?php echo (($tmp = @$_smarty_tpl->tpl_vars['note']->value['realname'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['note']->value['userid'] : $tmp);?>
, 
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 "<i><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
view/<?php echo $_smarty_tpl->tpl_vars['note']->value['ReferenceObject']['id'];?>
"><?php echo (($tmp = @smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['note']->value['ReferenceObject']['title']),36,'~',true))===null||$tmp==='' ? '[no title]' : $tmp);?>
'</a></i>"</li>
		<?php }
if (!$_smarty_tpl->tpl_vars["note"]->_loop) {
?>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
no notes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
		<?php } ?>
	</ul>

	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last comments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<ul id="lastcomments" class="bordered">
		<?php  $_smarty_tpl->tpl_vars["cmt"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["cmt"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lastComments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["cmt"]->key => $_smarty_tpl->tpl_vars["cmt"]->value){
$_smarty_tpl->tpl_vars["cmt"]->_loop = true;
?>
			<li><?php echo (($tmp = @$_smarty_tpl->tpl_vars['cmt']->value['author'])===null||$tmp==='' ? '' : $tmp);?>
, 
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 "<i><a href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
view/<?php echo $_smarty_tpl->tpl_vars['cmt']->value['id'];?>
"><?php echo (($tmp = @smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['cmt']->value['ReferenceObject']['title']),36,'~',true))===null||$tmp==='' ? '[no title]' : $tmp);?>
'</a></i>"</li>
		<?php }
if (!$_smarty_tpl->tpl_vars["cmt"]->_loop) {
?>
			<li><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
no comments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
		<?php } ?>
	</ul>


<script type="text/javascript">
<!--
$(document).ready(function(){
	
	var showTagsFirst = false;
	var showTags = false;
	$("#callTags").bind("click", function() {
		if (!showTagsFirst) {
			$("#loadingTags").show();
			$("#listExistingTags").load("<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/tags/listAllTags/1');?>
", function() {
				$("#loadingTags").slideUp("fast");
				$("#listExistingTags").slideDown("fast");
				showTagsFirst = true;
				showTags = true;
			});
		} else {
			if (showTags) {
				$("#listExistingTags").slideUp("fast");
			} else {
				$("#listExistingTags").slideDown("fast");
			}
			showTags = !showTags;
		}
	});	
});
//-->
</script>

	
	<div class="tab"><h2 id="callTags"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
tags<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<div id="tags">
		<div id="loadingTags" class="generalLoading" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Loading data<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">&nbsp;</div>	
		<div id="listExistingTags" class="tag graced" style="display: none; text-align:justify;"></div>
	</div>
	
</div>

<div class="dashboard second">

	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
your 5 recent items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<ul id="recent" class="bordered smallist">
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lastModBYUser']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<li><span class="listrecent <?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
">&nbsp;</span>
		<a class="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['BEObject']['status'])===null||$tmp==='' ? '' : $tmp);?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
 | <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
modified on<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['item']->value['BEObject']['modified'],$_smarty_tpl->tpl_vars['conf']->value->dateTimePattern);?>
" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['item']->value['ObjectType']['module_name'];?>
/view/<?php echo $_smarty_tpl->tpl_vars['item']->value['BEObject']['id'];?>
">
			<?php echo (($tmp = @smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['item']->value['BEObject']['title']),36,"~",true))===null||$tmp==='' ? '<i>[no title]</i>' : $tmp);?>
</a></li>
	<?php }
if (!$_smarty_tpl->tpl_vars['item']->_loop) {
?>
		<li><i><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
you have no recent items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</i></li>
	<?php } ?>
	</ul>
			
	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
your profile and preferences<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	
	<div id="userpreferences">	
		<?php echo $_smarty_tpl->getSubTemplate ("inc/userpreferences.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div>

	<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
connected user<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>
	<ul id="connected" class="bordered">
	<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['name'] = "i";
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['connectedUser']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		<?php  $_smarty_tpl->tpl_vars['usrdata'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['usrdata']->_loop = false;
 $_smarty_tpl->tpl_vars['usr'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['connectedUser']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['usrdata']->key => $_smarty_tpl->tpl_vars['usrdata']->value){
$_smarty_tpl->tpl_vars['usrdata']->_loop = true;
 $_smarty_tpl->tpl_vars['usr']->value = $_smarty_tpl->tpl_vars['usrdata']->key;
?>
		<li>
		<?php if (isset($_smarty_tpl->tpl_vars['moduleList']->value['admin'])){?>
		<a title="<?php echo $_smarty_tpl->tpl_vars['usrdata']->value['realname'];?>
 | <?php echo $_smarty_tpl->tpl_vars['usrdata']->value['userAgent'];?>
 | <?php echo $_smarty_tpl->tpl_vars['usrdata']->value['ipNumber'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
admin/viewUser/<?php echo $_smarty_tpl->tpl_vars['usrdata']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['usr']->value;?>
</a>
		<?php }else{ ?>		
		<a title="<?php echo $_smarty_tpl->tpl_vars['usrdata']->value['realname'];?>
" href="#"><?php echo $_smarty_tpl->tpl_vars['usr']->value;?>
</a>
		<?php }?>
		</li>
		<?php } ?>
	<?php endfor; endif; ?>
	</ul>
	
</div>	
		
		
<p style="clear:both; margin-bottom:20px;" />

<?php }} ?>