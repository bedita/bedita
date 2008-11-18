{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>

	
		{include file="../common_inc/messages.tpl"}
	
		<ul class="menuleft insidecol">

		{if $module_modify eq '1'}

			<li><a href="{$html->url('/books/view')}">{t}New Book{/t}</a></li>

		{/if}

		</ul>

{if (!empty($method)) && $method eq "index"}

		<div class="insidecol publishingtree">
			
				{$beTree->view($tree)}
		
		</div>

{/if}



{if !empty($previews)}

		<div class="insidecol"><label>{t}Previews{/t}</label></div>
		
		<ul class="insidecol">
		{foreach from=$previews item="preview"}
			<li><a href="{$preview.url}" target="_blank">{$preview.desc}</a></li>
		{/foreach}
		</ul>
		
{/if}

	{include file="../common_inc/user_module_perms.tpl"}
		

</div>