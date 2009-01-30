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
	width : "320",

	
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
		<td id="fototessera" style="vertical-align:top; padding-right:10px;" rowspan="4">
		{if !empty($attach[0])}	
			{assign_associative var="params" width=130 height=170 longside=false mode="crop"}
			{$beEmbedMedia->object($attach[0],$params)}
		{else}
			<div style="border:2px dashed gray; width:126px; height:166px">&nbsp;</div>
		{/if}
		</td>
		<td>
			<label>{t}title{/t}:</label><br />
			<input id="titleBEObject" style="width:320px;" type="text" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" />
		</td>
	</tr>
	<tr>
		<td>
			<label>{t}subtitle{/t}:</label><br />
			<textarea id="subtitle" style="width:320px; height:30px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
		</td>
	</tr>
	<tr>
		<td>
			<label>{t}abstract:{/t}</label><br />
			<textarea name="data[abstract]" class="mce">{$object.abstract|default:''}</textarea>
		</td>
	</tr>
</table>

<hr />

<table class="booksauthor" border=0 style="width:100%;">
<tr>
	<td></td><td style="padding:0px 0px 0px 10px">nome e cognome</td><td style="padding:0px 0px 0px 10px">ruolo</td>
</tr>
{if !empty($relObjects.author)}
{foreach from=$relObjects.author name=i item=author}
<input type="hidden" name="data[RelatedObject][author][{$author.id}][id]" value="{$author.id}"/>
<input type="hidden" name="data[RelatedObject][author][{$author.id}][priority]" value="{$author.priority}"/>
	<tr>
		<th style="width:20px">{t}author{/t}{$smarty.foreach.i.iteration}:</th>
		<td>
			<input style="width:100px" type="text" value="{$author.name}" />
			<input style="width:100px" type="text" value="{$author.name}" />
		</td>
		<td>	
			<select style="width:60px" class="authorrole" >
				<option>author</option>
				<option>editor</option>
				<option>illustrator</option>
				<option>translator</option>
				<option>speaker</option>
			</select>
		</td>
		<td nowrap>
			<input type="button" title="{t}remove item{/t}" style="width:25px" value="-" />	
		</td>
	</tr>
{/foreach}
{/if}

{*http://memory.loc.gov/cocoon/loc.terms/relators/dc-relators.html*}

<input type="hidden" name="data[RelatedObject][author][0][switch]" value="author"/>
	<tr>
		<th nowrap style="width:20px">{t}author{/t}{$smarty.foreach.i.iteration+1|default:1}:</th>
		<td nowrap>
			<input type="text" style="width:100px" name="data[author]" id="authorBEObject" />
			<input type="text" style="width:120px" name="data[author]" id="authorBEObject" />	
		</td>
		<td>
			<select style="width:60px" name="data[authorrole]" class="authorrole" >
				<option>author</option>
				<option>editor</option>
				<option>illustrator</option>
				<option>translator</option>
				<option>speaker</option>
			</select>
		</td>
		<td nowrap>
			{assign var=rel value='author'}
			<input type="button" class="modalbutton" title="{$rel|upper} : {t}select from author repository{/t}"
				rel="{$html->url('/areas/showObjects/')}{$object.id|default:0}/{$rel}/"  
				value="{t}get from list{/t}" />
			<input type="button" title="{t}add item{/t}" style="width:25px" value="+" />	
		</td>
	</tr>
</table>

<hr />

<table>
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
		<td><input type="text" style="width:38px" name="data[year]" value="{$object.year|default:''}" size="4" maxlength="4" /></td>
		
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
</table>
</fieldset>

<div class="tab"><h2>{t}More data{/t}</h2></div>

<fieldset id="moredata">

<table>
	<tr>
		<th>{t}serial number{/t}:</th>
		<td colspan="2"><input type="text" name="data[serial_number]" value="{$object.serial_number|default:''}"/></td>
	</tr>
	<tr>
		<th>{t}location code{/t}:</th>
		<td colspan="2"><input type="text" name="data[location]" value="{$object.location|default:''}"/></td>
	</tr>
	<tr>
		<th>{t}width{/t}:</th>
		<td><input type="text" style="width:30px" name="data[width]" value="{$object.width|default:''}"/> cm</td>
		<th>{t}height{/t}:</th>
		<td><input type="text" style="width:30px" name="data[height]" value="{$object.height|default:''}"/> cm</td>
	</tr>
	<tr>
		<th>{t}depth{/t}:</th>
		<td><input type="text" style="width:30px" name="data[depth]" value="{$object.depth|default:''}"/> cm</td>
		<th>{t}weight{/t}:</th>
		<td><input type="text" style="width:30px" name="data[weight]" value="{$object.weight|default:''}"/> gr</td>
	</tr>
</table>



</fieldset>
