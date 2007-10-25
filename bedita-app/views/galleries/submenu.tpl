<div id="menuLeftPage">
	<div class="menuLeft">
		<h1 onclick="window.location='{$html->url('/galleries')}'" class="gallery"><a href="{$html->url('/galleries')}">{t}Galleries{/t}</a></h1>
		<div class="inside">
			<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
				<li {if $method eq 'index'}class="on"{/if}> <b>&#8250;</b> {$tr->link('Galleries', '/galleries')}</li>
				<li {if $method eq 'view'}class="on"{/if}> <b>&#8250;</b> {$tr->link('New Gallery', '/galleries/view')}</li>
			</ul>
			<hr/>
		</div>
	</div>
	<hr/>
	<br/>
	<br/>
	<div id="handlerChangeAlert"></div>
	<br/>
</div>