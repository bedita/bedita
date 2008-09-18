
	
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
				
				{if !empty($groups)}
				<ul>
					{foreach from=$groups item="group"}
					<li {if $group.MailGroup.id == $selected_group_id}class="on"{/if}><a href="{$html->url('/newsletter/subscribers/')}{$group.MailGroup.id}">{$group.MailGroup.group_name}</a></li>
					{/foreach}
				</ul>				
				{/if}
				
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