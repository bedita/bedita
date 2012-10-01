{$view->set("method", $method)}

<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	
		
	
		<ul class="menuleft insidecol">

			<li {if $method eq "newsletters"}class="on"{/if}>
				<a href="{$this->Html->url('/newsletter/newsletters')}">{t}Newsletters{/t}</a>
			</li>
			
		</ul>
		
		<ul class="menuleft insidecol">
			<li {if $method eq "mailgroups"}class="on"{/if}>
				<a href="{$this->Html->url('/newsletter/mailGroups')}">{t}Subscriber lists{/t}</a>
			</li>
		</ul>
		
		<ul class="menuleft insidecol">

			<li {if $method eq "invoices"}class="on"{/if}><a href="{$this->Html->url('/newsletter/invoices')}">{t}Invoices{/t}</a></li>
	
		</ul>

		<ul class="menuleft insidecol">	
			<li {if $method eq "templates"}class="on"{/if}><a href="{$this->Html->url('/newsletter/templates')}">{t}Templates{/t}</a></li>
		
		</ul>
		
	{$view->element('user_module_perms')}
		

</div>