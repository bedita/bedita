{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{literal}
<script type="text/javascript">
<!--
// variables for jquery.changealert.js
var html = "<span id='_hndVisualAlert'><\/span> \
	<input type='checkbox' id='_hndChkbox'> \
	<a id='_cmdCheck' href='#'>{/literal}{t}Remind{/t}{literal}<\/a> \
	<br/> \
	{/literal}{t}Check if you want be notified, whenever you try to leave the page and you changed some data in the form{/t} ({t}changes would be lost if you leave the page{/t}){literal}.\
	";
var datachanged = "* {/literal}{t}data changed{/t}{literal}<br/>";
var changeAlertMessage = "{/literal}{t}The change will be lost. Do you want to continue{/t}{literal}?" ;
//-->
</script>

<script type="text/javascript">
$(document).ready(function(){
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}",
		message: "{t}Are you sure that you want to delete the item?{/t}"
		{literal}
	});
});
</script>

{/literal}


<div class="secondacolonna {if $fixed}fixed{/if}">
	
	<div class="modules">
	   <label class="galleries" rel="{$html->url('/galleries')}">{t}Galleries{/t}</label>
	</div> 


	{if $method != "index" && $module_modify eq '1'}
	<div class="insidecol">
		
	<input class="bemaincommands" type="submit" value=" {t}Save{/t} " name="save" />	

	<input class="bemaincommands" type="submit" value=" {t}clone{/t} " name="clone" />	
	
	<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if}/>

	</div>

	{/if}

{include file="messages.tpl"}

</div>
	
	
