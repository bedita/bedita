{*
Template incluso.
Menu comandi a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna" style="z-index:10">
	
	<div class="modules">
	  	   <label class="{$moduleName}" rel="{$html->url('/')}{$currentModule.path}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{if !empty($method) && ($method == "viewArea" or $method == "viewSection")}
	
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
	</div>
	
	{/if}
</div>


