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

{assign var=branch value=$html->params.named.branch|default:''}

<script type="text/javascript">

$(document).ready(function(){
	
	/**
	 * serialized form data used on change page to check if any not saved data is present
	 * get all form fields without class ignore
	 * and/or without parent with class ignore
	 * and exclude also richtext items (check done calling onChangeHandler() function)
	 */
	var formFieldToCheckSelector = "form#updateForm :input[class!='ignore']:not('[class^=mce]'):not('[class^=richtext]'):not(.ignore :input)";
	var formFieldToCheckData = jQuery.trim($(formFieldToCheckSelector).serialize());
	
	{if !empty($branch)}
		// se passato branch apre con quel ramo checked
		$('input[value="{$branch}"]').prop("checked", true);
		
		$('option[value="{$branch}"]').prop("selected", true);
		
		$("#whereto").prev(".tab").BEtabsopen();
	{/if}

	
	/* questo before serve per i concurrenteditors */
	$(".secondacolonna .insidecol #saveBEObject").before("<div id='concurrenteditors'></div>");
	
	$("#publishBEObject").hide().click(function() {
			$("input[name='data[status]']").val("on");
			$("#saveBEObject").click();

	});
	
	function checkStatus() {
		var objstatus = $("input[name='data[status]']:checked").val();
		if (objstatus == "draft") { $("#saveBEObject").val("{t}Save draft{/t}"); $("#publishBEObject").show(); }
		if (objstatus == "off") { $("#saveBEObject").val("{t}Save off{/t}"); $("#publishBEObject").show(); }
		if (objstatus == "on") { $("#saveBEObject").val("{t}Save{/t}"); $("#publishBEObject").hide(); }
	}
	
	checkStatus();

	$("input[name='data[status]']").click(function() {
		checkStatus();
	});


	$("#cancelBEObject").hide().click(function() {
		//if(confirm("{t}Are you sure you want to cancel and reload this document? All unsaved changes will be lost{/t}")) {
			window.location.reload();
		//};
	});
	
	$("#delBEObject").submitConfirm({
		
		action: "{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}",
		message: "{t}Are you sure that you want to delete the item?{/t}",
		formId: "updateForm"
		
	});
	

	window.onbeforeunload = function () {
		var submitFormFieldSerialized = jQuery.trim($(formFieldToCheckSelector).serialize());
		if (!$(".secondacolonna .modules label").hasClass("save") && formFieldToCheckData !== submitFormFieldSerialized) {
			$(".secondacolonna .modules label").addClass("save");
		}
		if ( !$(".secondacolonna .modules label").hasClass("submitForm") && $(".secondacolonna .modules label").hasClass("save")) {
			return "{t}All unsaved changes will be lost{/t}";
		}
	};

	$("#updateForm").submit(function() {
		$(".secondacolonna .modules label").addClass("submitForm");
	});

{if (in_array($currObjectTypeId|default:0, $conf->objectTypes.tree.id))}

	$("div.insidecol input[name='save']").click(function() {
			
		if ( $('#concurrenteditors #editorsList').children().size() > 0 ) {
			var answer = confirm("{t}More users are editing this object. Continue?{/t}")
			    if (answer){
			       $("#updateForm").submit();
			    }
    		return false;  
		}
		else if ( $('.publishingtree input:checked').val() === undefined ) {	
			var answer = confirm("{t}This content is not on publication tree. Continue?{/t}")
			    if (answer){
			       $("#updateForm").submit();
			    }
    		return false;  

		} else {
			$("#updateForm").submit();
		}
	});

{else}
	
	$("div.insidecol input[name='save']").click(function() {
		$("#updateForm").submit();
	});
	
{/if}

		
	$("div.insidecol input[name='clone']").click(function() {
		var cloneTitle=prompt("{t}Title{/t}",$("input[name='data[title]']").val()+"-copy");
		if (cloneTitle) {
			$("input[name='data[title]']").val(cloneTitle);
			$("#updateForm").prop("action","{$html->url('/')}{$submiturl}/cloneObject");
			$("#updateForm").submit();
		}
	});
	
	$.datepicker.setDefaults({
		speed: 'fast', 
		showOn: 'both',
		closeAtTop: false, 
		buttonImageOnly: true, 
	    buttonImage: '{$html->webroot}img/iconCalendar.gif', 
	    buttonText: '{t}Open Calendar{/t}',
	    dateFormat: '{$conf->dateFormatValidation|replace:'yyyy':'yy'}',
		firstDay: 1,
	    beforeShow: customRange
	}, $.datepicker.regional['{$currLang}']);
	
	$("input.dateinput").datepicker();



{if empty($object.id)}

		$("#delBEObject,#cloneBEObject").hide();

{/if}

{if (!empty($module_modify) && ($module_modify != 1))}
		
		$("#saveBEObject,#delBEObject,#publishBEObject").prop("disabled", true);
		$(".secondacolonna .modules label").addClass("readonly").prop("title", "readonly object");

{/if}


{if !empty($object.Permission)}

		$(".secondacolonna .modules label").addClass("lock").prop("title", "object with limited permissions");
	
{/if}

{if !empty($parents.1) || 
	(!empty($object) && in_array($object.object_type_id, $conf->objectTypes.multimedia.id) && $object.relations.attach|default:[]|@count > 1)}

		$(".secondacolonna .modules").after("<div class='subwarning ubiquity'>{t}Ubiquitous object{/t}</div>");
	
{/if}


{*  {if !($perms->isWritable($user.userid,$user.groups,$object.Permission))}
		//$("#delBEObject").prop("disabled", true);
		//$("#saveBEObject,#cloneBEObject,#delBEObject").prop("disabled", true);
		//$(".secondacolonna .modules label").addClass("readonly").prop("title", "readonly object");
	
{/if}*}


{if !empty($object.start_date) && ($object.start_date > ($smarty.now|date_format:"%Y-%m-%d %T"))}
		
		$(".secondacolonna .modules label").addClass("future").prop("title", "object scheduled in the future");

{/if}

{if !empty($object.end_date) && ($object.end_date < ($smarty.now|date_format:"%Y-%m-%d %T"))}
		
		$(".secondacolonna .modules label").addClass("expired").prop("title","expired object");

{/if}


{if !empty($object.start_date) && ($object.start_date|date_format:"%Y-%m-%d" == ($smarty.now|date_format:"%Y-%m-%d"))}
		
		$(".secondacolonna .modules label").addClass("today").prop("title","object scheduled to start today");

{/if}

{if !empty($object.end_date) && ($object.end_date|date_format:"%Y-%m-%d" == ($smarty.now|date_format:"%Y-%m-%d"))}
		
		$(".secondacolonna .modules label").addClass("today").prop("title", "object scheduled to end today");

{/if}


{if !empty($object.fixed) && ($object.fixed == 1)}

		$("#nicknameBEObject,#start,#end").prop("readonly", true);
		$("#status input").prop("readonly", true);
		$("#delBEObject").prop("disabled", true);
		$("#areaSectionAssoc").prop("disabled", true);
		$(".secondacolonna .modules label").addClass("fixedobject").prop("title", "fixed object");
		
{/if}

{if ($object.mail_status|default:'' == "sent")}

		$(".secondacolonna .modules label").addClass("sent").prop("title", "sent message");
		
{elseif ($object.mail_status|default:'' == "injob")}
		
		$(".secondacolonna .modules label").addClass("injob").prop("title", "in job");

		//un'ora prima dell'invio avverte 
{elseif ( (!empty($object.start_sending)) && ($object.start_sending < ($smarty.now+3600|date_format:"%Y-%m-%d %T")) )}
		
		$(".secondacolonna .modules label").addClass("pendingAlert").prop("title", "shortly scheduled invoice");
		{if $object.start_sending > ($smarty.now|date_format:"%Y-%m-%d %T")}
		alert('Attenzione! La newsletter sta per essere inviata oggi\nalle {$object.start_sending|date_format:'%H:%M'}\nogni modifica che fai potrebbe non essere applicata se non salvi in tempo');
		{/if}
	
{elseif ($object.mail_status|default:'' == "pending")}
		
		$(".secondacolonna .modules label").addClass("pending").prop("title", "pending invoice");
	
{elseif ($object.mail_status|default:'' == "unsent")}

		$(".secondacolonna .modules label").addClass("unsent").prop("title", "unsent message");

{elseif ($object.status|default:'' == "draft")}

		$(".secondacolonna .modules label").addClass("draft").prop("title", "draft message");
		$(".head H1").css("color","#666");

							
{/if}



/*
	check on not saved modify
*/
	$(formFieldToCheckSelector).change(function () {
		
		
		
		$(".secondacolonna .modules label").addClass("save").prop("title", "unsaved object");
		$("#cancelBEObject").show();
		{if $autosave|default:false}
		if (autoSaveTimer == undefined || ( $(this).prop("name") == "data[status]" && autoSaveTimer !== false ) ){
			autoSave();
		}
		{/if}
	});

{if !empty($object.id)}
	updateEditors();
{/if}

	status = $("input[name=data\\[status\\]]:checked").val();
	if (status == "on") {
		switchAutosave('off', false);
	}
	
});

function onChangeHandler() {
	$(".secondacolonna .modules label").addClass("save").prop("title", "unsaved object");
	$("#cancelBEObject").show();
	{if $autosave|default:false}
	if (autoSaveTimer == undefined || ( $(this).prop("name") == "data[status]" && autoSaveTimer !== false ) ){
		autoSave();
	}
	{/if}
}

var status;
var autoSaveTimer; //Set to false to turn off autosave.

function autoSave() {
	
	var submitUrl = "{$html->url('/')}{$view->params.controller}/autosave/";
	

	var optionsForm = { target: '#messagesDiv' };

	var newStatus = $("input[name=data\\[status\\]]:checked").val();

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
			{if !empty($conf->richtexteditor.name) && $conf->richtexteditor.name == "tinymce"}
				tinyMCE.triggerSave(true, true);
			{/if}
			$('#updateForm').ajaxSubmit(optionsForm);
		}
		switchAutosave('enable', false);
	}

}

function switchAutosave(action, triggerMsg) {
	
	var checkTime = {$conf->autoSaveTime};
	var submitUrl = "{$html->url('/pages/showAjaxMessage/')}";
	

	if (checkTime <= 0 || (autoSaveTimer === false && action != "on")) {
		action = "off";
		triggerMsg = false;
	}

	switch (action) {
		case "disable":
			clearTimeout(autoSaveTimer);
			autoSaveTimer = undefined;
			var message = '{t}Autosave disabled{/t}';
			break;
		case "enable":
		case "on":
			autoSaveTimer = setTimeout(autoSave,checkTime);
			var message = '{t}Autosave enabled{/t}';
			break;
		case "off":
			clearTimeout(autoSaveTimer);
			autoSaveTimer = false;
			var message = '{t}Autosave turned off{/t}';
			break;
	}
	if (triggerMsg !== false) {
		$("#messagesDiv").load(submitUrl,{ msg:message,type:'info' });
	}
}

function updateEditors() {
	
	var checkTime = {$conf->concurrentCheckTime};
	var submitUrl = "{$html->url('/pages/updateEditor/')}"+"{$object.id|default:''}";
	
	
	$("#concurrenteditors").load(submitUrl);
	chatTimer=setTimeout(updateEditors,checkTime);	
}



</script>