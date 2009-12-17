{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
usare
{$session->read("backFromView")}
oppure
{$currentModule.path}
a seconda del metodo
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	{if !empty($view->action) && $view->action != "index" && $view->action != "categories"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.path}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{if !empty($view->action) && $view->action != "index" && $view->action != "categories"}
	
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
	
		{$view->element('prevnext')}

		
	</div>
	
	{/if}

</div>
