{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	<div class="modules">
		{strip}
		<label class="{$moduleName}" rel="
		{if !empty($html->params.named.referer)}
			{$html->params.named.referer|replace:'|':'/'}
		{else}
			{$html->url('/')}{$currentModule.path}
		{/if}
		">{t}{$currentModule.label}{/t}</label>
		{/strip}
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
		
		<input class="bemaincommands" style="display:none" type="button" value="{t}cancel{/t}" name="cancel" id="cancelBEObject" />
		
		
	</div>
	
	{/if}


</div>
