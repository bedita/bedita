<script type="text/javascript">
{literal}
// USATO????
var langs = {
{/literal}
	{foreach name=i from=$conf->langOptions key=lang item=label}
	"{$lang}":	"{$label}" {if !($smarty.foreach.i.last)},{/if}
	{/foreach}
{literal}
} ;
// FINE USATO????

$(document).ready(function(){
	
	$.datepicker.setDefaults({
		speed: 'fast', 
		showOn: 'both',
		closeAtTop: false, 
		buttonImageOnly: true, 
	    buttonImage: '{/literal}{$html->webroot}img/iconCalendar.gif{literal}', 
	    buttonText: '{t}Open Calendar{/t}',
	    dateFormat: '{/literal}{$conf->dateFormatValidation|replace:'yyyy':'yy'}{literal}',
		firstDay: 1,
	    beforeShow: customRange
	}, $.datepicker.regional['{/literal}{$currLang}{literal}']);

	{/literal}
	{if !empty($object.id) && ($object.fixed == 1)}
		{literal}
		$("#titleBEObject").attr("readonly",true).attr("disabled",true);
		$("#nicknameBEObject").attr("readonly",true).attr("disabled",true);
		$("#start").attr("readonly",true);
		$("#start").attr("value","");
		$("#end").attr("readonly",true);
		$("#end").attr("value","");
		$("#delBEObject").attr("disabled",true);
		$(".secondacolonna .modules label").addClass("fixedobject").attr("title","fixed object");
		{/literal}
	{else}
		{literal}
		$("#delBEObject").attr("disabled",false);
		$("input.dateinput").datepicker();
		{/literal}
	{/if}
	{literal}
});

{/literal}
</script>