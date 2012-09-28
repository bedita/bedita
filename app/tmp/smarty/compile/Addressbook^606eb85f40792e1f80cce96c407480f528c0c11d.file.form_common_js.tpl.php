<?php /* Smarty version Smarty-3.1.11, created on 2012-09-11 10:27:13
         compiled from "/home/bato/workspace/github/bedita/bedita-app/views/elements/form_common_js.tpl" */ ?>
<?php /*%%SmartyHeaderCode:184036523504ef5e1375f43-13401249%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '606eb85f40792e1f80cce96c407480f528c0c11d' => 
    array (
      0 => '/home/bato/workspace/github/bedita/bedita-app/views/elements/form_common_js.tpl',
      1 => 1347273764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '184036523504ef5e1375f43-13401249',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'session' => 0,
    'submiturl' => 0,
    'currentModule' => 0,
    'html' => 0,
    'branch' => 0,
    'delparam' => 0,
    'currObjectTypeId' => 0,
    'conf' => 0,
    'currLang' => 0,
    'object' => 0,
    'module_modify' => 0,
    'autosave' => 0,
    'view' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_504ef5e1764630_68440583',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_504ef5e1764630_68440583')) {function content_504ef5e1764630_68440583($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include '/home/bato/workspace/github/bedita/bedita-app/vendors/_smartyPlugins/block.t.php';
if (!is_callable('smarty_modifier_replace')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/bato/workspace/github/bedita/vendors/smarty/libs/plugins/modifier.date_format.php';
?>


<?php $_smarty_tpl->tpl_vars["user"] = new Smarty_variable($_smarty_tpl->tpl_vars['session']->value->read('BEAuthUser'), null, 0);?>
<?php $_smarty_tpl->tpl_vars["submiturl"] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['submiturl']->value)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['currentModule']->value['url'] : $tmp), null, 0);?>

<?php $_smarty_tpl->tpl_vars['branch'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['html']->value->params['named']['branch'])===null||$tmp==='' ? '' : $tmp), null, 0);?>

<script type="text/javascript">

$(document).ready(function(){
	
	
	<?php if (!empty($_smarty_tpl->tpl_vars['branch']->value)){?>
		// se passato branch apre con quel ramo checked
		$('input[value="<?php echo $_smarty_tpl->tpl_vars['branch']->value;?>
"]').attr("checked",true);
		
		$('option[value="<?php echo $_smarty_tpl->tpl_vars['branch']->value;?>
"]').attr("selected",true);
		
		$("#whereto").prev(".tab").BEtabsopen();
	<?php }?>

	
	/* questo before serve per i concurrenteditors */
	$(".secondacolonna .insidecol #saveBEObject").before("<div id='concurrenteditors'></div>");
	
	$("#publishBEObject").hide().click(function() {
			$("input[name='data[status]']").val("on");
			$("#saveBEObject").click();

	});
	
	function checkStatus() {
		var objstatus = $("input[name='data[status]']:checked").val();
		if (objstatus == "draft") { $("#saveBEObject").val("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save draft<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"); $("#publishBEObject").show(); }
		if (objstatus == "off") { $("#saveBEObject").val("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save off<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"); $("#publishBEObject").show(); }
		if (objstatus == "on") { $("#saveBEObject").val("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"); $("#publishBEObject").hide(); }
	}
	
	checkStatus();

	$("input[name='data[status]']").click(function() {
		checkStatus();
	});


	$("#cancelBEObject").hide().click(function() {
		//if(confirm("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure you want to cancel and reload this document? All unsaved changes will be lost<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
")) {
			window.location.reload();
		//};
	});
	
	$("#delBEObject").submitConfirm({
		
		action: "<?php if (!empty($_smarty_tpl->tpl_vars['delparam']->value)){?><?php echo $_smarty_tpl->tpl_vars['html']->value->url($_smarty_tpl->tpl_vars['delparam']->value);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['html']->value->url('delete/');?>
<?php }?>",
		message: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure that you want to delete the item?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
",
		formId: "updateForm"
		
	});
	

	window.onbeforeunload = function () {
		if ( !$(".secondacolonna .modules label").hasClass("submitForm") && $(".secondacolonna .modules label").hasClass("save")) {
			return "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All unsaved changes will be lost<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
		}
	};

	$("#updateForm").submit(function() {
		$(".secondacolonna .modules label").addClass("submitForm");
	});

<?php if ((in_array((($tmp = @$_smarty_tpl->tpl_vars['currObjectTypeId']->value)===null||$tmp==='' ? 0 : $tmp),$_smarty_tpl->tpl_vars['conf']->value->objectTypes['tree']['id']))){?>

	$("div.insidecol input[name='save']").click(function() {
			
		if ( $('#concurrenteditors #editorsList').children().size() > 0 ) {
			var answer = confirm("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
More users are editing this object. Continue?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
")
			    if (answer){
			       $("#updateForm").submit();
			    }
    		return false;  
		}
		else if ( $('.publishingtree input:checked').val() === undefined ) {	
			var answer = confirm("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
This content is not on publication tree. Continue?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
")
			    if (answer){
			       $("#updateForm").submit();
			    }
    		return false;  

		} else {
			$("#updateForm").submit();
		}
	});

<?php }else{ ?>
	
	$("div.insidecol input[name='save']").click(function() {
		$("#updateForm").submit();
	});
	
<?php }?>

		
	$("div.insidecol input[name='clone']").click(function() {
		var cloneTitle=prompt("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
",$("input[name='data[title]']").val()+"-copy");
		if (cloneTitle) {
			$("input[name='data[title]']").attr("value",cloneTitle);
			$("#updateForm").attr("action","<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['submiturl']->value;?>
/cloneObject");
			$("#updateForm").submit();
		}
	});
	
	$.datepicker.setDefaults({
		speed: 'fast', 
		showOn: 'both',
		closeAtTop: false, 
		buttonImageOnly: true, 
	    buttonImage: '<?php echo $_smarty_tpl->tpl_vars['html']->value->webroot;?>
img/iconCalendar.gif', 
	    buttonText: '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Open Calendar<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
',
	    dateFormat: '<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['conf']->value->dateFormatValidation,'yyyy','yy');?>
',
		firstDay: 1,
	    beforeShow: customRange
	}, $.datepicker.regional['<?php echo $_smarty_tpl->tpl_vars['currLang']->value;?>
']);
	
	$("input.dateinput").datepicker();



<?php if (empty($_smarty_tpl->tpl_vars['object']->value['id'])){?>

		$("#delBEObject,#cloneBEObject").hide();

<?php }?>

<?php if ((!empty($_smarty_tpl->tpl_vars['module_modify']->value)&&($_smarty_tpl->tpl_vars['module_modify']->value!=1))){?>
		
		$("#saveBEObject,#delBEObject,#publishBEObject").attr("disabled",true);
		$(".secondacolonna .modules label").addClass("readonly").attr("title","readonly object");

<?php }?>


<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['Permission'])){?>

		$(".secondacolonna .modules label").addClass("lock").attr("title","object with limited permissions");
	
<?php }?>




<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['start_date'])&&($_smarty_tpl->tpl_vars['object']->value['start_date']>(smarty_modifier_date_format(time(),"%Y-%m-%d %T")))){?>
		
		$(".secondacolonna .modules label").addClass("future").attr("title","object scheduled in the future");

<?php }?>

<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['end_date'])&&($_smarty_tpl->tpl_vars['object']->value['end_date']<(smarty_modifier_date_format(time(),"%Y-%m-%d %T")))){?>
		
		$(".secondacolonna .modules label").addClass("expired").attr("title","expired object");

<?php }?>


<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['start_date'])&&(smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['start_date'],"%Y-%m-%d")==(smarty_modifier_date_format(time(),"%Y-%m-%d")))){?>
		
		$(".secondacolonna .modules label").addClass("today").attr("title","object scheduled to start today");

<?php }?>

<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['end_date'])&&(smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['end_date'],"%Y-%m-%d")==(smarty_modifier_date_format(time(),"%Y-%m-%d")))){?>
		
		$(".secondacolonna .modules label").addClass("today").attr("title","object scheduled to end today");

<?php }?>


<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['fixed'])&&($_smarty_tpl->tpl_vars['object']->value['fixed']==1)){?>

		$("#nicknameBEObject,#start,#end").attr("readonly",true);
		$("#status input").attr("readonly",true);
		$("#delBEObject").attr("disabled",true);
		$("#areaSectionAssoc").attr("disabled",true);
		$(".secondacolonna .modules label").addClass("fixedobject").attr("title","fixed object");
		
<?php }?>

<?php if (((($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_status'])===null||$tmp==='' ? '' : $tmp)=="sent")){?>

		$(".secondacolonna .modules label").addClass("sent").attr("title","sent message");
		
<?php }elseif(((($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_status'])===null||$tmp==='' ? '' : $tmp)=="injob")){?>
		
		$(".secondacolonna .modules label").addClass("injob").attr("title","in job");

		//un'ora prima dell'invio avverte 
<?php }elseif(((!empty($_smarty_tpl->tpl_vars['object']->value['start_sending']))&&($_smarty_tpl->tpl_vars['object']->value['start_sending']<(time()+smarty_modifier_date_format(3600,"%Y-%m-%d %T"))))){?>
		
		$(".secondacolonna .modules label").addClass("pendingAlert").attr("title","shortly scheduled invoice");	
		<?php if ($_smarty_tpl->tpl_vars['object']->value['start_sending']>(smarty_modifier_date_format(time(),"%Y-%m-%d %T"))){?>
		alert('Attenzione! La newsletter sta per essere inviata oggi\nalle <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['object']->value['start_sending'],'%H:%M');?>
\nogni modifica che fai potrebbe non essere applicata se non salvi in tempo');
		<?php }?>
	
<?php }elseif(((($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_status'])===null||$tmp==='' ? '' : $tmp)=="pending")){?>
		
		$(".secondacolonna .modules label").addClass("pending").attr("title","pending invoice");
	
<?php }elseif(((($tmp = @$_smarty_tpl->tpl_vars['object']->value['mail_status'])===null||$tmp==='' ? '' : $tmp)=="unsent")){?>

		$(".secondacolonna .modules label").addClass("unsent").attr("title","unsent message");

<?php }elseif(((($tmp = @$_smarty_tpl->tpl_vars['object']->value['status'])===null||$tmp==='' ? '' : $tmp)=="draft")){?>

		$(".secondacolonna .modules label").addClass("draft").attr("title","draft message");
		$(".head H1").css("color","#666");

							
<?php }?>



/*
	check sulle modifiche non salvate e variabile sul submit
*/
	$("#updateForm *").not(".ignore").change(function () {
		
		
		
		$(".secondacolonna .modules label").addClass("save").attr("title","unsaved object");
		$("#cancelBEObject").show();
		<?php if ((($tmp = @$_smarty_tpl->tpl_vars['autosave']->value)===null||$tmp==='' ? false : $tmp)){?>
		if (autoSaveTimer == undefined || ( $(this).attr("name") == "data[status]" && autoSaveTimer !== false ) ){
			autoSave();
		}
		<?php }?>
	});

<?php if (!empty($_smarty_tpl->tpl_vars['object']->value['id'])){?>
	updateEditors();
<?php }?>

	status = $("input[name=data\\[status\\]]:checked").attr('value');
	if (status == "on") {
		switchAutosave('off', false);
	}
	
});

function onChangeHandler(inst) {
	$(".secondacolonna .modules label").addClass("save").attr("title","unsaved object");
	$("#cancelBEObject").show();
	<?php if ((($tmp = @$_smarty_tpl->tpl_vars['autosave']->value)===null||$tmp==='' ? false : $tmp)){?>
	if (autoSaveTimer == undefined || ( $(this).attr("name") == "data[status]" && autoSaveTimer !== false ) ){
		autoSave();
	}
	<?php }?>
}

var status;
var autoSaveTimer; //Set to false to turn off autosave.

function autoSave() {
	
	var submitUrl = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/');?>
<?php echo $_smarty_tpl->tpl_vars['view']->value->params['controller'];?>
/autosave/";
	

	var optionsForm = { target: '#messagesDiv' };

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
	
	var checkTime = <?php echo $_smarty_tpl->tpl_vars['conf']->value->autoSaveTime;?>
;
	var submitUrl = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/showAjaxMessage/');?>
";
	

	if (checkTime <= 0 || (autoSaveTimer === false && action != "on")) {
		action = "off";
		triggerMsg = false;
	}

	switch (action) {
		case "disable":
			clearTimeout(autoSaveTimer);
			autoSaveTimer = undefined;
			var message = '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Autosave disabled<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';
			break;
		case "enable":
		case "on":
			autoSaveTimer = setTimeout(autoSave,checkTime);
			var message = '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Autosave enabled<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';
			break;
		case "off":
			clearTimeout(autoSaveTimer);
			autoSaveTimer = false;
			var message = '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Autosave turned off<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';
			break;
	}
	if (triggerMsg !== false) {
		$("#messagesDiv").load(submitUrl,{ msg:message,type:'info' });
	}
}

function updateEditors() {
	
	var checkTime = <?php echo $_smarty_tpl->tpl_vars['conf']->value->concurrentCheckTime;?>
;
	var submitUrl = "<?php echo $_smarty_tpl->tpl_vars['html']->value->url('/pages/updateEditor/');?>
"+"<?php echo (($tmp = @$_smarty_tpl->tpl_vars['object']->value['id'])===null||$tmp==='' ? '' : $tmp);?>
";
	
	
	$("#concurrenteditors").load(submitUrl);
	chatTimer=setTimeout(updateEditors,checkTime);	
}



</script>

<?php }} ?>