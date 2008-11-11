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
	{if !empty($object) && !empty($object.id) && ($object.fixed == 1)}
		{literal}
		$("#titleBEObject").attr("readonly",true);
		$("#nicknameBEObject").attr("readonly",true);
		$("#start").attr("readonly",true);
		$("#start").attr("value","");
		$("#end").attr("readonly",true);
		$("#end").attr("value","");
		$("#delBEObject").attr("disabled",true);
		{/literal}
	{else}
		{literal}
		$("#titleBEObject").attr("readonly",false);
		$("#nicknameBEObject").attr("readonly",false);
		$("#start").attr("readonly",false);
		$("#end").attr("readonly",false);
		{/literal}{if !empty($object)}{literal}
			$("#delBEObject").attr("disabled",false);
		{/literal}{/if}{literal}
		$("input.dateinput").datepicker();
		{/literal}
	{/if}
	{literal}
});

{/literal}
</script>