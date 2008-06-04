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
	theme_advanced_toolbar_location : "bottom",
	theme_advanced_toolbar_align : "center",
	//theme_advanced_statusbar_location : "bottom",
	//theme_advanced_resizing : true,
	// Example content CSS (should be your site CSS)
	content_css : "/css/htmleditor.css",
	width : "470",
	height : "120"

});

	</script>
{/literal}
{/if}

<div class="tab"><h2>{t}Long Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">
	
	<label>{t}Short text{/t}:</label>
	<textarea name="data[abstract]" class="mce">{$object.abstract|default:''}</textarea>
	
	<label>{t}Long text{/t}:</label>
	<textarea name="data[body]" class="mce">{$object.body|default:''}</textarea>
		
</fieldset>