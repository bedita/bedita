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
	
	$("#updateForm").validate();
	
	$("#updateForm//input[@name='data[object_type_id]']").bind("click", function() {
		activePortionsForm(this.value) ;	
	}) ;
	var type = {/literal}{$object.object_type_id|default:'22'}{literal} ;
	activePortionsForm(type) ;
	
	$('div.tabsContainer > ul').tabs();
	$('div.tabsContainer > ul > li > a').changeActiveTabs();
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