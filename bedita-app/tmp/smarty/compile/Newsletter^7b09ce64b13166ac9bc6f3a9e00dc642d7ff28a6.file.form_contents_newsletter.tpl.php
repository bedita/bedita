<?php /* Smarty version Smarty-3.1.11, created on 2012-09-14 17:13:01
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form_contents_newsletter.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21117456015053497de7b0b5-82204597%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b09ce64b13166ac9bc6f3a9e00dc642d7ff28a6' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/newsletter/inc/form_contents_newsletter.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21117456015053497de7b0b5-82204597',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'html' => 0,
    'relObjects' => 0,
    'conf' => 0,
    'cssUrl' => 0,
    'view' => 0,
    'object' => 0,
    'default' => 0,
    'templateByArea' => 0,
    'pub' => 0,
    'temp' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5053497e1ba0f6_99719151',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5053497e1ba0f6_99719151')) {function content_5053497e1ba0f6_99719151($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
if (!is_callable('smarty_function_assign_concat')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/function.assign_concat.php';
?><script language="javascript" type="text/javascript">
	var urlAddObjToAssBase = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/loadContentToNewsletter');?>
";
	var urlAddObjToAss = urlAddObjToAssBase;
	<?php if (!empty($_smarty_tpl->tpl_vars['relObjects']->value['template'])){?>
		urlAddObjToAss += "/<?php echo $_smarty_tpl->tpl_vars['relObjects']->value['template'][0]['id'];?>
";
	<?php }?>
</script>

<?php if (((($tmp = @$_smarty_tpl->tpl_vars['conf']->value->mce)===null||$tmp==='' ? true : $tmp))){?>
	<?php echo $_smarty_tpl->tpl_vars['html']->value->script("tiny_mce/tiny_mce");?>

	
	<script language="javascript" type="text/javascript">

		function addObjToAssoc(url, postdata) {
			$("#loaderContent").show();
		    $.post(url, postdata, function(html){
				tinyMCE.activeEditor.dom.add(tinyMCE.activeEditor.getBody(), "span", null, html);
				// get txt
				postdata.txt = 1;
				$.post(url, postdata, function(txt){
					prevText = $("#txtarea").val(); 
					$("#txtarea").val(prevText + txt).focus(); // focus is used to update textarea dimension with autogrow
					$("#loaderContent").hide();
				}, "text");
			});
		}

		function initializeTinyMCE(cssPath) {
			tinyMCE.init({
				// General options
				mode : "textareas",
				theme : "advanced",
				editor_selector : "mce",
				plugins : "safari,pagebreak,paste,fullscreen",
			
				// Theme options
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough, | ,formatselect,bullist,numlist, hr, | ,link,unlink,pastetext,pasteword, | ,removeformat,charmap,code,fullscreen",
				theme_advanced_buttons2 : "sub,sup,fontsizeselect,forecolor,styleselect,justifyleft,justifycenter,justifyright,justifyfull,image",
				theme_advanced_buttons3 : "",
				//http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/template 
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				//theme_advanced_resizing : true,
				theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address",
				//width : "450",
				//http://wiki.moxiecode.com/index.php/TinyMCE:Control_reference
				
				// Example content CSS (should be your site CSS)
				content_css : cssPath,
			    relative_urls : false,
				convert_urls : false,
			    remove_script_host : false,
				document_base_url : "/"
			});
			
		}

		initializeTinyMCE("<?php echo (($tmp = @$_smarty_tpl->tpl_vars['cssUrl']->value)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['html']->value->url('/css/newsletter.css') : $tmp);?>
");

		$(document).ready(function() {
			$("#changeTemplate").change(function() {
				
				var template_id = $(this).val();
				// update url ajax return from modal window 
				urlAddObjToAss = urlAddObjToAssBase + "/" + template_id;
				
				if (template_id != "") {
					$("#msgDetailsLoader").show();
					
					$("#msgDetails").load("<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/showTemplateDetailsAjax/');?>
" + template_id, function() {
						$("#msgDetailsLoader").hide();	
					})
				}
				
				// reinitilize tinyMCE with templatecss
				mce = tinyMCE.get("htmltextarea");
				mce.remove();
				cssBaseUrl = $(this).find("option:selected").attr("rel");
				if (cssBaseUrl === undefined) {
					cssPath = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/css/newsletter.css');?>
";
				} else {
					cssPath =  cssBaseUrl + "/css/<?php echo $_smarty_tpl->tpl_vars['conf']->value->newsletterCss;?>
";
				}

				initializeTinyMCE(cssPath);
				
			});
		});

	</script>

<?php }else{ ?>

	<?php echo $_smarty_tpl->tpl_vars['view']->value->element('texteditor');?>


	<script language="javascript" type="text/javascript">

	function addObjToAssoc(url, postdata) {
			$("#loaderContent").show();
		    $.post(url, postdata, function(html){
				var data = $('#htmltextarea').val() + html;
				$( '#htmltextarea' ).val(data);
				// get txt
				postdata.txt = 1;
				$.post(url, postdata, function(txt){
					prevText = $("#txtarea").val(); 
					$("#txtarea").val(prevText + txt).focus(); // focus is used to update textarea dimension with autogrow
					$("#loaderContent").hide();
				}, "text");
			});
		}

	function changeCKeditorCss(csshref) {
		var editor = $('#htmltextarea').ckeditorGet();
		var linkElement = $(editor.document.$).find('link');
		linkElement.attr('href', csshref);
	}

	$(document).ready(function() {
		$("#changeTemplate").change(function() {
			var template_id = $(this).val();
			// update url ajax return from modal window
			urlAddObjToAss = urlAddObjToAssBase + "/" + template_id;
			if (template_id != "") {
				$("#msgDetailsLoader").show();
				$("#msgDetails").load("<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/newsletter/showTemplateDetailsAjax/');?>
" + template_id, function() {
					$("#msgDetailsLoader").hide();
				})
			}
			var	cssBaseUrl = $(this).find("option:selected").attr("rel");
			var cssPath;
			if (cssBaseUrl === undefined || cssBaseUrl == '') {
				cssPath = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/css/newsletter.css');?>
";
			} else {
				cssPath =  cssBaseUrl + "/css/<?php echo $_smarty_tpl->tpl_vars['conf']->value->newsletterCss;?>
";
			}
			changeCKeditorCss(cssPath);
		});
	});

	</script>

<?php }?>


<div class="tab"><h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Compile<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2></div>

<fieldset id="contents">
	
	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: </label>
	<?php echo smarty_function_assign_concat(array('var'=>"default",1=>"Newsletter | ",2=>smarty_modifier_date_format(time(),"%B %Y")),$_smarty_tpl);?>

	<input type="text" id="title" name="data[title]" class="required"
	value="<?php echo preg_replace("%(?<!\\\\)'%", "\'",htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['object']->value['title'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['default']->value : $tmp), ENT_QUOTES, 'UTF-8', true));?>
" id="titleBEObject"/>



	<hr />

	<input class="modalbutton" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Get contents<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" rel="<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/showObjects/0/0/0/leafs');?>
" style="width:200px" />

	&nbsp;&nbsp;
	
	
	<label><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
use template<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</label>
	<input type="hidden" name="data[RelatedObject][template][0][switch]" value="template" />
	<select name="data[RelatedObject][template][1][id]" id="changeTemplate">
		<option value="">--</option>
		<?php  $_smarty_tpl->tpl_vars["pub"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["pub"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['templateByArea']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["pub"]->key => $_smarty_tpl->tpl_vars["pub"]->value){
$_smarty_tpl->tpl_vars["pub"]->_loop = true;
?>
			<?php if (!empty($_smarty_tpl->tpl_vars['pub']->value['MailTemplate'])){?>
				<option value=""><?php echo mb_strtoupper($_smarty_tpl->tpl_vars['pub']->value['title'], 'UTF-8');?>
</option>
			<?php }?>
			<?php  $_smarty_tpl->tpl_vars["temp"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["temp"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pub']->value['MailTemplate']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["temp"]->key => $_smarty_tpl->tpl_vars["temp"]->value){
$_smarty_tpl->tpl_vars["temp"]->_loop = true;
?>
				<option rel="<?php echo $_smarty_tpl->tpl_vars['pub']->value['public_url'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['temp']->value['id'];?>
"<?php if (!empty($_smarty_tpl->tpl_vars['relObjects']->value['template'])&&$_smarty_tpl->tpl_vars['relObjects']->value['template'][0]['id']==$_smarty_tpl->tpl_vars['temp']->value['id']){?> selected<?php }?>>&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['temp']->value['title'];?>
</option>
			<?php } ?>
		<?php } ?>
	</select>

	<hr />
	
	<div id="loaderContent" class="loader"><span></span></div>
	
	<table class="htab">
		<td rel="html"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
HTML version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
		<td rel="txt"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
PLAIN TEXT version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
	</table>
	
	<div class="htabcontainer" id="templatebody">
		
		<div class="htabcontent" id="html">
			<textarea id="htmltextarea" name="data[body]" style="height:350px;  width:610px" class="mce"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['body'])===null||$tmp==='' ? null : $tmp);?>
</textarea>
		</div>
		
		<div class="htabcontent" id="txt">
			<textarea id="txtarea" name="data[abstract]" style="height:350px; border:1px solid silver; width:610px"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['abstract'])===null||$tmp==='' ? null : $tmp);?>
</textarea>
		</div>
		
	</div>

	
	<br />
	
	<div id="msgDetailsLoader" class="loader"></div>
	<div id="msgDetails"><?php echo $_smarty_tpl->getSubTemplate ("inc/form_message_details.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div>
	
	
</fieldset>
<?php }} ?>