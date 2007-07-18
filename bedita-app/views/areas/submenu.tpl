{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}
{assign var='method' value=$method|default:'index'}
	<div id="menuLeftPage">
		<div class="menuLeft">
			<h1 onClick="window.location='./'" class="aree"><a href="./">Areas</a></h1>
	
			<div class="inside">
				<ul class="simpleMenuList" style="margin:10px 0px 10px 0px">
					<li {if $method == 'index'}class="on"{/if}>		<b>&#8250;</b> {$html->link('Albero delle Aree', '/areas')}</li>
					<li {if $method == 'viewArea'}class="on"{/if}>	<b>&#8250;</b> {$html->link('Nuova Area', '/areas/viewArea')}</li>
					<li {if $method == 'viewSection'}class="on"{/if}>	<b>&#8250;</b> {$html->link('Nuova Sezione', '/areas/viewSection')}</li>
				</ul>	
				<hr>
			</div>
		</div>
	<p>
	Testo a seguire
	</p>
	<hr>
	
	<div id="handlerChangeAlert">
<!--	
		<span id="hndVisualAlert"></span>
		<input type="checkbox" id="hndChkbox">
		<a href="#" onclick="javascript:$('#hndChkbox').toggleCheck()">Segnala</a>
		<br/>
		Seleziona qui se vuoi essere avvertito del cambiamento dei dati quando lasci la pagina.
-->
	</div>
	
	</div>
