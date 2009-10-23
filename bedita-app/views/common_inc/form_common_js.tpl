{*
** tutta la validazione e i controlli e i comportamenti del form updateform comune vanno QUI
** soprattutto pulire menucommands.tpl / view.tpl / form.tpl
** questo TPL è incluso nelle varie view.tpl degli oggetti e non nelle liste e nelel categorie
** controlli aggiuntivi specifici per il singolo form locale del modulo vanno nel suo module.js
** 
** parametri opzionali ricevuti:
** submiturl = url, default: currentModule.path
**
*}


{assign var="user" value=$session->read('BEAuthUser')}
{assign var="submiturl" value=$submiturl|default:$currentModule.path}


<script type="text/javascript">
{literal}
$(document).ready(function(){
	
	//alert("pop");

	$("#cancelBEObject").hide().click(function() {
		if(confirm("{/literal}{t}Are you sure you want to cancel and reload this document? All unsaved changes will be lost{/t}{literal}")) {
			window.location.reload();
		}
	;
	});
	
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


{/literal}

{if empty($object.id)}{literal}

		$("#delBEObject,#cloneBEObject").hide();

{/literal}{/if}{literal}

{/literal}{if (!empty($module_modify) && ($module_modify != 1))}{literal}
		
		$("#saveBEObject,#delBEObject").attr("disabled",true);
		$(".secondacolonna .modules label").addClass("readonly").attr("title","readonly object");

{/literal}{/if}{literal}

{/literal}{if !empty($object.Permissions)}{literal}

		$(".secondacolonna .modules label").addClass("lock").attr("title","object with limited permissions");

	
{/literal}{/if}{literal}

{/literal}{*  {if !($perms->isWritable($user.userid,$user.groups,$object.Permissions))}{literal}
		//$("#delBEObject").attr("disabled",true);
		//$("#saveBEObject,#cloneBEObject,#delBEObject").attr("disabled",true);
		//$(".secondacolonna .modules label").addClass("readonly").attr("title","readonly object");
	
{/literal}{/if}*}{literal}


{/literal}{if !empty($object.start) && ($object.start > ($smarty.now|date_format:"%Y-%m-%d %T"))}{literal}
		
		$(".secondacolonna .modules label").addClass("future").attr("title","object scheduled in the future");

{/literal}{/if}{literal}

{/literal}{if !empty($object.end) && ($object.end < ($smarty.now|date_format:"%Y-%m-%d %T"))}{literal}
		
		$(".secondacolonna .modules label").addClass("expired").attr("title","expired object");

{/literal}{/if}{literal}


{/literal}{if !empty($object.start) && ($object.start|date_format:"%Y-%m-%d" == ($smarty.now|date_format:"%Y-%m-%d"))}{literal}
		
		$(".secondacolonna .modules label").addClass("today").attr("title","object scheduled to start today");

{/literal}{/if}{literal}

{/literal}{if !empty($object.end) && ($object.end|date_format:"%Y-%m-%d" == ($smarty.now|date_format:"%Y-%m-%d"))}{literal}
		
		$(".secondacolonna .modules label").addClass("today").attr("title","object scheduled to end today");

{/literal}{/if}{literal}


{/literal}{if !empty($object.fixed) && ($object.fixed == 1)}{literal}

		$("#nicknameBEObject,#start,#end").attr("readonly",true);
		$("#status input,#delBEObject").attr("disabled",true);
		$("#areaSectionAssoc").attr("disabled",true);
		$(".secondacolonna .modules label").addClass("fixedobject").attr("title","fixed object");
		
{/literal}{/if}{literal}

{/literal}{if (@$object.mail_status == "sent")}{literal}

		$(".secondacolonna .modules label").addClass("sent").attr("title","sent message");
		
{/literal}{elseif (@$object.mail_status == "injob")}{literal}
		
		$(".secondacolonna .modules label").addClass("injob").attr("title","in job");

		//un'ora prima dell'invio avverte 
{/literal}{elseif ( (!empty($object.start_sending)) && ($object.start_sending < ($smarty.now+3600|date_format:"%Y-%m-%d %T")) )}{literal}
		
		$(".secondacolonna .modules label").addClass("pendingAlert").attr("title","shortly scheduled invoice");	
		{/literal}{if $object.start_sending > ($smarty.now|date_format:"%Y-%m-%d %T") }{literal}
		alert('Attenzione! La newsletter sta per essere inviata oggi\nalle {/literal}{$object.start_sending|date_format:'%H:%M'}{literal}\nogni modifica che fai potrebbe non essere applicata se non salvi in tempo');
		{/literal}{/if}{literal}
	
{/literal}{elseif (@$object.mail_status == "pending")}{literal}
		
		$(".secondacolonna .modules label").addClass("pending").attr("title","pending invoice");
	
{/literal}{elseif (@$object.mail_status == "unsent")}{literal}

		$(".secondacolonna .modules label").addClass("unsent").attr("title","unsent message");
				
{/literal}{/if}{literal}


/*
	check sulle modifiche non salvate
*/
	
	$("#updateForm *").change(function () {

		$(".secondacolonna .modules label").addClass("save").attr("title","unsaved object");
		$("#cancelBEObject").show();
	});




});
{/literal}
</script>


