{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
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

	{if empty($categories)}

	{if !empty($view->action) && $view->action != "index" && $view->action != "categories"}
		<div class="insidecol">
			<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
			<input class="bemaincommands" type="button" value=" {t}Publish{/t} " style="display:none" name="publish" id="publishBEObject" />
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
		</div>
		
		{$view->element('prevnext')}
		
	{/if}

	{/if}

</div>

