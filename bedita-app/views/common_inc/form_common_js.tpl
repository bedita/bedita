{*
** tutta la validazione e i controlli e i comportamenti del form updateform comune vanno QUI
** soprattutto pulire menucommands.tpl / view.tpl / form.tpl
** questo TPL è incluso nelle varie view.tpl degli oggetti e non nelle liste e nelel categorie
** controlli aggiuntivi specifici per il singolo form locale del modulo vanno nel suo module.js
** 
** parametri opzionali ricevuti:
** saveurl = url, default: currentModule.path
**
*}


{assign var="user" value=$session->read('BEAuthUser')}
{assign var="submiturl" value=$submiturl|default:$currentModule.path}

<script type="text/javascript">
{literal}
$(document).ready(function(){
	
	//alert("pop");
	
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}",
		message: "{t}Are you sure that you want to delete the item?{/t}",
		formId: "updateForm"
		{literal}
	});
	
	$("div.insidecol input[@name='save']").click(function() {
		$("#updateForm").submit();
	});
	
	$("div.insidecol input[@name='clone']").click(function() {
		$("#updateForm").attr("action","{/literal}{$html->url('/')}{$submiturl}{literal}/cloneObject");
		var cloneTitle=prompt("{/literal}{t}Title{/t}{literal}",$("input[@name='data[title]']").val()+"-copy");
		if (cloneTitle) {
			$("input[@name='data[title]']").attr("value",cloneTitle);
			$("#updateForm").submit();
		}
	});
	
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
	
	$("input.dateinput").datepicker();

{/literal}{if !($object.id)}{literal}

		$("#delBEObject,#cloneBEObject").hide();

{/literal}{/if}{literal}

{/literal}{if $module_modify != 1}{literal}
		
		$("#saveBEObject,#delBEObject").attr("disabled",true);

{/literal}{/if}{literal}

{/literal}{if !($perms->isDeletable($user.userid,$user.groups,$object.Permissions))}{literal}
		
		$("#delBEObject").attr("disabled",true);

{/literal}{/if}{literal}

{/literal}{if !($perms->isWritable($user.userid,$user.groups,$object.Permissions))}{literal}
		
		$("#saveBEObject,#cloneBEObject,#delBEObject").attr("disabled",true);
	
{/literal}{/if}{literal}

{/literal}{if ($object.fixed == 1)}{literal}

		$("#titleBEObject").attr("readonly",true);
		$("#nicknameBEObject").attr("readonly",true);
		$("#start").attr("readonly",true);
		$("#start").attr("value","");
		$("#end").attr("readonly",true);
		$("#end").attr("value","");
		$("#delBEObject").attr("disabled",true);
		$(".secondacolonna .modules label").addClass("fixedobject").attr("title","fixed object");
		
{/literal}{/if}{literal}

{/literal}{if ($object.mail_status == "pending")}{literal}
		
		$(".secondacolonna .modules label").addClass("pending").attr("title","pending invoice");
		
{/literal}{elseif ($object.mail_status == "unsent")}{literal}

		$(".secondacolonna .modules label").addClass("unsent").attr("title","unsent message");
			
{/literal}{/if}{literal}


/*
	check sulle modifiche non salvate
*/
	
	$("#updateForm *").change(function () {

		$(".secondacolonna .modules label").addClass("save").attr("title","unsaved object").attr("rel","");

	});




});
{/literal}
</script>


