{assign var='method' value=$method|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
		
		{include file="../common_inc/messages.tpl"}

		<ul class="insidecol">
			
			<li {if $method eq 'index'}class="on"{/if}>{$tr->link('Multimedia', '/multimedia')}</li>	
					
		</ul>

<div id="handlerChangeAlert"></div>

</div>




