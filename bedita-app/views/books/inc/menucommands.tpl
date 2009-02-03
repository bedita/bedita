{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	<div class="modules">
	   <label class="{$moduleName}" rel="{$session->read("backFromView")}">{t}{$currentModule.label}{/t}</label>
	</div> 

	{if !empty($method) && $method != "index"}
	
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />

		{include file="../common_inc/prevnext.tpl"}
		
	</div>
	
	{/if}

</div>