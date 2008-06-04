{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
		
{*		
		<ul class="insidecol">

			<li {if $method eq 'index'}class="on"{/if}>{$tr->link('Areas Tree', '/areas')}</li>
			{if $module_modify eq '1'}
				<li {if $method eq 'viewArea'}class="on"{/if}>{$tr->link('New Area', '/areas/viewArea')}</li>
				<li {if $method eq 'viewSection'}class="on"{/if}>{$tr->link('New Section', '/areas/viewSection')}</li>
			{/if}
					
		</ul>


		<div id="handlerChangeAlert"></div>
*}
</div>





