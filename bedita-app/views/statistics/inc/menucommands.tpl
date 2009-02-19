{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($method) && $method != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0="/" 1=$currentModule.path}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	
	{if $method == "view" && $module_modify eq '1'}
	<div class="insidecol">
		
		<input class="submit" type="submit" value=" {t}Save{/t} " name="save"/>	
		<input type="button" name="delete" id="delBEObject" class="submit" value="{t}Delete{/t}" 
		{if !($tag.id|default:false)}disabled="1"{/if}/>


	</div>
	
	{/if}

</div>