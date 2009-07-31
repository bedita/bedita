{if ($conf->mce|default:true)}
	{$javascript->link("tiny_mce/tiny_mce")}
	
<script language="javascript" type="text/javascript">

var urlAddObjToAssBase = "{$html->url('/newsletter/loadContentToNewsletter')}";
var urlAddObjToAss = urlAddObjToAssBase;
{if !empty($relObjects.template)}
	urlAddObjToAss += "/{$relObjects.template.0.id}";
{/if}

{literal}

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
		theme_advanced_buttons2 : "sub,sup,fontsizeselect,forecolor,styleselect,justifyleft,justifycenter,justifyright,justifyfull",
		theme_advanced_buttons3 : "",
		//http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/template 
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		//theme_advanced_resizing : true,
		theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address",
		width : "450",
		//http://wiki.moxiecode.com/index.php/TinyMCE:Control_reference
		
		// Example content CSS (should be your site CSS)
		content_css : cssPath,
	    relative_urls : false,
		convert_urls : false,
	    remove_script_host : false,
		document_base_url : "/",
/*
		template_cdate_classes : "cdate creationdate",
		template_mdate_classes : "mdate modifieddate",
		template_selected_content_classes : "selcontent",
		template_cdate_format : "%m/%d/%Y : %H:%M:%S",
		template_mdate_format : "%m/%d/%Y : %H:%M:%S",
		template_replace_values : {
			username : "Jack Black",
			staffid : "991234"
		},
		template_templates : [
			{
				title : "Test1 Details",
				src : "/test1.html",
				description : "Adds Editor Name and Staff ID"
			},
			{
				title : "TESt2 Timestamp",
				src : "/test2.html",
				description : "Adds an editing timestamp."
			}
		]
*/

	/*
	<a href="#" onclick="tinyMCE.execCommand('Bold');return false;">[Bold]</a>
	<a href="#" onclick="tinyMCE.execCommand('Italic');return false;">[Italic]</a>
	<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,'<b>Hello world!!</b>');return false;">[Insert some HTML]</a>
	<a href="#" onclick="tinyMCE.execCommand('mceReplaceContent',false,'<b>{$selection}</b>');return false;">[Replace selection]</a>
	 */
	
	});
	
}

initializeTinyMCE("{/literal}{$cssUrl|default:$html->url('/css/newsletter.css')}{literal}");

$(document).ready(function() {
	$("#changeTemplate").change(function() {
		
		var template_id = $(this).val();
		// update url ajax return from modal window 
		urlAddObjToAss = urlAddObjToAssBase + "/" + template_id;
		
		if (template_id != "") {
			$("#msgDetailsLoader").show();
			
			$("#msgDetails").load("{/literal}{$html->url('/newsletter/showTemplateDetailsAjax/')}{literal}" + template_id, function() {
				$("#msgDetailsLoader").hide();	
			})
		}
		
		// reinitilize tinyMCE with templatecss
		mce = tinyMCE.get("htmltextarea");
		mce.remove();
		cssBaseUrl = $(this).find("option:selected").attr("rel");
		if (cssBaseUrl === undefined)
			cssPath = "{/literal}{$html->url('/css/newsletter.css')}{literal}";
		else
			cssPath =  cssBaseUrl + "/css/{/literal}{$conf->newsletterCss}{literal}";

		initializeTinyMCE(cssPath);
		
	});
});


	</script>
{/literal}
{/if}


<div class="tab"><h2>{t}Compile{/t}</h2></div>

<fieldset id="contents">


	<label>{t}Title{/t}: </label>
	{assign_concat var="default" 0="Newsletter | " 1=$smarty.now|date_format:"%B %Y"}
	<input type="text" id="title" name="data[title]" 
	value="{$object.title|default:$default|escape:'html'|escape:'quotes'}" id="titleBEObject"/>


	<hr />
	
	<label>{t}Subject{/t}: </label>
	<input type="text" id="subject" name="data[subject]" 
	value="{$object.subject|default:null}" id="subjectBEObject"/>

	<hr />

	<input class="modalbutton" type="button" value="{t}Get contents{/t}" rel="{$html->url('/pages/showObjects/0/0/0/leafs')}" style="width:200px" />

	&nbsp;&nbsp;
	
	<label>{t}use template{/t}:</label>
	<input type="hidden" name="data[RelatedObject][template][0][switch]" value="template" />
	<select name="data[RelatedObject][template][1][id]" id="changeTemplate">
		<option value="">--</option>
		{foreach from=$templateByArea item="pub"}
			{if !empty($pub.MailTemplate)}
				<option value="">{$pub.title|upper}</option>
			{/if}
			{foreach from=$pub.MailTemplate item="temp"}
				<option rel="{$pub.public_url}" value="{$temp.id}"{if !empty($relObjects.template) && $relObjects.template.0.id == $temp.id} selected{/if}>&nbsp;&nbsp;&nbsp;{$temp.title}</option>
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
			<textarea id="htmltextarea" name="data[body]" style="height:300px" class="mce">{$object.body|default:null}</textarea>
		</div>
		
		<div class="htabcontent" id="txt">
			<textarea id="txtarea" name="data[abstract]" style="height:300px; border:1px solid silver; width:450px" class="autogrowarea">{$object.abstract|default:null}</textarea>
		</div>
		
	</div>

	
	<br />
	
	<div id="msgDetailsLoader" class="loader"></div>
	<div id="msgDetails">{include file="inc/form_message_details.tpl"}</div>
	
	
</fieldset>
