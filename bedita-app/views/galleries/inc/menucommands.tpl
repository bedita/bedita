{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<script type="text/javascript">
{literal}
$(document).ready(function(){
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
});
</script>
{/literal}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="{$moduleName}" rel="{$html->url('/galleries')}">{t}{$moduleName}{/t}</label>
	</div> 
	
	
	{if !empty($method) && $method != "index" && $module_modify eq '1'}
	<div class="insidecol">
		
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />	
		<input class="bemaincommands" type="submit" value=" {t}clone{/t} " name="clone" />	
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />

	</div>
	
	{/if}



</div>
	
