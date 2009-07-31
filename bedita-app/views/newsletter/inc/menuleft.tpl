
	
<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	
		{include file="../common_inc/messages.tpl"}
	
		<ul class="menuleft insidecol">

			<li {if $method eq "newsletters"}class="on"{/if}>
				<a href="{$html->url('/newsletter/newsletters')}">{t}Newsletters{/t}</a>
			</li>
			
		</ul>
		
		<ul class="menuleft insidecol">
			<li {if $method eq "mailgroups"}class="on"{/if}>
				<a href="{$html->url('/newsletter/mailGroups')}">{t}Subscriber lists{/t}</a>
			</li>
		</ul>
		
		<ul class="menuleft insidecol">

			<li {if $method eq "invoices"}class="on"{/if}><a href="{$html->url('/newsletter/invoices')}">{t}Invoices{/t}</a></li>
	
		</ul>

		<ul class="menuleft insidecol">	
			<li {if $method eq "templates"}class="on"{/if}><a href="{$html->url('/newsletter/templates')}">{t}Templates{/t}</a></li>
		
		</ul>
		
	{include file="../common_inc/user_module_perms.tpl"}
		

</div>