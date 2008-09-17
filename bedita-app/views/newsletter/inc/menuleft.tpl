{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>

	
		{include file="../common_inc/messages.tpl"}
	
		<ul class="menuleft insidecol">

			<li {if $method eq "newsletters"}class="on"{/if}>
				<a href="{$html->url('/newsletter/newsletters')}">Manage Newsletters</a>
			</li>
			{if $method eq "newsletters"}
				<ul>
					<li>pubblicazione uno</li>
					<li>pubblic azione 2</li>
					<li>pu blic azione III</li>
					<li>Quarta pubblicazione</li>
				</ul>
			{/if}
			<li {if $method eq "view"}class="on"{/if}><a href="{$html->url('/newsletter/view')}">Create new</a></li>
			<li {if $method eq "templates"}class="on"{/if}><a href="{$html->url('/newsletter/templates')}">Templates</a></li>
		
		</ul>
		
		<ul class="menuleft insidecol">

			<li {if $method eq "subscribers"}class="on"{/if}>
				<a href="{$html->url('/newsletter/subscribers')}">Manage Subscribers</a>
			</li>
			{if $method eq "subscribers"}
				{*<ul>	
					<li {if $group == 1}class="on"{/if}><a href="{$html->url('/newsletter/subscribers/1')}">gruppo uno</a></li>
					<li {if $group == 2}class="on"{/if}><a href="{$html->url('/newsletter/subscribers/2')}">gruppo azione 2</a></li>
					<li {if $group == 3}class="on"{/if}><a href="{$html->url('/newsletter/subscribers/3')}">group II</a></li>
					<li {if $group == 4}class="on"{/if}><a href="{$html->url('/newsletter/subscribers/4')}">Quarto gruppo</a></li>
				</ul>*}
				{/if}
			<li {if $method eq "importsubscribers"}class="on"{/if}>
				<a href="{$html->url('/newsletter/importsubscribers')}">Import email</a>
			</li>
			<li {if $method eq "groups"}class="on"{/if}><a href="{$html->url('/newsletter/groups')}">Recipient Groups</a></li>


		</ul>
		
		<ul class="menuleft insidecol">

			<li><a href="{$html->url('/newsletter/invoices')}">Manage invoices</a></li>
			<ul>
				<li><a href="{$html->url('/newsletter/invoices')}">queued jobs</a></li>
				<li><a href="{$html->url('/newsletter/invoices')}">ended jobs</a></li>
				<li><a href="{$html->url('/newsletter/invoices')}">bounced</a></li>
			</ul>	
		</ul>



{if !empty($previews)}

		<div class="insidecol"><label>{t}Previews{/t}</label></div>
		
		<ul class="insidecol">
		{foreach from=$previews item="preview"}
			<li><a href="{$preview.url}" target="_blank">{$preview.desc}</a></li>
		{/foreach}
		</ul>
		
{/if}

		<div id="handlerChangeAlert"></div>
		

</div>