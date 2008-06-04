{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
		
		
		<ul class="insidecol">
			<li><a href="{$html->url('/galleries')}">{t}Galleries{/t}</a></li>
			
		{if $module_modify eq '1'}
					
			<li><a href="{$html->url('/galleries/view')}">{t}New Gallery{/t}</a></li>
					
		{/if}
					
		</ul>

<div id="handlerChangeAlert"></div>

</div>