{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="events" rel="{$html->url('/events')}">{t}Events{/t}</label>
	</div> 
	
	
	
	{if $method == "view" && $module_modify eq '1'}
	<div class="insidecol">
		
		<input class="bemaincommands" type="submit" value=" {t}Save{/t} " name="save"/>	
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" 
		{if !($object.id|default:false)}disabled="1"{/if}/>

	</div>
	
	{/if}



</div>