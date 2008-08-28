{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>

	
		{include file="../common_inc/messages.tpl"}
	
		<ul class="insidecol">



			<li>
				<b>{t}Manage Newsletters{/t}</b>
				<ul>
					<li><a href="{$html->url('/newsletter/allnewsletters')}">view all newsletters</a></li>
					<li><a href="{$html->url('/newsletter/view')}">Create new</a></li>
				</ul>
			</li>


		</ul>
		<hr />
		<ul class="insidecol">
			<li>
				<a href="{$html->url('/newsletter/lists')}"><b>{t}Manage Subscribers{/t}</b></a>
				<ul>
					<li>All Subscriber</li>
					<li>Subscriber Groups</li>
					<ul>
						<li>gruppo uno</li>
						<li>gruppo 2</li>
						<li>gruppo III</li>
					</ul>

				</ul>
			</li>

		</ul>
		<hr />
		<ul class="insidecol">
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