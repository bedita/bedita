<script type="text/javascript">
var urlIcoCalendar = '{$html->url('../img/calendar.gif')}' ;
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the gallery?{/t}" ;

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
	
	// submit delete
	$("#delBEObject").bind("click", function() {
		if(!confirm(message)) {
			return false ;
		}
		$("#updateForm").attr("action", urlDelete)
		$("#updateForm").submit();
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
{include file="../pages/form_file_list.tpl" containerId='multimediaContainer' controller='multimedia' title='Multimedia' items=$multimedia}
{include file="../pages/form_custom_properties.tpl" el=$object}
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</form>
</div>