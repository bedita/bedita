{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
		
		
	<ul class="insidecol">
		<li><a href="{$html->url('/comments')}">{t}Comments{/t}</a></li>					
	</ul>


{if (!empty($method)) && $method eq "index"}

	<div class="insidecol">
	{$beTree->tree("tree", $tree)}
	</div>

{/if}
	

	<div id="handlerChangeAlert"></div>
	

</div>

