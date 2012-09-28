{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="primacolonna">
	
	<div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
		
	<ul class="insidecol">
			<li {if $method eq 'index'}class="on"{/if}>{$this->Tr->link('Tags', '/tags')}</li>
		{if $module_modify eq '1'}
			<li {if $method eq 'view'}class="on"{/if}>{$this->Tr->link('New Tag', '/tags/view')}</li>
		{/if}
									
	</ul>


<div id="handlerChangeAlert"></div>

</div>


