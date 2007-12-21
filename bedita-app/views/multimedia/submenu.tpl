{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}
{assign var='method' value=$method|default:'index'}
<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onClick="window.location='{$html->url('/multimedia')}'" class="multimedia"><a href="{$html->url('/multimedia')}">{t}Multimedia{/t}</a></h1>
		<div class="inside">
			{include file="../pages/user_module_perms.tpl"}
			<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
				<li {if $method eq 'index'}class="on"{/if}><b>&#8250;</b> {$tr->link('Multimedia', '/multimedia')}</li>
			</ul>	
		</div>
	</div>
	<hr/>	
	<div id="handlerChangeAlert"></div>
	<br/>
	<br/>
	<br/>
</div>	