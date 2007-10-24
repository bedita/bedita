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
/*		
		// Formatta le date start, end
		var Year 	= $("#updateform//input[@name='data[start]']").val().match({/literal}{$conf->match_year}{literal})[1] ;
		var Month 	= $("#updateform//input[@name='data[start]']").val().match({/literal}{$conf->match_month}{literal})[1] ;
		var Day 	= $("#updateform//input[@name='data[start]']").val().match({/literal}{$conf->match_day}{literal})[1] ;
		$("#updateform//input[@name='data[start]']").val(Year+"-"+Month+"-"+Day) ;
		
		var Year 	= $("#updateform//input[@name='data[end]']").val().match({/literal}{$conf->match_year}{literal})[1] ;
		var Month 	= $("#updateform//input[@name='data[end]']").val().match({/literal}{$conf->match_month}{literal})[1] ;
		var Day 	= $("#updateform//input[@name='data[end]']").val().match({/literal}{$conf->match_day}{literal})[1] ;
		$("#updateform//input[@name='data[end]']").val(Year+"-"+Month+"-"+Day) ;
*/
	}) ;

	// Conferma cancellazione
	$("#updateform//input[@name=cancella]").bind("click", function() {
		if(!confirm("Attenzione!!! operazione potenzialmente dannosa.\nSicuro di voler continuare?")) {
			return false ;
		}
		document.location = "{/literal}{$html->url('delete/')}{$object.id}{literal}" ;
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubtitle").addTranslateField('subtitle', langs) ;
	
	// Dal tipo di documento selezionato, visualizza o no parti di form
	$("#updateform//input[@name='data[object_type_id]']").bind("click", function() {
		activePortionsForm(this.value) ;	
	}) ;
	
	// Selezionano la tipologia di documento
	var type = {/literal}{$object.object_type_id|default:'22'}{literal} ;
	activePortionsForm(type) ;
	
	$("#updateform//input[@name='data[object_type_id]'][@value='"+type+"']").get(0).checked = true ;
});

objectTypeDiv = {
	"22" : "",
	"24" : "#divLinkExtern",
	"23" : "#divLinkIntern"
}

function activePortionsForm(objectType) {
	for(k in objectTypeDiv) {
		if(k != objectType && objectTypeDiv[k].length) {
			$(objectTypeDiv[k]).hide("fast") ;
		} else   {
			if(objectTypeDiv[k].length)
				$(objectTypeDiv[k]).show("fast") ;
		}
	}
}

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
	{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
	<br />
	<span style="font-weight:bold;">{t}pubblicazione{/t}. {t}inizio{/t}:</span>
	<input type="text" name="data[start]" id="start" value="{$object.start|date_format:$conf->date_format}"/>
	<span style="font-weight:bold;">{t}fine{/t}:</span>
	<input type="text" name="data[end]" id="end" value="{$object.end|date_format:$conf->date_format}"/>
	<hr/>
	<span style="font-weight:bold;">{t}Scegli il tipo di documento{/t}:</span>
	{html_radios name="data[object_type_id]" options=$conf->docTypeOptions selected=$object.object_type_id|default:'22' separator="&nbsp;"}
	<hr/>
	
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Lingua{/t}:</td>
		<td>
			<select name="data[lang]">
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
</div>

<h2 class="showHideBlockButton">{t}Sotto titolo, descrizione{/t}</h2>

<div class="blockForm" style="display:none" id="subtitle">

	<table class="tableForm" border="0">
	<tr id="SubTitle_TR_{$object.lang|default:$conf->lang}">
		<td></td>
		<td>
			<textarea name="data[subtitle]" class="subtitle">{$object.subtitle|default:''|escape:'html'}</textarea>
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

<div id="divLinkExtern"  style="display: none">
	<h2 class="showHideBlockButton">{t}Inserisci un link esterno{/t}</h2>

	<div class="blockForm" id="linkEsterno" style="display: block">
		LINK ESTERNO
	</div>
</div>

<div id="divLinkIntern"  style="display: none">
	<h2 class="showHideBlockButton">{t}Seleziona un oggetto{/t}</h2>

	<div class="blockForm" id="linkInterno"  style="display: block">
		LINK INTERNO
	</div>
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