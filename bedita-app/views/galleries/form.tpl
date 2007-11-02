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
{include file="../pages/form_header.tpl"}
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="proprieta">
{include file="../pages/form_properties.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Sotto titolo, descrizione{/t}</h2>
<div class="blockForm" style="display:none" id="subtitle">
{include file="../pages/form_subtitle_desc.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Where put the gallery into{/t}</h2>
<div class="blockForm" id="dove">
{include file="../pages/form_tree.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Images of the gallery{/t}</h2>
<div class="blockForm" id="imgs">
{include file="../pages/form_images.tpl"}
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