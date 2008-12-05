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

<fieldset id="bookdetails">

<input type="hidden" name="data[weight_unit]" value="gr"/>
<input type="hidden" name="data[length_unit]" value="cm"/>
<input type="hidden" name="data[volume_unit]" value="cm3"/>

<table>
	<tr>
		<th>{t}title{/t}:</th>
		<td colspan="4"><input type="text" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject"/></td>
	</tr>
	<tr>
		<th>{t}subtitle{/t}:</th>
		<td colspan="4"><textarea id="subtitle" style="width:380px; height:30px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea></td>
	</tr>
	<tr>
		<th>{t}author/s{/t}:</th>
		<td>
			<input type="text" name="data[author]" value="{$object.author|default:''|escape:'html'|escape:'quotes'}" id="authorBEObject"/>
		</td>
		<td colspan="2">	
			<input type="button" class="modalbutton" value="get from list" rel="{$html->url('/biographies')}">
		</td>
	</tr>
	<tr>
		<th>{t}publisher{/t}:</th>
		<td><input type="text" name="data[publisher]" value="{$object.publisher|default:''}" /></td>
		<th>{t}series{/t}:</th>
		<td><input type="text" name="data[series]" value="{$object.series|default:''}"/></td>

	</tr>
	<tr>
		<th>{t}place{/t}:</th>
		<td><input type="text" name="data[production_place]" value="{$object.production_place|default:''}"/></td>
		<th>{t}year{/t}:</th>
		<td><input type="text" style="width:30px" name="data[year]" value="{$object.year|default:''}" size="4" maxlength="4" /></td>
		
	</tr>
	<tr>
		<th>{t}isbn/issn{/t}:</th>
		<td><input type="text" name="data[isbn]" value="{$object.isbn|default:''}" /></td>
		
		<th>{t}language{/t}:</th>
		<td>
		{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
		<select name="data[lang]" id="main_lang">
			{foreach key=val item=label from=$conf->langOptions name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
			{foreach key=val item=label from=$conf->langsIso name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
		</select>
		</td>
	</tr>
	<tr>
		<th>{t}abstract:{/t}</th>
		<td  colspan="4">
			<textarea name="data[abstract]" class="mce">{$object.abstract|default:''}</textarea>
		</td>
	</tr>

</table>

<hr />

<table>
	<tr>
		<th>{t}location{/t}:</th>
		<td><input type="text" name="data[location]" value="{$object.location|default:''}"/></td>
		<td rowspan="4">
			qui in futuro un bel lettore di codice a barre via webcam?
			<br>
			http://en.barcodepedia.com/download
			<br>
			http://scan.jsharkey.org/
		</td>
	</tr>
	<tr>
		<th>{t}serial number{/t}:</th>
		<td><input type="text" name="data[serial_number]" value="{$object.serial_number|default:''}"/></td>
	</tr>
	<tr>
		<th>{t}weight{/t}:</th>
		<td><input type="text" style="width:30px" name="data[weight]" value="{$object.weight|default:''}"/> (gr)</td>
	</tr>
	<tr>
		<th>{t}width{/t}:</th>
		<td><input type="text" style="width:30px" name="data[width]" value="{$object.width|default:''}"/> (cm)</td>
	</tr>
	<tr>
		<th>{t}height{/t}:</th>
		<td><input type="text" style="width:30px" name="data[height]" value="{$object.height|default:''}"/> (cm)</td>
	</tr>
	<tr>
		<th>{t}depth{/t}:</th>
		<td><input type="text" style="width:30px" name="data[depth]" value="{$object.depth|default:''}"/> (cm)</td>
	</tr>
</table>



</fieldset>
