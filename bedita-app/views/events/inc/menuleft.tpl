{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	{$view->element('messages')}

	<ul class="menuleft insidecol">
	{if $module_modify eq '1'}
		<li {if $view->action eq 'index'}class="on"{/if}>{$tr->link('Events', '/events')}</li>
		<li {if $view->action eq 'categories'}class="on"{/if}>{$tr->link('Categories', '/events/categories')}</li>
		<li><a href="{$html->url('/')}{$currentModule.path}/view">{t}Create new event{/t}</a></li>

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