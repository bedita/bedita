
{$view->set("method", $method)}
<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($method) && $method != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.url}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 

	
	{if !empty($method) && $method != "index"}
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
	</div>
	
		{$view->element("prevnext")}
	
	{/if}

</div>

