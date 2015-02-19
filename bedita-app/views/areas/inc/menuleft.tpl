{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$view->action|default:'index'}

<div class="primacolonna">
		
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''}</label></div>
		
	
	{if $module_modify eq '1'}
	<ul class="menuleft insidecol">
		<li id="newArea" {if $method eq 'viewArea'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/viewArea">
				{t}new publication{/t}
			</a>
		</li>
		<li id="newSection" {if $method eq 'viewSection'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/viewSection">
				{t}new section{/t}
			</a>
		</li>
		
		<li {if $view->action eq 'categories'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/categories">{t}Manage categories{/t}</a>
		</li>
	</ul>
	{/if}

		
	{if ($method != "viewArea" && $method != "viewSection") && !empty($tree)}
	<div class="insidecol publishingtree">	
		{assign_associative var="options" treeParams=['action' => 'index']}
		{$view->element('tree', $options)}
	</div>
	{/if}	

	{$view->element('user_module_perms')}
	
</div>





