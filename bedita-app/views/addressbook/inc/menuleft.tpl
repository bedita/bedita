{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

		<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

		

		<ul class="menuleft insidecol">

		{if $module_modify eq '1'}

			<li><a href="{$html->url('/')}{$currentModule.url}/view">{t}New card{/t}</a></li>
			<li><a href="{$html->url('/')}{$currentModule.url}/categories">{t}Categories{/t}</a></li>

		{/if}
			
		</ul>

{$view->element('export')}

{if (!empty($view->action)) && $view->action eq "index"}

		<div class="insidecol publishingtree">
			
			{$view->element('tree')}
		
		</div>

{/if}

{$view->element('user_module_perms')}
		
</div>