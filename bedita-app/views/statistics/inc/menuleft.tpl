{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''|escape}</label></div>

	{if (!empty($view->action)) && $view->action eq "index" && !empty($tree)}
	<div class="insidecol publishingtree">
		{assign_associative var="options" treeParams=['action' => 'index']}
		{$view->element('tree', $options)}
	</div>
	{/if}


{$view->element('user_module_perms')}

</div>