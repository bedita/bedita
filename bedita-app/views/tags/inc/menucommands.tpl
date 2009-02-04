{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="{$moduleName}" rel="{$html->url('/')}{$currentModule.path}">{t}{$currentModule.label}{/t}</label>
	</div>
	
	
	{if $method == "view" && $module_modify eq '1'}
	<div class="insidecol">
		
		<input type="submit" value=" {t}Save{/t} " name="save"/>	
		<input type="button" name="delete" id="delBEObject" value="{t}Delete{/t}"  {if !($tag.id|default:false)}disabled="1"{/if}/>


	</div>
	
	{/if}

</div>