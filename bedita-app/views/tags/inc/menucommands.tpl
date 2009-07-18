{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.path}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{if $method == "view" && $module_modify eq '1'}
	<div class="insidecol">

		<input type="submit" value=" {t}Save{/t} " name="save" />	
		<input type="button" name="delete" id="delBEObject" value="{t}Delete{/t}"  {if !($tag.id|default:false)}disabled="1"{/if}/>

	</div>
	
	{/if}

</div>