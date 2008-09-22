{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">


	<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>


	{include file="../common_inc/messages.tpl"}

	<ul class="menuleft insidecol">
	
	{if $module_modify eq '1'}

		<li><a href="{$html->url('/documents/view')}">{t}Create new document{/t}</a></li>

	{/if}


	</ul>

{if !empty($object)}
	<ul class="menuleft insidecol">
		<li>
			<a href="javascript:void(0)" onClick="$('#export').slideToggle();">Export document</a>
			<ul id="export" style="display:none;">
				<li><a href="">xml openDoc</a></li>
				<li><a href="">xml BEdita</a></li>
				<li><a href="">rtf</a></li>
				<li><a href="">PDF</a></li>
				<li><a href="">html</a></li>
			</ul>
		
		</li>
	</ul>
{/if}


{if !empty($previews)}
	<ul class="menuleft insidecol">
		<li>
			<a href="javascript:void(0)" onClick="$('#previews').slideToggle();">{t}Previews{/t}</a>
			<ul id="previews" style="display:none;">
			{foreach from=$previews item="preview"}
				<li><a href="{$preview.url}" target="_blank">{$preview.desc}</a></li>
			{/foreach}
			</ul>
	</ul>
{/if}


{if (!empty($method)) && $method eq "index"}
	<div class="insidecol publishingtree">
		
			{$beTree->view($tree)}
	
	</div>
{/if}




	<div id="handlerChangeAlert"></div>
	

</div>