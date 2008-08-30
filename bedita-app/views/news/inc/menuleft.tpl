{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>

	<ul class="menuleft insidecol">
		<li {if $method eq 'index'}class="on"{/if}>{$tr->link('News', '/news')}</li>
		<li {if $method eq 'categories'}class="on"{/if}>{$tr->link('Categories', '/news/categories')}</li>
		{if $module_modify eq '1'}
		<li><a href="{$html->url('/news/view')}">{t}Create news{/t}</a></li>
		{/if}
	</ul>

{if (!empty($method)) && $method eq "index"}

		<div class="insidecol publishingtree">
			
				{$beTree->view($tree)}
		
		</div>

{/if}

		<div id="handlerChangeAlert"></div>

</div>