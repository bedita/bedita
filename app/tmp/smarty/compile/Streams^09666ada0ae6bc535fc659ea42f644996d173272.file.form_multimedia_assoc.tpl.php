<?php /* Smarty version Smarty-3.1.11, created on 2012-09-17 12:08:46
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_multimedia_assoc.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1907879624504e107c34da97-00486640%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '09666ada0ae6bc535fc659ea42f644996d173272' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_multimedia_assoc.tpl',
      1 => 1347876227,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1907879624504e107c34da97-00486640',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504e107c7473c5_91757074',
  'variables' => 
  array (
    'relation' => 0,
    'html' => 0,
    'object_id' => 0,
    'toolbar' => 0,
    'streamSearched' => 0,
    'items' => 0,
    'thumbWidth' => 0,
    'thumbHeight' => 0,
    'mobj' => 0,
    'params' => 0,
    'attributes' => 0,
    'beEmbedMedia' => 0,
    'itemType' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504e107c7473c5_91757074')) {function content_504e107c7473c5_91757074($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_function_assign_associative')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_associative.php';
if (!is_callable('smarty_modifier_filesize')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/modifier.filesize.php';
?><!-- start upload block-->
<script type="text/javascript">
<!--

function addItemsToParent() { 
	var itemsIds = new Array() ;
	$(":checkbox").each(function() { 
		try { 
			if (this.checked && this.name == 'chk_bedita_item') { 
				itemsIds[itemsIds.length] = $(this).attr("value");
			} 
		} catch(e) { 
		} 
	} ) ;
	for (i=0;i<itemsIds.length;i++) { 
		$("#tr_"+itemsIds[i]).remove();
	} 
	commitUploadItem(itemsIds, '<?php echo $_smarty_tpl->tpl_vars['relation']->value;?>
');
}

function loadMultimediaAssoc(urlSearch, showAll) { 
	$("#loading").show();
	$("#ajaxSubcontainer").load(urlSearch, function() { 
		$("#loading").hide();
		if (showAll) { 
			$("#searchMultimediaShowAll").show();
		} else { 
			$("#searchMultimediaShowAll").hide();
		} 
	} );
} 

$(document).ready(function(){ 

	$(".selItems").bind("click", function(){ 
		var check = $("input:checkbox",$(this).parent().parent()).get(0).checked ;
		$("input:checkbox",$(this).parent().parent()).get(0).checked = !check ;
	} );
	
	$("#searchMultimedia").bind("click", function() { 
		var textToSearch = escape($("#searchMultimediaText").val());
		loadMultimediaAssoc(
			"<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/streams/showStreams');?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_id']->value)===null||$tmp==='' ? '0' : $tmp);?>
/" + textToSearch,
			true
		);
	} );
	$("#searchMultimediaText").focus(function() { 
		$(this).val("");
	} );
	$("#searchMultimediaShowAll").click(function() { 
		loadMultimediaAssoc(
			"<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/showStreams");?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_id']->value)===null||$tmp==='' ? '0' : $tmp);?>
",
			false
		);
	} );
	
	$("#addItems").click(function(){ 
		addItemsToParent();
	} );
	
	<?php if ((($tmp = @$_smarty_tpl->tpl_vars['toolbar']->value)===null||$tmp==='' ? '' : $tmp)){?>
	
		$("#streamPagList").tablesorter({ 
			headers: {  
				0: { sorter: false },
				2: { sorter: false } 
			} 
		} );
		 
		$("#streamNextPage").click(function() { 
			urlReq = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/showStreams");?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_id']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['streamSearched']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['next'];?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['dim'];?>
";
			loadMultimediaAssoc(urlReq,	false);
		} );
		$("#streamPrevPage").click(function() { 
			urlReq = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/showStreams");?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_id']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['streamSearched']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['prev'];?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['dim'];?>
";
			loadMultimediaAssoc(urlReq,	false);
		} );
		$("#streamFirstPage").click(function() { 
			urlReq = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/showStreams");?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_id']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['streamSearched']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['first'];?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['dim'];?>
";
			loadMultimediaAssoc(urlReq,	false);
		} );
		$("#streamLastPage").click(function() { 
			urlReq = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/showStreams");?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_id']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['streamSearched']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['last'];?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['dim'];?>
";
			loadMultimediaAssoc(urlReq,	false);
		} );
		$("#streamPagDim").change(function() { 
			urlReq = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url("/streams/showStreams");?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object_id']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['streamSearched']->value)===null||$tmp==='' ? '0' : $tmp);?>
/<?php echo $_smarty_tpl->tpl_vars['toolbar']->value['first'];?>
/" + $(this).val();
			loadMultimediaAssoc(urlReq,	false);
		} );
	
	<?php }?>
	
} );
//-->

</script>

<div id="formMultimediaAssoc" class="ignore">
	<fieldset>
		<?php if (!empty($_smarty_tpl->tpl_vars['items']->value)){?>
			<?php if ((($tmp = @$_smarty_tpl->tpl_vars['toolbar']->value)===null||$tmp==='' ? '' : $tmp)){?>
				<p>		
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: <?php echo $_smarty_tpl->tpl_vars['toolbar']->value['size'];?>
 | <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo $_smarty_tpl->tpl_vars['toolbar']->value['page'];?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
of<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php echo $_smarty_tpl->tpl_vars['toolbar']->value['pages'];?>
 

						&nbsp; | &nbsp;
						<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['page']>1){?>
							<span><a href="javascript: void(0);" id="streamFirstPage" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
first page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
first<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></span>
						<?php }else{ ?>
							<span><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
first<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
						<?php }?>
						
						&nbsp; | &nbsp;
						
						<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['prev']>0){?>
							<span><a href="javascript: void(0);" id="streamPrevPage" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
previous page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
prev<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></span>
						<?php }else{ ?>
							<span><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
prev<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
						<?php }?>
			
						&nbsp; | &nbsp;			
					
						<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['next']>0){?>
							<span><a href="javascript: void(0);" id="streamNextPage" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
next page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
next<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></span>
						<?php }else{ ?>
							<span><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
next<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
						<?php }?>
						
						&nbsp; | &nbsp;
						
						<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['last']>0){?>
							<span><a href="javascript: void(0);" id="streamLastPage" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></span>
						<?php }else{ ?>
							<span><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
last<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						<?php }?>
										
						&nbsp; | &nbsp;
					
						<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Dimensions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: 
						<select name="streamPagDim" id="streamPagDim">
							<option value="1"<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['dim']==1){?> selected="selected"<?php }?>>1</option>
							<option value="5"<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['dim']==5){?> selected="selected"<?php }?>>5</option>
							<option value="10"<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['dim']==10){?> selected="selected"<?php }?>>10</option>
							<option value="20"<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['dim']==20){?> selected="selected"<?php }?>>20</option>
							<option value="50"<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['dim']==50){?> selected="selected"<?php }?>>50</option>
							<option value="100"<?php if ($_smarty_tpl->tpl_vars['toolbar']->value['dim']==100){?> selected="selected"<?php }?>>100</option>
						</select>
				</p>
			<hr />
			<?php }?>
		<?php }?>
		<div>
			<input type="text" id="searchMultimediaText" name="searchMultimediaItems" value="<?php if (!empty($_smarty_tpl->tpl_vars['streamSearched']->value)){?><?php echo $_smarty_tpl->tpl_vars['streamSearched']->value;?>
<?php }else{ ?>search<?php }?>"/>
			<input id="searchMultimedia" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
			<input type="button" id="searchMultimediaShowAll" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Show all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" style="display: none;" />
		</div>

		<hr />
		<table class="indexlist" id="streamPagList" style="clear: left;">
			
			<tbody>
			
			<?php $_smarty_tpl->tpl_vars["thumbWidth"] = new Smarty_variable(45, null, 0);?>
			<?php $_smarty_tpl->tpl_vars["thumbHeight"] = new Smarty_variable(45, null, 0);?>
			<?php echo smarty_function_assign_associative(array('var'=>"params",'presentation'=>"thumb",'width'=>$_smarty_tpl->tpl_vars['thumbWidth']->value,'height'=>$_smarty_tpl->tpl_vars['thumbHeight']->value),$_smarty_tpl);?>

			<?php echo smarty_function_assign_associative(array('var'=>"attributes",'style'=>"width:45px;"),$_smarty_tpl);?>

			
			<?php  $_smarty_tpl->tpl_vars['mobj'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mobj']->_loop = false;
 $_smarty_tpl->tpl_vars['mkey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['mobj']->key => $_smarty_tpl->tpl_vars['mobj']->value){
$_smarty_tpl->tpl_vars['mobj']->_loop = true;
 $_smarty_tpl->tpl_vars['mkey']->value = $_smarty_tpl->tpl_vars['mobj']->key;
?>	
			<tr class="rowList" id="tr_<?php echo $_smarty_tpl->tpl_vars['mobj']->value['id'];?>
">
				
				<td style="width:12px;"><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['mobj']->value['id'];?>
" name="chk_bedita_item" class="objectCheck"/></td>
				
				<td style="width:<?php echo $_smarty_tpl->tpl_vars['thumbWidth']->value;?>
px;">
				<a title="show details" href="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/multimedia/view/');?>
<?php echo $_smarty_tpl->tpl_vars['mobj']->value['id'];?>
" target="_blank">
					<?php echo $_smarty_tpl->tpl_vars['beEmbedMedia']->value->object($_smarty_tpl->tpl_vars['mobj']->value,$_smarty_tpl->tpl_vars['params']->value,$_smarty_tpl->tpl_vars['attributes']->value);?>

				</a>
				</td>
				
				
				<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['mobj']->value['title'])===null||$tmp==='' ? '' : $tmp);?>
</td>
				
				<td><?php echo smarty_modifier_filesize((($tmp = @$_smarty_tpl->tpl_vars['mobj']->value['file_size'])===null||$tmp==='' ? '' : $tmp));?>
</td>
				
				<td><?php echo $_smarty_tpl->tpl_vars['mobj']->value['lang'];?>
</td>
				
			</tr>
			<?php }
if (!$_smarty_tpl->tpl_vars['mobj']->_loop) {
?>
				<tr>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No <?php echo $_smarty_tpl->tpl_vars['itemType']->value;?>
 item found<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			<?php } ?>
			</tbody>
			</table>
		<?php if (!empty($_smarty_tpl->tpl_vars['items']->value)){?>
			<hr />
			&nbsp;<input type="checkbox" class="selectAll" id="selectAll" />&nbsp;
			<label for="selectAll"> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
(Un)Select All<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</label>
			&nbsp;&nbsp;&nbsp;
			<input type="button" id="addItems" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Add selected items<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
		<?php }?>
	</fieldset>
	
</div>
<!-- end upload block --><?php }} ?>