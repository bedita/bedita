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
	$("#updateForm").validate();
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{$html->url('delete/')}",
		message: "{t}Are you sure that you want to delete the tag?{/t}"
		{literal}
	});
});

{/literal}
</script>
<div id="containerPage">
<form action="{$html->url('/tags/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input  type="hidden" name="data[id]" value="{$object.id|default:''}"/>
{include file="../pages/form_header.tpl"}
<div class="blockForm" id="errorForm"></div>
{include file="../pages/form_tag.tpl"}
</form>
</div>