{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	<div class="modules">
	   <label class="{$moduleName}" rel="{$html->url('/documents')}">{t}{$currentModule.label}{/t}</label>
	</div> 

	{if !empty($method) && $method != "index"}
	
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
	
		<div class="listobjnav">
			<a title="next" href="#">
				‹
			</a>
	
			<a title="next" href="#">
				›
			</a> 
		</div>
		
	</div>
	
	{/if}

</div>
