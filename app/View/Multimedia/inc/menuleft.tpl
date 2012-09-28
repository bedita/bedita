{assign var='method' value=$method|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
		
		

		<ul class="menuleft insidecol">
			
			<li><a href="{$this->Html->url('/multimedia/view')}">{t}Add new item{/t}</a></li>	
					
		</ul>

{$view->element('export')}

	{if (!empty($view->action)) && $view->action eq "index"}
	<div class="insidecol publishingtree">

		{$view->element('tree')}

	</div>
	{/if}

{$view->element('user_module_perms')}

</div>




