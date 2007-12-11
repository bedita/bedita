<script type="text/javascript">
var urlIcoCalendar 	= '{$html->url('../img/calendar.gif')}' ;
urlDelete 			=  "{$html->url('delete/')}{$object.id}" ;
{literal}
var langs = {
{/literal}
	{foreach name=i from=$conf->langOptions key=lang item=label}
	"{$lang}":	"{$label}" {if !($smarty.foreach.i.last)},{/if}
	{/foreach}
{literal}
} ;

var validate = null ;

$.validator.setDefaults({ 
	success: function(label) {
		// set &nbsp; as text for IE
		label.html("&nbsp;").addClass("checked");
	}
});

$(document).ready(function(){

	// Visualizzazione campi con  calendario
	$('#start').calendar({autoPopUp: 'both', buttonImageOnly: true, buttonImage: urlIcoCalendar , buttonText: 'Calendar'});
	$('#end').calendar({autoPopUp: 'both', buttonImageOnly: true, buttonImage: urlIcoCalendar , buttonText: 'Calendar'});

	// Validazione al submit
	$("#updateForm").validate();

	$("#updateForm input[@name=cancella]").bind("click", function() {
		if(!confirm("{/literal}{t}Attention!!! you are deleting an item.\nAre you sure that you want to continue?{/t}{literal}")) {
			return false ;
		}
		$("#frmDelete //input[@name='data[id]']").attr("value", $(this).attr("name")) ;
		
		$("#updateForm").attr("action", urlDelete) ;
		$("#updateForm").get(0).submit() ;

		return false ;	
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubTitle").addTranslateField('subtitle', langs) ;
	$("#cmdTranslateShortDesc").addTranslateField('shortdesc', langs) ;
	$("#cmdTranslateLongDesc").addTranslateField('longdesc', langs) ;
});

{/literal}
</script>

<div id="containerPage">

<form action="{$html->url('/galleries/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">

<fieldset>
<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />
</fieldset>

<div class="FormPageHeader">
{include file="../pages/form_header.tpl"}
</div>

<div class="blockForm" id="errorForm"></div>

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
{include file="../pages/form_properties.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Subtitle, description{/t}</h2>
<div class="blockForm" style="display:none" id="subtitle">
{include file="../pages/form_subtitle_desc.tpl"}
</div>

<h2 class="showHideBlockButton">{t}Images of the gallery{/t}</h2>
<div class="blockForm" id="imgs" style="display:block">
{include file="../pages/form_multimedia.tpl" multimedia=$multimedia}
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