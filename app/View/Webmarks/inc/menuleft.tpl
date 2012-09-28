{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}
{$view->set('method', $method)}
<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	

	<ul class="menuleft insidecol">
	{if $module_modify eq '1'}

			<li><a href="{$this->Html->url('/')}{$currentModule.url}/view">{t}New link{/t}</a></li>
			<li><a href="{$this->Html->url('/')}{$currentModule.url}/categories">{t}Categories{/t}</a></li>
	{/if}
	</ul>

{$view->element('export')}

{if (!empty($method)) && $method eq "index"}
	<div class="insidecol publishingtree">
		
		{$view->element('tree')}
	
	</div>
{/if}

{$view->element('user_module_perms')}

</div>