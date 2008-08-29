{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>

	
		{include file="../common_inc/messages.tpl"}
	
		<ul class="menuleft insidecol">



			<li>
				<b>{t}Manage Newsletters{/t}</b>
				<ul>
					<li><a href="{$html->url('/newsletter/allnewsletters')}">View all newsletters</a></li>
					<li><a href="{$html->url('/newsletter/view')}">Create new</a></li>
					<li><a href="{$html->url('/newsletter/templates')}">Templates</a></li>
				</ul>
			</li>


		</ul>
		<hr />
		<ul class="menuleft insidecol">
			<li>
				<b>{t}Manage Subscribers{/t}</b>
				<ul>
					<li><a href="{$html->url('/newsletter/allsubscribers')}">View all Subscribers</a></li>
					<li>Recipient Groups</li>
					<ul>
						<li><a href="{$html->url('/newsletter/viewgroup/12')}">gruppo uno</a></li>
						<li><a href="{$html->url('/newsletter/viewgroup/13')}">gruppo 2</a></li>
						<li><a href="{$html->url('/newsletter/viewgroup/14')}">gruppo III</a></li>
					</ul>

				</ul>
			</li>

		</ul>
		<hr />
		<ul class="menuleft insidecol">
			<li>
				<a href="{$html->url('/newsletter/invoices')}"><b>{t}Manage invoices{/t}</b></a>
				<ul>
					<li>queued jobs</li>
					<li>ended jobs</li>
					<li>all jobs</li>
					<li>bounced</li>
				</ul>
			</li>

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