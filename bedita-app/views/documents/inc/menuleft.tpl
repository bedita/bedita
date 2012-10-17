{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}
<div class="primacolonna">
	
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
	
	<ul class="menuleft insidecol bordered">
		{if $view->viewVars.module_modify eq '1'}
		<li><a href="{$html->url('/')}{$currentModule.url}/view">{t}Create new document{/t}</a></li>
		{/if}
		<li {if $view->action eq 'index'}class="on"{/if}>{$tr->link('List documents', '/documents')}</li>
		{if !empty($view->action) && $view->action == "index"}
			{$view->element('select_categories')}
		{/if}
		<li {if $view->action eq 'categories'}class="on"{/if}>{$tr->link('Manage categories', '/documents/categories')}</li>
		{$view->element('export')}	
	</ul>

	{if !empty($view->action) && $view->action == "index"}
	<div class="insidecol publishingtree">
		{$view->element('tree')}
	</div>
	{/if}
	
	{*if !empty($view->action) && $view->action == "view"}
	<div class="insidecol publishingtree" style="margin-top:60px">
		{assign_associative var="params" checkbox=true}
		{$view->element('tree', $params)}
	</div>
	{/if*}
	
	{$view->element('user_module_perms')}
	
</div>