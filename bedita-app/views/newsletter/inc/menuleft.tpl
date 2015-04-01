{$view->set("method", $method)}

<div class="primacolonna">

		<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''|escape}</label></div>

		<ul class="menuleft insidecol bordered">

			<li {if $method eq "newsletters" or $method eq "view"}class="on"{/if}>
				<a href="{$html->url('/newsletter/newsletters')}">{t}Newsletters{/t}</a>
			</li>
			
			<li {if $method eq "mailgroups"}class="on"{/if}>
				<a href="{$html->url('/newsletter/mailGroups')}">{t}Subscriber lists{/t}</a>
			</li>

			<li {if $method eq "invoices"}class="on"{/if}><a href="{$html->url('/newsletter/invoices')}">{t}Invoices{/t}</a></li>
	
			<li {if $method eq "templates"}class="on"{/if}><a href="{$html->url('/newsletter/templates')}">{t}Templates{/t}</a></li>
		
		</ul>
		
	{$view->element('user_module_perms')}
		
</div>