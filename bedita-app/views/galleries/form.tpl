<script type="text/javascript">
var urlIcoCalendar = '{$html->url('../img/calendar.gif')}' ;
{literal}
var langs = {
{/literal}
	{foreach name=i from=$conf->langOptions key=lang item=label}
	"{$lang}":	"{$label}" {if !($smarty.foreach.i.last)},{/if}
	{/foreach}
{literal}
} ;

var validate = null ;

$(document).ready(function(){

	// Visualizzazione campi con  calendario
	$('#start').calendar({autoPopUp: 'both', buttonImageOnly: true, buttonImage: urlIcoCalendar , buttonText: 'Calendar'});
	$('#end').calendar({autoPopUp: 'both', buttonImageOnly: true, buttonImage: urlIcoCalendar , buttonText: 'Calendar'});

	// Validazione al submit
	validateFrm = $("#updateform").validate({
		debug:false,
		errorLabelContainer: $("#errorForm"),
		errorClass: "errorFieldForm",
		rules: {
			"data[title]"		: "required",
		},
		messages: {
			"data[title]"		: "Il titolo &egrave; obbligatorio",
		}
	});

	$("#updateform//input[@name=cancella]").bind("click", function() {
		if(!confirm("{/literal}{t}Attention!!! you are deleting an item.\nAre you sure that you want to continue?{/t}{literal}")) {
			return false ;
		}
		document.location = "{/literal}{$html->url('delete/')}{$object.id}{literal}" ;
	}) ;

	$("#updateform").bind("submit", function() {
		// se ci sono stati errori, stampa un messaggio
		if(validateFrm.errorList.length) {
			alert(validateFrm.errorList[0].message) ;
		}
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubtitle").addTranslateField('subtitle', langs) ;
});

{/literal}
</script>

<div id="containerPage">

{formHelper fnc="create" args="'galleries', array('id' => 'updateform', 'action' => 'save', 'type' => 'POST', 'enctype' => 'multipart/form-data')"}

<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />

<div class="FormPageHeader">
	<h1>{t}{$object.title|default:"New Gallery"}{/t}</h1>
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<a id="openAllBlockLabel" style="display:block;" href="javascript:showAllBlockPage(1)"><span style="font-weight:bold;">&gt;</span> {t}open details{/t}</a>
			<a id="closeAllBlockLabel" href="javascript:hideAllBlockPage()"><span style="font-weight:bold;">&gt;</span> {t}close details{/t}</a>
		</td>
		<td style="padding-left:40px;" nowrap>
			{formHelper fnc="submit" args="' salva ', array('name' => 'save', 'class' => 'submit', 'div' => false)"}
			<input type="button" name="cancella" class="submit" value="{t}cancel{/t}" />
		</td>
		<td style="padding-left:40px">&nbsp;</td>
	</tr>
	</table>
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>

<div class="blockForm" id="proprieta">
	<span style="font-weight:bold;">{t}status{/t}</span>:
	{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
	<br />
	<span style="font-weight:bold;">{t}publication{/t}. {t}start{/t}:</span>
	<input type="text" name="data[start]" id="start" value="{if !empty($object.start)}{$object.start|date_format:$conf->date_format}{/if}"/>
	<span style="font-weight:bold;">{t}end{/t}:</span>
	<input type="text" name="data[end]" id="end" value="{if !empty($object.end)}{$object.end|date_format:$conf->date_format}{/if}"/>
	<hr/>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Language{/t}:</td>
		<td>
			<select name="data[lang]">
			{html_options options=$conf->langOptions selected=$object.lang|default:$conf->lang}
			</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr id="Title_TR_{$object.lang|default:$conf->lang}">
		<td class="label">{t}Title{/t}:</td>
		<td>
			<input  class="{literal}{required:true}{/literal}" id="titleInput"  type="text" name="data[title]" value="{$object.title|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
		</td>
		{if ($object)}
		<td><input class="cmdField" id="cmdTranslateTitle" type="button" value="lang ..."/></td>
		{/if}
	</tr>
	{if (isset($object.LangText.title))}
	{foreach name=i from=$object.LangText.title key=lang item=text}
	<tr>
		<td class="label">&#160;</td>
		<td>
			<input type='hidden' value='title' name="data[LangText][{$smarty.foreach.i.iteration}][name]"/>
			<input type="text" name="data[LangText][{$smarty.foreach.i.iteration}][txt]" value="{$text|escape:'html'|escape:'quotes'}"/>&nbsp;
		</td>
		<td>
			<select name="data[LangText][{$smarty.foreach.i.iteration}][lang]">
			{html_options options=$conf->langOptions selected=$lang}
			</select>
			&nbsp;&nbsp;
			<input type="button" name="delete" value=" x " onclick="$('../..', this).remove() ;"/>
		</td>
	</tr>
	{/foreach}
	{/if}
	</table>
	{if ($object)}
	<hr/>
	<table class="tableForm" border="0">
	<tr><td class="label">{t}Alias{/t}:</td><td>{$object.nickname}</td></tr>
	<tr><td class="label">{t}Creato il{/t}:</td><td>{$object.created|date_format:$conf->date_format}</td></tr>
	<tr><td class="label">{t}Da{/t}:</td><td>{$object.UserCreated.userid|default:""}</td></tr>
	<tr><td class="label">{t}Ultima modifica{/t}:</td><td>{$object.modified|date_format:$conf->date_format}</td></tr>
	<tr><td class="label">{t}Da{/t}:</td><td>{$object.UserModified.userid|default:""}</td></tr>
	<tr><td class="label">{t}IP{/t}:</td><td>{$object.IP_created}</td></tr>
	</table>
	{/if}
</div>

<h2 class="showHideBlockButton">{t}Sotto titolo, descrizione{/t}</h2>

<div class="blockForm" style="display:none" id="subtitle">
	<table class="tableForm" border="0">
	<tr id="SubTitle_TR_{$object.lang|default:$conf->lang}">
		<td><textarea class="subtitle">{$object.subtitle|default:''|escape:'html'}</textarea></td>
		{if ($object)}
		<td><input class="cmdField" id="cmdTranslateSubtitle" type="button" value="lang ..."/></td>
		{/if}
	</tr>
	{if (isset($object.LangText.subtitle))}
	{foreach name=i from=$object.LangText.subtitle key=lang item=text}
	<tr>
		<td>
			<input type='hidden' value='subtitle' name="data[LangText][{$smarty.foreach.i.iteration}][name]"/>
			<textarea class="subtitle" name="data[LangText][{$smarty.foreach.i.iteration}][txt]">{$text|escape:'html'}</textarea>
		</td>
		{if ($object)}
		<td>
			<select name="data[LangText][{$smarty.foreach.i.iteration}][lang]">
			{html_options options=$conf->langOptions selected=$lang}
			</select>
			&nbsp;&nbsp;
			<input type="button" name="delete" value=" x " onclick="$('../..', this).remove() ;"/>
		</td>
		{/if}
	</tr>
	{/foreach}
	{/if}
	</table>
</div>

<h2 class="showHideBlockButton">{t}Where put the gallery into{/t}</h2>

<div class="blockForm" id="dove">
	<div id="treecontrol">
		<a href="#">{t}Close all{/t}</a>
		<a href="#">{t}Expand all{/t}</a>
	</div>
	{$beTree->tree("treeWhere", $tree)}
</div>

<h2 class="showHideBlockButton">{t}Images of the gallery{/t}</h2>

<div class="blockForm" id="imgs">

	<h3>:: {t}Gallery images{/t} ::</h3>
	{if !empty($imagesForGallery)}
	<p class="toolbar">
		{t}Images{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	<table class="indexList" cellpadding="0" cellspacing="0" style="width:578px">
	<thead>
	<tr>
		<th>{$beToolbar->order('thumbnail', 'Thumbnail')}</th>
		<th>{$beToolbar->order('id', 'Id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
		<th>-</th>
	</tr>
	</thead>
	<tbody>
	{section name="i" loop=$imagesForGallery}
	<tr class="rowList">
		<td>THUMBNAIL</td>
		<td>{$images[i].id}</td>
		<td>{$images[i].title}</td>
		<td>{$images[i].status}</td>
		<td>{$images[i].created|date_format:$conf->date_format}</td>
		<td>{$images[i].lang}</td>
		<td><input type="button" value="Remove"/></td>
	</tr>
	{/section}
	</tbody>
	</table>
	<p class="toolbar">
		{t}Images{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	{else}
	{t}No images found{/t}
	{/if}

	<br/>

	<h3>:: {t}Available images{/t} ::</h3>
	{if !empty($images)}
	<p class="toolbar">
		{t}Images{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	<table class="indexList" cellpadding="0" cellspacing="0" style="width:578px">
	<thead>
	<tr>
		<th>{$beToolbar->order('thumbnail', 'Thumbnail')}</th>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
		<th>-</th>
	</tr>
	</thead>
	<tbody>
	{section name="i" loop=$images}
	<tr class="rowList">
		<td><img src="http://bedita.channelweb.it/img/bedita_sfo.jpg" style="height:40px;"/></td>
		<td>{$images[i].id}</td>
		<td>{$images[i].title}</td>
		<td>{$images[i].status}</td>
		<td>{$images[i].created|date_format:$conf->date_format}</td>
		<td>{$images[i].lang}</td>
		<td><input type="button" value="Add"/></td>
	</tr>
	{/section}
	</tbody>
	</table>
	<p class="toolbar">
		{t}Images{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	{else}
	{t}No images found{/t}
	{/if}

	<br/>

	<h3>:: {t}Add image to the gallery{/t} ::</h3>
	<input type="file" name="{t}Search{/t}"/><br/>
	<input type="submit" value="{t}Add{/t}"/>
</div>

<h2 class="showHideBlockButton">{t}Proprieta' Custom{/t}</h2>

<div class="blockForm" id="proprietaCustom">
{include file="../pages/form_custom_properties.tpl" el=$object}
</div>

<h2 class="showHideBlockButton">{t}Permessi{/t}</h2>
<div class="blockForm" id="permessi">
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</div>

</form>

</div>