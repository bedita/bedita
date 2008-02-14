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
<fieldset><input  type="hidden" name="data[id]" value="{$object.id|default:''}" /></fieldset>
{include file="../pages/form_header.tpl"}
<div class="blockForm" id="errorForm"></div>
{include file="../pages/form_container_properties.tpl"}
{include file="../pages/form_subtitle_desc.tpl"}
{include file="../pages/form_multimedia.tpl" multimedia=$multimedia}
{include file="../pages/form_custom_properties.tpl" el=$object}
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</form>
</div>