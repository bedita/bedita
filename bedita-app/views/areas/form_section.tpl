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

	validateFrm = $("#updateform").validate({
		debug:false,
		errorLabelContainer: $("#errorForm"),
		errorClass: "errorFieldForm",
		rules: {
			"data[title]"		: "required",
			"data[destination]" : "required"
		},
		messages: {
			"data[title]"		: "{/literal}{t}Title is required{/t}{literal}",
			"data[destination]" : "{/literal}{t}Select destination for section{/t}{literal}",
		}
	});

	$("#updateform//input[@name=cancella]").bind("click", function() {
		if(!confirm("{/literal}{t}Pay attention!!! the operation is potentially dangerous.\nDo you really want to continue?{/t}{literal}")) {
			return false ;
		}
		document.location = "{/literal}{$html->url('deleteSection/')}{$section.id}{literal}" ;
	}) ;

	$("#updateform").bind("submit", function() {
		// se ci sono stati errori, stampa un messaggio
		if(validateFrm.errorList.length) {
			alert(validateFrm.errorList[0].message) ;
		}
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubTitle").addTranslateField('subtitle', langs) ;
});

{/literal}
</script>

<div id="containerPage">

{formHelper fnc="create" args="'area', array('id' => 'updateform', 'action' => 'saveSection', 'type' => 'POST', 'enctype' => 'multipart/form-data')"}

<input  type="hidden" name="data[id]" value="{$section.id|default:''}" />

<div class="FormPageHeader">
{include file="../pages/form_header.tpl"}
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
{include file="../pages/form_properties.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Where put the section into{/t}</h2>
<div class="blockForm" id="whereto">
{include file="../pages/form_tree.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Custom Properties{/t}</h2>
<div class="blockForm" id="customProperties">
{include file="../pages/form_custom_properties.tpl" el=$section}
</div>

<h2 class="showHideBlockButton">{t}Permissions{/t}</h2>
<div class="blockForm" id="permissions">
{include file="../pages/form_permissions.tpl" el=$section recursion=true}
</div>

</form>

</div>