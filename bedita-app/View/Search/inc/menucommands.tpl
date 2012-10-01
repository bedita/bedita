{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="secondacolonna {if $fixed}fixed{/if}">
	
	<div class="modules">
	   <label class="tags" rel="{$this->Html->url('/tags')}">{t}Tags{/t}</label>
	</div> 
	
	
	{include file="../messages.tpl"}
	
	{if $method == "view" && $module_modify eq '1'}
	<div class="insidecol">
		
		<input class="submit" type="submit" value=" {t}Save{/t} " name="save"/>	
		<input type="button" name="delete" id="delBEObject" class="submit" value="{t}Delete{/t}" 
		{if !($object.id|default:false)}disabled="1"{/if}/>


	</div>
	
	{/if}

</div>