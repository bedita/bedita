{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">
	
	<div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	
	
	<ul class="menuleft insidecol">
			<li {if $view->action eq 'index'}class="on"{/if}>{$this->Tr->link('Tags', '/tags')}</li>
		{if $module_modify eq '1'}
			<li {if $view->action eq 'view'}class="on"{/if}>{$this->Tr->link('New tag', '/tags/view')}</li>
		{/if}
									
	</ul>

	{$view->element('user_module_perms')}

</div>


