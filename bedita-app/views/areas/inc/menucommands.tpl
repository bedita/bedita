{*
Template incluso.
Menu comandi a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna" style="z-index:10">
	
	{if !empty($method) && $method != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.path}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{if !empty($method) && ($method == "viewArea" or $method == "viewSection")}
	
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
	</div>
	
	{/if}
</div>


