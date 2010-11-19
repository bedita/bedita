{*
** tutta la validazione e i controlli e i comportamenti del form updateform comune vanno QUI
** soprattutto pulire menucommands.tpl / view.tpl / form.tpl
** questo TPL è incluso nelle varie view.tpl degli oggetti e non nelle liste e nelel categorie
** controlli aggiuntivi specifici per il singolo form locale del modulo vanno nel suo module.js
** 
** parametri opzionali ricevuti:
** submiturl = url, default: currentModule.url
**
*}


{assign var="user" value=$session->read('BEAuthUser')}
{assign var="submiturl" value=$submiturl|default:$currentModule.url}

{assign var=branch value=$html->params.named.branch}

<script type="text/javascript">
{literal}
$(document).ready(function(){
	
	{/literal}
	{if !empty($branch)}
		// se passato branch apre con quel ramo checked
		$('input[value="{$branch}"]').attr("checked",true);
		$("#whereto").prev(".tab").BEtabsopen();
			
	{/if}
	{literal}
	
	/* questo before server per i concurrenteditors */
	$(".secondacolonna .insidecol #saveBEObject").before("<div id='concurrenteditors'></div>");
	
	$("#publishBEObject").hide().click(function() {
			$("input[name='data[status]']").val("on");
			$("#saveBEObject").click();

	});
	
	function checkStatus() {
		var objstatus = $("input[name='data[status]']:checked").val();
		if (objstatus == "draft") { $("#saveBEObject").val("{/literal}{t}Save draft{/t}{literal}"); $("#publishBEObject").show(); }
		if (objstatus == "off") { $("#saveBEObject").val("{/literal}{t}Save off{/t}{literal}"); $("#publishBEObject").show(); }
		if (objstatus == "on") { $("#saveBEObject").val("{/literal}{t}Save{/t}{literal}"); $("#publishBEObject").hide(); }
	}
	
	checkStatus();

	$("input[name='data[status]']").click(function() {
		checkStatus();
	});


	$("#cancelBEObject").hide().click(function() {
		//if(confirm("{/literal}{t}Are you sure you want to cancel and reload this document? All unsaved changes will be lost{/t}{literal}")) {
			window.location.reload();
		//};
	});
	
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}",
		message: "{t}Are you sure that you want to delete the item?{/t}",
		formId: "updateForm"
		{literal}
	});
	

	window.onbeforeunload = function () {
		if ( !$(".secondacolonna .modules label").hasClass("submitForm") && $(".secondacolonna .modules label").hasClass("save")) {
			return "{/literal}{t}All unsaved changes will be lost{/t}{literal}";
		}
	};

	$("#updateForm").submit(function() {
		$(".secondacolonna .modules label").addClass("submitForm");
	});

{/literal}{if (@in_array($currObjectTypeId, $conf->objectTypes.tree.id))}{literal}

	$("div.insidecol input[name='save']").click(function() {
			
		if ( $('#concurrenteditors #editorsList').children().size() > 0 ) {
			var answer = confirm("{/literal}{t}More users are editing this object. Continue?{/t}{literal}")
			    if (answer){
			       $("#updateForm").submit();
			    }
    		return false;  
		}
		else if ( $('.publishingtree input:checked').val() === undefined ) {	
			var answer = confirm("{/literal}{t}This content is not on publication tree. Continue?{/t}{literal}")
			    if (answer){
			       $("#updateForm").submit();
			    }
    		return false;  

		} else {
			$("#updateForm").submit();
		}
	});

{/literal}{else}{literal}
	
	$("div.insidecol input[name='save']").click(function() {
		$("#updateForm").submit();
	});
	
{/literal}{/if}{literal}	

		
	$("div.insidecol input[name='clone']").click(function() {
		var cloneTitle=prompt("{/literal}{t}Title{/t}{literal}",$("input[name='data[title]']").val()+"-copy");
		if (cloneTitle) {
			$("input[name='data[title]']").attr("value",cloneTitle);
			$("#updateForm").attr("action","{/literal}{$html->url('/')}{$submiturl}{literal}/cloneObject");
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
		
		$("#saveBEObject,#delBEObject,#publishBEObject").attr("disabled",true);
		$(".secondacolonna .modules label").addClass("readonly").attr("title","readonly object");

{/literal}{/if}{literal}


{/literal}{if !empty($object.Permission)}{literal}

		$(".secondacolonna .modules label").addClass("lock").attr("title","object with limited permissions");
	
{/literal}{/if}{literal}

{/literal}{*  {if !($perms->isWritable($user.userid,$user.groups,$object.Permission))}{literal}
		//$("#delBEObject").attr("disabled",true);
		//$("#saveBEObject,#cloneBEObject,#delBEObject").attr("disabled",true);
		//$(".secondacolonna .modules label").addClass("readonly").attr("title","readonly object");
	
{/literal}{/if}*}{literal}


{/literal}{if !empty($object.start_date) && ($object.start_date > ($smarty.now|date_format:"%Y-%m-%d %T"))}{literal}
		
		$(".secondacolonna .modules label").addClass("future").attr("title","object scheduled in the future");

{/literal}{/if}{literal}

{/literal}{if !empty($object.end_date) && ($object.end_date < ($smarty.now|date_format:"%Y-%m-%d %T"))}{literal}
		
		$(".secondacolonna .modules label").addClass("expired").attr("title","expired object");

{/literal}{/if}{literal}


{/literal}{if !empty($object.start_date) && ($object.start_date|date_format:"%Y-%m-%d" == ($smarty.now|date_format:"%Y-%m-%d"))}{literal}
		
		$(".secondacolonna .modules label").addClass("today").attr("title","object scheduled to start today");

{/literal}{/if}{literal}

{/literal}{if !empty($object.end_date) && ($object.end_date|date_format:"%Y-%m-%d" == ($smarty.now|date_format:"%Y-%m-%d"))}{literal}
		
		$(".secondacolonna .modules label").addClass("today").attr("title","object scheduled to end today");

{/literal}{/if}{literal}


{/literal}{if !empty($object.fixed) && ($object.fixed == 1)}{literal}

		$("#nicknameBEObject,#start,#end").attr("readonly",true);
		$("#status input").attr("readonly",true);
		$("#delBEObject").attr("disabled",true);
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

{/literal}{elseif (@$object.status == "draft")}{literal}

		$(".secondacolonna .modules label").addClass("draft").attr("title","draft message");
		$(".head H1").css("color","#666");

							
{/literal}{/if}{literal}



/*
	check sulle modifiche non salvate e variabile sul submit
*/
	$("#updateForm *").change(function () {
		$(".secondacolonna .modules label").addClass("save").attr("title","unsaved object");
		$("#cancelBEObject").show();
		{/literal}{if $autosave|default:false}{literal}
		if (autoSaveTimer == undefined || ( $(this).attr("name") == "data[status]" && autoSaveTimer !== false ) ){
			autoSave();
		}
		{/literal}{/if}{literal}
	});

{/literal}{if !empty($object.id)}{literal}
	updateEditors();
{/literal}{/if}{literal}

	status = $("input[name=data\\[status\\]]:checked").attr('value');
	if (status == "on") {
		switchAutosave('off', false);
	}
	
});

function onChangeHandler(inst) {
	$(".secondacolonna .modules label").addClass("save").attr("title","unsaved object");
	$("#cancelBEObject").show();
	{/literal}{if $autosave|default:false}{literal}
	if (autoSaveTimer == undefined || ( $(this).attr("name") == "data[status]" && autoSaveTimer !== false ) ){
		autoSave();
	}
	{/literal}{/if}{literal}
}

var status;
var autoSaveTimer; //Set to false to turn off autosave.

function autoSave() {
	{/literal}
	var submitUrl = "{$html->url('/')}{$view->params.controller}/autosave/";
	{literal}

	var optionsForm = {target: '#messagesDiv'};

	var newStatus = $("input[name=data\\[status\\]]:checked").attr('value');

	if (status != newStatus) {
		if (newStatus == 'on') {
			switchAutosave('disable');
		} else {
			if (status == 'on') {
				switchAutosave('enable');
			} else {
				switchAutosave('enable', false);
			}
		}
		status = newStatus;
	} else if (newStatus != 'on') {
		if (autoSaveTimer != undefined) {
			optionsForm.url = submitUrl; // override form action
			tinyMCE.triggerSave(true, true);
			$('#updateForm').ajaxSubmit(optionsForm);
		}
		switchAutosave('enable', false);
	}

}

function switchAutosave(action, triggerMsg) {
	{/literal}
	var checkTime = {$conf->autoSaveTime};
	var submitUrl = "{$html->url('/pages/showAjaxMessage/')}";
	{literal}

	if (checkTime <= 0 || (autoSaveTimer === false && action != "on")) {
		action = "off";
		triggerMsg = false;
	}

	switch (action) {
		case "disable":
			clearTimeout(autoSaveTimer);
			autoSaveTimer = undefined;
			var message = {/literal}'{t}Autosave disabled{/t}'{literal};
			break;
		case "enable":
		case "on":
			autoSaveTimer = setTimeout(autoSave,checkTime);
			var message = {/literal}'{t}Autosave enabled{/t}'{literal};
			break;
		case "off":
			clearTimeout(autoSaveTimer);
			autoSaveTimer = false;
			var message = {/literal}'{t}Autosave turned off{/t}'{literal};
			break;
	}
	if (triggerMsg !== false) {
		$("#messagesDiv").load(submitUrl,{msg:message,type:'info'});
	}
}

function updateEditors() {
	{/literal}
	var checkTime = {$conf->concurrentCheckTime};
	var submitUrl = "{$html->url('/pages/updateEditor/')}"+"{$object.id|default:''}";
	{literal}
	
	$("#concurrenteditors").load(submitUrl);
	chatTimer=setTimeout(updateEditors,checkTime);	
}


{/literal}
</script>

