{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}
{assign var='method' value=$method|default:'index'}
<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onClick="window.location='{$html->url('/documents')}'" class="documenti"><a href="{$html->url('/documents')}">{t}Documents{/t}</a></h1>
		<div class="inside">
			<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
				<li {if $method eq 'index'}class="on"{/if}>		<b>&#8250;</b> {$tr->link('Documents', '/documents')}</li>
				<li {if $method eq 'viewArea'}class="on"{/if}>	<b>&#8250;</b> {$tr->link('Add Documents', '/documents/view')}</li>
			</ul>	
			<hr/>
		</div>
	</div>
	<hr/>
	<div id="handlerChangeAlert">
	</div>
	<br/>
	<br/>
	<br/>
</div>