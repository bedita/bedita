{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="attachments" rel="{$html->url('/attachments')}">{t}Attachments{/t}</label>
	</div> 
	
{include file="../common_inc/messages.tpl"}
	
	
	{if !empty($method) && $method != "index" && $module_modify eq '1'}
	<div class="insidecol">
		
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />	
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />

	</div>
	
	{/if}


</div>

