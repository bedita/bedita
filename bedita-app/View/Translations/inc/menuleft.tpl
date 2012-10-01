{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>


	
	
{*
		<ul class="menuleft insidecol">
			<li><a href="{$this->Html->url('/translations/view')}">{t}New Translation{/t}</a></li>
		</ul>
*}

	{$view->element('user_module_perms')}


</div>
