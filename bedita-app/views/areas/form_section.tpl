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
	
	$("#updateForm").validate(); 

	$("#updateForm//input[@name=cancella]").bind("click", function() {
		if(!confirm("{/literal}{t}Pay attention!!! the operation is potentially dangerous.\nDo you really want to continue?{/t}{literal}")) {
			return false ;
		}
		document.location = "{/literal}{$html->url('deleteSection/')}{$section.id}{literal}" ;
	}) ;

	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubTitle").addTranslateField('subtitle', langs) ;
});

{/literal}
</script>

<div id="containerPage">

<form action="{$html->url('/areas/saveSection')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<fieldset>
	<input  type="hidden" name="data[id]" value="{$section.id|default:''}" />
</fieldset>

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