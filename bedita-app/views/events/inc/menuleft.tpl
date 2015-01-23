{* Left menu, all controller pages *}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''}</label></div>

{assign var="moduleurl" value=$currentModule.url}

	<ul class="menuleft insidecol bordered">
		{if $view->viewVars.module_modify eq '1'}
		<li><a href="{$html->url('/')}{$currentModule.url}/view">{t}Create new{/t}</a></li>
		{/if}
		<li {if $view->action eq 'index'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/">{t}List {$currentModule.name}{/t}</a>
		</li>
		<li {if $view->action eq 'calendar'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/calendar">{t}Calendar view{/t}</a>
		</li>
		{if !empty($view->action) && ($view->action == "index")}
			{$view->element('select_categories')}
		{/if}
		<li {if $view->action eq 'categories'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/categories">{t}Manage categories{/t}</a>
		</li>
		{$view->element('export')}	
	</ul>


	{if !empty($view->action) && ($view->action == "index")}
	<div class="insidecol publishingtree">
		{assign_associative var="options" treeParams=['action' => 'index']}
		{$view->element('tree', $options)}
	</div>
	{/if}

{$view->element('user_module_perms')}

</div>
