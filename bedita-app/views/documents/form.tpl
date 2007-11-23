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

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
{include file="../pages/form_properties.tpl" doctype=false}
</div>

<h2 class="showHideBlockButton">{t}Subtitle, description{/t}</h2>
<div class="blockForm" style="display:none" id="subtitle">
{include file="../pages/form_subtitle_desc.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Where put the document into{/t}</h2>
<div class="blockForm" id="whereto">
{include file="../pages/form_tree.tpl"}
</div>

<div id="divLinkExtern"  style="display: none">
<h2 class="showHideBlockButton">{t}External links{/t}</h2>
<div class="blockForm" id="linkEsterno" style="display: block">
	LINK ESTERNO
</div>
</div>

<div id="divLinkIntern"  style="display: none">
<h2 class="showHideBlockButton">{t}Objects{/t}</h2>
<div class="blockForm" id="linkInterno"  style="display: block">
	LINK INTERNO
</div>
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