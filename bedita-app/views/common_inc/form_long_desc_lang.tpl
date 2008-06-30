{if ($conf->mce|default:true)}
	{$javascript->link("tiny_mce/tiny_mce")}
{literal}
<script language="javascript" type="text/javascript">

tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mce",
	plugins : "safari,pagebreak,paste,fullscreen",

	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough, | ,formatselect,bullist,numlist,blockquote, | ,link,unlink,pastetext,pasteword, | ,hr,pagebreak, | ,removeformat,charmap,code,fullscreen",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "", 
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	//theme_advanced_statusbar_location : "bottom",
	//theme_advanced_resizing : true,
	theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address",
	width : "470",

	
	// Example content CSS (should be your site CSS)
	content_css : "/css/htmleditor.css",
    relative_urls : false,
	convert_urls : false,
    remove_script_host : false,
	document_base_url : "/"
	
	

});

	</script>
{/literal}
{/if}

<div class="tab"><h2>{t}Long Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">
	
	<label>{t}Short text{/t}:</label>
	<textarea name="data[abstract]" style="height:200px" class="mce">{$object.abstract|default:''}</textarea>
	
	<label>{t}Long text{/t}:</label>
	<textarea name="data[body]" style="height:400px" class="mce">{$object.body|default:''}</textarea>
		
</fieldset>