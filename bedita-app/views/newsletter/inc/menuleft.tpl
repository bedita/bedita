
	
<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>

	
		{include file="../common_inc/messages.tpl"}
	
		<ul class="menuleft insidecol">

			<li {if $method eq "newsletters"}class="on"{/if}>
				<a href="{$html->url('/newsletter/newsletters')}">Manage newsletters</a>
			</li>
			<li {if $method eq "view"}class="on"{/if}><a href="{$html->url('/newsletter/view')}">Create new</a></li>
			<li><a href="{$html->url('/newsletter/mailGroups')}">{t}Lists{/t}</a></li>
		</ul>
		
		<ul class="menuleft insidecol">	
			<li {if $method eq "templates"}class="on"{/if}><a href="{$html->url('/newsletter/templates')}">Manage templates</a></li>
		
		</ul>
		

		
		<ul class="menuleft insidecol">

			<li {if $method eq "invoices"}class="on"{/if}><a href="{$html->url('/newsletter/invoices')}">Manage invoices</a></li>
	
		</ul>




		<div id="handlerChangeAlert"></div>
		

</div>