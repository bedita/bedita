<?php /* Smarty version Smarty-3.1.12, created on 2012-09-26 16:37:37
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/areas/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:118318661250535c4fba8b34-60525149%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '24caf6332dc797086cf29bfd51f6c153816bfa7c' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/areas/index.tpl',
      1 => 1347894656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118318661250535c4fba8b34-60525149',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50535c4fe1f867_29800610',
  'variables' => 
  array (
    'cssOptions' => 0,
    'html' => 0,
    'currLang' => 0,
    'view' => 0,
    'object' => 0,
    'objectType' => 0,
    'actionForm' => 0,
    'formDetails' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50535c4fe1f867_29800610')) {function content_50535c4fe1f867_29800610($_smarty_tpl) {?><?php if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_modifier_capitalize')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.capitalize.php';
if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
?><?php echo smarty_function_assign_associative(array('var'=>"cssOptions",'inline'=>false),$_smarty_tpl);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->css("ui.datepicker",null,$_smarty_tpl->tpl_vars['cssOptions']->value);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.form",false);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.autogrow",false);?>


<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.sortable",true);?>

<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/jquery.selectboxes.pack",false);?>


<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/jquery.ui.datepicker",false);?>

<?php if ($_smarty_tpl->tpl_vars['currLang']->value!="eng"){?>
<?php echo $_smarty_tpl->tpl_vars['html']->value->script("jquery/ui/i18n/ui.datepicker-".((string)$_smarty_tpl->tpl_vars['currLang']->value).".js",false);?>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['view']->value->element('modulesmenu');?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menuleft.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("inc/menucommands.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('fixed'=>true), 0);?>


<div class="head">

	<h1><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? '' : $tmp);?>
</h1>

</div> 

<?php if (!empty($_smarty_tpl->tpl_vars['object']->value)){?>

	<?php echo smarty_function_assign_concat(array('var'=>"actionForm",1=>"save",2=>(($tmp = @smarty_modifier_capitalize($_smarty_tpl->tpl_vars['objectType']->value))===null||$tmp==='' ? "Area" : $tmp)),$_smarty_tpl);?>

	
	<form action="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/areas/');?>
<?php echo $_smarty_tpl->tpl_vars['actionForm']->value;?>
" method="post" name="updateForm" id="updateForm" class="cmxform">

	<div id="loading" style="position:absolute; left:320px; top:110px; ">&nbsp;</div>

	<div class="main full">

	<!--
		<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Details<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 of &nbsp; <span class="graced" style="font-size:1.5em" id="sectionTitle"></span></h2></div>
	-->

		<fieldset style="padding:0px" id="properties">		

			

			<!-- questo è brutto ma cross-browser -->
			<table class="htab">
				<td rel="areacontentC"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all contents<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td rel="areasectionsC"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
sections only<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td rel="areapropertiesC"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
properties<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
			</table>	
			<!-- -->	

			<div class="htabcontainer" id="sectiondetails">

				<div id="areacontentC" class="htabcontent">

					<?php echo $_smarty_tpl->getSubTemplate ('./inc/list_content.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


				</div>

				<div id="areasectionsC" class="htabcontent">

					<?php echo $_smarty_tpl->getSubTemplate ('./inc/list_sections.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


				</div>


				<div id="areapropertiesC" class="htabcontent">
					
					<?php echo smarty_function_assign_concat(array('var'=>"formDetails",1=>"./inc/form_",2=>$_smarty_tpl->tpl_vars['objectType']->value,3=>".tpl"),$_smarty_tpl);?>

					<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['formDetails']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


				</div>

			</div>

		</fieldset>	

	</div>

	</form>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('menuright');?>


<?php }?><?php }} ?>