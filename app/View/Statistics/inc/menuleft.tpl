{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	


	{if (!empty($view->action)) && $view->action eq "index"}
	<div class="insidecol publishingtree">

		{$view->element('tree')}
	
	</div>
	{/if}


{$view->element('user_module_perms')}

</div>