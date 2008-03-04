<script type="text/javascript">
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
	
	$.datepicker.setDefaults({
		showOn: 'both', 
		buttonImageOnly: true, 
	    buttonImage: '{/literal}{$html->webroot}img/calendar.gif{literal}', 
	    buttonText: 'Calendar',
	    dateFormat: '{/literal}{$conf->dateFormatValidation|replace:'yyyy':'yy'}{literal}',
	    beforeShow: customRange
	}, $.datepicker.regional['{/literal}{$currLang}{literal}']); 
	
	$('#start').attachDatepicker();
	$('#end').attachDatepicker();
	
	// Validazione al submit
	$("#updateForm").validate();

	// submit delete
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{$html->url('delete/')}",
		message: "{t}Are you sure that you want to delete the document?{/t}"
		{literal}
	});
	
	// Dal tipo di documento selezionato, visualizza o no parti di form
	$("#updateForm//input[@name='data[object_type_id]']").bind("click", function() {
		activePortionsForm(this.value) ;	
	}) ;
	
	// Selezionano la tipologia di documento
	var type = {/literal}{$object.object_type_id|default:'22'}{literal} ;
	activePortionsForm(type) ;
});

function localTriggerTabs(index) {
	$('#properties_langs_container').triggerTab(index);
	$('#subtitle_langs_container').triggerTab(index);
	$('#long_desc_langs_container').triggerTab(index);
}

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
<form action="{$html->url('/documents/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<fieldset><input  type="hidden" name="data[id]" value="{$object.id|default:''}"/></fieldset>
{include file="../pages/form_header.tpl"}
<div class="blockForm" id="errorForm"></div>
{include file="../pages/form_properties.tpl" doctype=false comments=true}
{include file="../pages/form_subtitle_desc.tpl"}
{include file="../pages/form_tree.tpl"}
{*include file="../pages/form_lang_version.tpl"*}
{include file="../pages/form_longdesc.tpl"}
{*include file="../pages/form_long_desc_lang.tpl"*}
{*include file="../pages/form_file_list.tpl" containerId='multimediaContainer' controller='multimedia' title='Multimedia' items=$multimedia*}
{include file="../pages/form_file_list.tpl" containerId='attachContainer' controller='attachments' title='Attachments' items=$attachments}
{include file="../pages/form_galleries.tpl"}
{include file="../pages/form_custom_properties.tpl" el=$object}
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</form>
</div>