<script language="javascript" type="text/javascript">
	var urlAddObjToAssBase = "{$html->url('/newsletter/loadContentToNewsletter')}";
	var urlAddObjToAss = urlAddObjToAssBase;
	{if !empty($relObjects.template)}
		urlAddObjToAss += "/{$relObjects.template.0.id}";
	{/if}
	var cssTemplate = false;
	var cssDefault = false;
	var cssInit = false;

	{if !empty($conf->newsletterCss)}
		cssDefault = cssInit = "{$html->url('/css')}/{$conf->newsletterCss}";
	{/if}

	{if !empty($cssUrl)}
		cssTemplate = cssInit = "{$cssUrl}";
	{/if}
</script>

{if !empty($conf->richtexteditor.name) && $conf->richtexteditor.name == "tinymce"}
	{$html->script("libs/richtexteditors/tiny_mce/tiny_mce")}
	
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
				editor_selector : "richtextNewsletterMessage",
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

		initializeTinyMCE("{$cssUrl|default:$html->url('/css/newsletter.css')}");

		$(document).ready(function() {
			$("#changeTemplate").change(function() {
				
				var template_id = $(this).val();
				// update url ajax return from modal window 
				urlAddObjToAss = urlAddObjToAssBase + "/" + template_id;
				
				if (template_id != "") {
					$("#msgDetailsLoader").show();
					
					$("#msgDetails").load("{$html->url('/newsletter/showTemplateDetailsAjax/')}" + template_id, function() {
						$("#msgDetailsLoader").hide();	
					})
				}
				
				// reinitilize tinyMCE with templatecss
				mce = tinyMCE.get("htmltextarea");
				mce.remove();
				cssBaseUrl = $(this).find("option:selected").attr("rel");
				if (cssBaseUrl === undefined) {
					cssPath = "{$html->url('/css/newsletter.css')}";
				} else {
					cssPath =  cssBaseUrl + "/css/{$conf->newsletterCss}";
				}

				initializeTinyMCE(cssPath);
				
			});
		});

	</script>

{else}

	{$view->element('texteditor')}

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
		$(document).on('instanceReady.ckeditor', '#htmltextarea', function(event,editor) {
			if (cssInit) {
				var linkElement = $(editor.document.$).find('link');
				linkElement.attr('href', cssInit);
			}
		});
		
		$("#changeTemplate").change(function() {
			var template_id = $(this).val();
			// update url ajax return from modal window
			urlAddObjToAss = urlAddObjToAssBase + "/" + template_id;
			if (template_id != "") {
				$("#msgDetailsLoader").show();
				$("#msgDetails").load("{$html->url('/newsletter/showTemplateDetailsAjax/')}" + template_id, function() {
					$("#msgDetailsLoader").hide();
				})
			}
			var	cssBaseUrl = $(this).find("option:selected").attr("rel");
			var cssPath;
			if (cssBaseUrl === undefined || cssBaseUrl == '') {
				cssPath = cssDefault;
			} else {
				if (cssDefault) {
					cssPath =  cssBaseUrl + "/css/{$conf->newsletterCss}";
				}
			}
			if (cssPath) {
				changeCKeditorCss(cssPath);
			}
		});

		// if fieldset is visible, tab should have open class (fck-maximize-fix) 
		// REMOVE OR REVIEW WHEN UPGRADING CKEDITOR
		$('.tab.fck-maximize-fix:not(.open)+fieldset:visible').css('display', 'none');
		$(document).off("keydown"); //disable Esc keybinding
	});
	</script>
{/if}


<div class="tab fck-maximize-fix"><h2>{t}Compile{/t}</h2></div>

<fieldset id="contents">
	
	<label>{t}Title{/t}: </label>
	{assign_concat var="default" 1="Newsletter | " 2=$smarty.now|date_format:"%B %Y"}
	<input type="text" id="title" name="data[title]" class="required"
	value="{$object.title|default:$default|escape:'html'|escape:'quotes'}" id="titleBEObject"/>

	<hr />
	{bedev}
	<input class="modalbutton" type="button" value="{t}Get contents{/t}" rel="{$html->url('/pages/showObjects/0/0/0/leafs')}" style="width:200px" />
	&nbsp;&nbsp;
	{/bedev}
	
	
	<label>{t}use template{/t}:</label>
	<input type="hidden" name="data[RelatedObject][template][0][switch]" value="template" />
	<select name="data[RelatedObject][template][1][id]" id="changeTemplate" style="width: 20em;">
		<option value="">--</option>
		{foreach $templateNotOnTree as $t}
			<option rel="" value="{$t.id}"{if !empty($relObjects.template) && $relObjects.template.0.id == $t.id} selected{/if}>{$t.title|escape}</option>
		{/foreach}
		{foreach from=$templateOnTree item="pub"}
			{if !empty($pub.MailTemplate)}
				<option value="">{$pub.title|escape|upper}</option>
			{/if}
			{foreach from=$pub.MailTemplate item="temp"}
				<option rel="{$pub.public_url}" value="{$temp.id}"{if !empty($relObjects.template) && $relObjects.template.0.id == $temp.id} selected{/if}>&nbsp;&nbsp;&nbsp;{$temp.title|escape}</option>
			{/foreach}
		{/foreach}
	</select>

	<hr />
	
	<div id="loaderContent" class="loader"><span></span></div>
	
	<table class="htab">
		<td rel="html">{t}HTML version{/t}</td>
		<td rel="txt">{t}PLAIN TEXT version{/t}</td>
	</table>
	
	<div class="htabcontainer" id="templatebody">
		
		<div class="htabcontent" id="html">
			<textarea id="htmltextarea" class="richtextNewsletterMessage" name="data[body]" style="height:350px;  width:610px">{$object.body|default:null}</textarea>
		</div>
		
		<div class="htabcontent" id="txt">
			<textarea id="txtarea" name="data[abstract]" style="height:350px; border:1px solid silver; width:610px">{$object.abstract|escape|default:null}</textarea>
		</div>
		
	</div>

	
	<br />
	
	<div id="msgDetailsLoader" class="loader"></div>
	<div id="msgDetails">{include file="inc/form_message_details.tpl"}</div>
	
	
</fieldset>
