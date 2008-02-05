<script type="text/javascript">
var urlIcoCalendar 		= '{$html->url('../img/calendar.gif')}' ;

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

	$.datepicker.setDefaults({
		showOn: 'both', 
		buttonImageOnly: true, 
	    buttonImage: urlIcoCalendar, 
	    buttonText: 'Calendar',
	    dateFormat: '{/literal}{$conf->dateFormatValidation|replace:'yyyy':'yy'}{literal}',
	    beforeShow: customRange
	}, $.datepicker.regional['{$currLang}']); 
	
	$('#start').attachDatepicker();
	$('#end').attachDatepicker();
	
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
	$("#updateForm input[@name=cancella]").bind("click", function() {
		if(!confirm("Attenzione!!! operazione potenzialmente dannosa.\nSicuro di voler continuare?")) {
			return false ;
		}
		
		$("#updateform").attr("action", "{/literal}{$html->url('delete/')}{literal}") ;
		$("#updateform").submit() ;
//		document.location = "{/literal}{$html->url('delete/')}{$object.id}{literal}" ;
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubTitle").addTranslateField('subtitle', langs) ;
	$("#cmdTranslateShortDesc").addTranslateField('shortdesc', langs) ;
	$("#cmdTranslateLongDesc").addTranslateField('longdesc', langs) ;
	
	// TEmporaneo
	$("input[@name='save']").attr("disabled", 1) ;
	
});

{/literal}
</script>

<div id="containerPage">

<form action="{$html->url('/multimedia/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">

<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />

<div class="FormPageHeader">
{include file="../pages/form_header.tpl"}
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
{include file="../pages/form_properties.tpl" doctype=false publication=false}
</div>

<h2 class="showHideBlockButton">{t}File{/t}</h2>
<div class="blockForm" style="display:block" id="multimediaitem">
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