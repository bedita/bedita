{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
		
		
		<ul class="insidecol">
			<li><a href="{$html->url('/documents')}">{t}Documents{/t}</a></li>
			
		{if $module_modify eq '1'}
					
			<li><a href="{$html->url('/documents/view')}">{t}New Document{/t}</a></li>
					
		{/if}
					
		</ul>




	
	
{if (!empty($method)) && $method eq "index"}

		<div class="insidecol">
		{*include file="../pages/form_tree.tpl"*}
		{$beTree->tree("tree", $tree)}
		</div>

{/if}
	

<div id="handlerChangeAlert"></div>
	

</div>

