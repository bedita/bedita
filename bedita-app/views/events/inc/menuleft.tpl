{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

{assign var="moduleurl" value=$currentModule.url}

	<ul class="menuleft insidecol bordered">
		{if $view->viewVars.module_modify eq '1'}
		<li><a href="{$html->url('/')}{$currentModule.url}/view">{t}Create new{/t}</a></li>
		{/if}
		<li {if $view->action eq 'index'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/">{t}List {$currentModule.name}{/t}</a>
		</li>
		{bedev}
		<li {if $view->action eq 'calendar'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/calendar">{t}Calendar view{/t}</a>
		</li>
		{/bedev}
		{if !empty($view->action) && ($view->action == "index")}
			{$view->element('select_categories')}
		{/if}
		<li {if $view->action eq 'categories'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/categories">{t}Manage categories{/t}</a>
		</li>
		{$view->element('export')}	
	</ul>


	{if !empty($view->action) && ($view->action == "index" or $view->action == "calendar")}
	<div class="insidecol publishingtree">
		{$view->element('tree')}
	</div>
	{/if}

{$view->element('user_module_perms')}

</div>