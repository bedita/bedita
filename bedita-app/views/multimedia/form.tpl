<script type="text/javascript">
var urlIcoCalendar 		= '{$html->url('../img/calendar.gif')}' ;
var formatDateCalendar	= '{$conf->df_jquery_calendar}' ;

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
	$('#start').calendar({autoPopUp: 'both', buttonImageOnly: true, buttonImage: urlIcoCalendar , buttonText: 'Calendar', dateFormat:formatDateCalendar});
	$('#end').calendar({autoPopUp: 'both', buttonImageOnly: true, buttonImage: urlIcoCalendar , buttonText: 'Calendar', dateFormat:formatDateCalendar});
	
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

	$("#updateform").bind("submit", function() {
		// se ci sono stati errori, stampa un messaggio
		if(validateFrm.errorList.length) {
			alert(validateFrm.errorList[0].message) ;
		}
	}) ;

	// Conferma cancellazione
	$("#updateform//input[@name=cancella]").bind("click", function() {
		if(!confirm("Attenzione!!! operazione potenzialmente dannosa.\nSicuro di voler continuare?")) {
			return false ;
		}
		
		$("#updateform").attr("action", "{/literal}{$html->url('delete/')}{literal}") ;
		$("#updateform").submit() ;
//		document.location = "{/literal}{$html->url('delete/')}{$object.id}{literal}" ;
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubtitle").addTranslateField('subtitle', langs) ;
	
	// TEmporaneo
	$("input[@name='save']").attr("disabled", 1) ;
	
});

{/literal}
</script>

<div id="containerPage">

{formHelper fnc="create" args="'multimedia', array('id' => 'updateform', 'action' => 'save', 'type' => 'POST', 'enctype' => 'multipart/form-data')"}
<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />

<div class="FormPageHeader">
	<h1>{$object.title|default:"nuovo documento"}</h1>
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<a id="openAllBlockLabel" style="display:block;" href="javascript:showAllBlockPage(1)"><span style="font-weight:bold;">&gt;</span> {t}apri tutti i dettagli{/t}</a>
			<a id="closeAllBlockLabel" href="javascript:hideAllBlockPage()"><span style="font-weight:bold;">&gt;</span> {t}chiudi tutti i dettagli{/t}</a>
		</td>
		<td style="padding-left:40px;" nowrap>
			{formHelper fnc="submit" args="' salva ', array('name' => 'save', 'class' => 'submit', 'div' => false, 'disabled' => '1')"}
			<input type="button" name="cancella" class="submit" value="cancella"/>
		</td>
		<td style="padding-left:40px">&nbsp;</td>
	</tr>
	</table>
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
<fieldset>
	<span style="font-weight:bold;">{t}status{/t}</span>:
	{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
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
	<tr>
		<td class="label">{t}Alias{/t}:</td><td>{$object.nickname}</td>
		<td class="label">{t}IP{/t}:</td><td>{$object.IP_created}</td>
	</tr>
	<tr>
		<td class="label">{t}Creato il{/t}:</td><td>{$object.created|date_format:$conf->date_format}</td>
		<td class="label">{t}Da{/t}:</td><td>{$object.UserCreated.userid|default:""}</td>
	</tr>
	<tr>
		<td class="label">{t}Ultima modifica{/t}:</td><td>{$object.modified|date_format:$conf->date_format}</td>
		<td class="label">{t}Da{/t}:</td><td>{$object.UserModified.userid|default:""}</td>
	</tr>
	</table>
	{/if}
</fieldset>
</div>

<h2 class="showHideBlockButton">{t}File{/t}</h2>
<div class="blockForm" style="display:block" id="subtitle">
<fieldset>
	{if (isset($object))}
		<table class="tableForm" border="0">
			{if ($object.ObjectType.name == "image")}
			<tr>
			<td colspan="2">
			{thumb 
				width="100" 
				height="100" 
				file=$imagePath
				cache=$CACHE 
				MAT_SERVER_PATH=$MEDIA_ROOT 
				MAT_SERVER_NAME=$MEDIA_URL
				linkurl=$imageUrl
				longside=""
				shortside=""
				html=""
				dev=""
				offset_w = ""
				sharpen = ""
				addgreytohint = ""	
			} 	
			</td>
			</tr>
			{/if}
			<tr>
			<td class="label">{t}File name{/t}:</td><td>{$object.name|default:""}</td>
			</tr>
			<tr>
			<td class="label">{t}File type{/t}:</td><td>{$object.type|default:""}</td>
			</tr>
			<tr>
			<td class="label">{t}File size{/t}:</td><td>{$object.size|default:""}</td>
			</tr>
		</table>
	{/if}
</fieldset>
</div>

<h2 class="showHideBlockButton">{t}Subtitle, description{/t}</h2>
<div class="blockForm" style="display:none" id="subtitle">
{include file="../pages/form_subtitle_desc.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Custom Properties{/t}</h2>
<div class="blockForm" id="customProperties">
{include file="../pages/form_custom_properties.tpl" el=$object}
</div>

<h2 class="showHideBlockButton">{t}Permissions{/t}</h2>
<div class="blockForm" id="permissions">
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</div>

</form>

</div>