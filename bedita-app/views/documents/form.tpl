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
		if(!confirm("Attenzione!!! operazione potenzialmente dannosa.\nSicuro di voler continuare?")) {
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

{formHelper fnc="create" args="'documents', array('id' => 'updateform', 'action' => 'save', 'type' => 'POST', 'enctype' => 'multipart/form-data')"}

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
			{formHelper fnc="submit" args="' salva ', array('name' => 'save', 'class' => 'submit', 'div' => false)"}
			<input type="button" name="cancella" class="submit" value="cancella" />
		</td>
		<td style="padding-left:40px">&nbsp;</td>
	</tr>
	</table>
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Proprieta'{/t}</h2>

<div class="blockForm" id="proprieta">
	<span style="font-weight:bold;">{t}status{/t}</span>:
	{html_radios name="status" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
	<br />
	<span style="font-weight:bold;">{t}pubblicazione{/t}. {t}inizio{/t}:</span>
	<input type="text" name="start" id="start" value="{$object.start|date_format:'%d/%m/%Y'}"/>
	<span style="font-weight:bold;">{t}fine{/t}:</span>
	<input type="text" name="end" id="end" value="{$object.end|date_format:'%d/%m/%Y'}"/>
	<hr/>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Lingua{/t}:</td>
		<td>
			<select name="lang">
			{html_options options=$conf->langOptions selected=$object.lang|default:$conf->lang}
			</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr id="Title_TR_{$object.lang|default:$conf->lang}">
		<td class="label">{t}Titolo{/t}:</td>
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
		<td></td>
		<td>
			<textarea class="subtitle">{$object.subtitle|default:''|escape:'html'}</textarea>
		</td>
		{if ($object)}
		<td><input class="cmdField" id="cmdTranslateSubtitle" type="button" value="lang ..."/></td>
		{/if}
	</tr>
	{if (isset($object.LangText.subtitle))}
	{foreach name=i from=$object.LangText.subtitle key=lang item=text}
	<tr>
		<td></td>
		<td>
			<input type='hidden' value='subtitle' name="data[LangText][{$smarty.foreach.i.iteration}][name]"/>
			<textarea class="subtitle" name="data[LangText][{$smarty.foreach.i.iteration}][txt]">{$text|escape:'html'}</textarea>
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
</div>

<h2 class="showHideBlockButton">{t}Dove inserire il documento{/t}</h2>

<div class="blockForm" id="dove">
	<div id="treecontrol">
		<a href="#">{t}Chiudi tutti{/t}</a>
		<a href="#">{t}Espandi tutto{/t}</a>
	</div>
	{$beTree->tree("treeWhere", $tree)}
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