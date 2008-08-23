{* title and description *}

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
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough, | ,link,unlink,pastetext,pasteword, | ,removeformat,charmap,fullscreen",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "", 
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	//theme_advanced_statusbar_location : "bottom",
	//theme_advanced_resizing : true,
	theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,address",
	width : "400",

	
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

<div class="tab"><h2>{t}Book details{/t}</h2></div>

<fieldset id="title">

<table>
	<tr>
		<th>{t}Title{/t}</th>
		<td><input type="text" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject"/></td>
	</tr>
	<tr>
		<th>{t}Subtitle{/t}</th>
		<td><textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea></td>
	</tr>
	<tr>
		<th>{t}Author/s{/t}</th>
		<td>
			<input type="text" name="data[author]" value="{$object.author|escape:'html'|escape:'quotes'}" id="authorBEObject"/>
			<input type="button" class="modalbutton" value="get from list" rel="{$html->url('/biographies')}">
		</td>
	</tr>
	<tr>
		<th>{t}Abstract{/t}</th>
		<td>
			<textarea name="data[abstract]" class="mce">{$object.abstract|default:''}</textarea>
		</td>
	</tr>

	
</table>


</fieldset>
