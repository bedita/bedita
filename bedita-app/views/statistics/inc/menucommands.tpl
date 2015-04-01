{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($view->action) && $view->action != "index"}
		{assign var="back" value=$session->read("backFromView")|escape}
	{else}
		{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	
	{if $view->action == "view" && $module_modify eq '1'}
	<div class="insidecol">
		
		<input class="submit" type="submit" value=" {t}Save{/t} " name="save"/>	
		<input type="button" name="delete" id="delBEObject" class="submit" value="{t}Delete{/t}" 
		{if !($tag.id|default:false)}disabled="1"{/if}/>


	</div>
	
	{/if}

</div>