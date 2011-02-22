{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

		{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{if !empty($view->action) && $view->action != "index" && $view->action != "categories"}
	<div class="insidecol">

		<input type="submit" value=" {t}Save{/t} " id="saveBEObject"name="save" />	
		<input type="button" name="delete" id="delBEObject" value="{t}Delete{/t}"  {if !($tag.id|default:false)}disabled="1"{/if}/>
	
	</div>
	
	{/if}

</div>