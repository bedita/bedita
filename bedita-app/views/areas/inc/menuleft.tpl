{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
		
	{include file="../common_inc/messages.tpl"}


	<div class="insidecol publishingtree">
		
			{$beTree->view($tree)}

	
	</div>

		<br /><br /><br /><br /><br /><br /><br /><br />
		
		<ul class="menuleft insidecol">

			<li {if $method eq 'index'}class="on"{/if}>{$tr->link('Publishing Tree', '/areas')}</li>
			<li {if $method eq 'hyper'}class="on"{/if}>{$tr->link('Publishing HyperTree', '/areas?hyper=1')}</li>
			{if $module_modify eq '1'}
				<li {if $method eq 'viewArea'}class="on"{/if}>{$tr->link('New Publishing', '/areas/viewArea')}</li>
				<li {if $method eq 'viewSection'}class="on"{/if}>{$tr->link('New Section', '/areas/viewSection')}</li>
			{/if}
					
		</ul>



</div>





