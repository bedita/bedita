{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
		<label class="{$moduleName}" rel="{$html->url('/')}{$session->read("backFromView")}">{t}{$currentModule.label}{/t}</label>
	</div> 

	{if empty($categories)}

	{if !empty($method) && $method != "index"}
		<div class="insidecol">
			<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
		</div>
		
		{include file="../common_inc/prevnext.tpl"}
		
	{/if}

	{/if}

</div>

