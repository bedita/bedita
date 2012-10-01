{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
usare
{$this->Session->read("backFromView")}
oppure
{$currentModule.url}
a seconda del metodo
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	{if !empty($view->action) && $view->action != "index" && $view->action != "categories"}
		{assign var="back" value=$this->Session->read("backFromView")}
	{else}
		{assign_concat var="back" 1=$this->Html->url('/') 2=$currentModule.url}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{if !empty($view->action) && $view->action != "index" && $view->action != "categories"}
	
	<div class="insidecol">
				
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}Publish{/t} " style="display:none" name="publish" id="publishBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />		
		<!-- <input class="bemaincommands" type="button" value="{t}undo{/t}" name="cancel" id="cancelBEObject" /> -->
		
		{$view->element('prevnext')}
		
	</div>
	
	{/if}
	{if !empty($view->action) && $view->action == "index"}
		{$view->element('select_categories')}
	{/if}
</div>
