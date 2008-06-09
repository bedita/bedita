{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>


{if (!empty($method)) && $method eq "index"}

		<div class="insidecol publishingtree">
			
				{$beTree->view($tree)}
			
		</div>

{/if}


		<hr />
		
		<ul class="insidecol">
			<li><a href="{$html->url('/documents')}">{t}Documents{/t}</a></li>

		{if $module_modify eq '1'}

			<li><a href="{$html->url('/documents/view')}">{t}New Document{/t}</a></li>

		{/if}

		</ul>

		{if !empty($previews)}
		<div class="insidecol"><label>{t}Previews{/t}</label></div>
		<ul class="insidecol">
		{foreach from=$previews item="preview"}<li><a href="{$preview.url}" target="_blank">{$preview.desc}</a></li>{/foreach}
		</ul>
		{/if}

	
	



<div id="handlerChangeAlert"></div>
	

</div>

