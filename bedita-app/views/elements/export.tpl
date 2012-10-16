{if !empty($object)}

<ul class="menuleft insidecol">
	<li>
		<a href="javascript:void(0)" onclick="$('#export').slideToggle();">Export</a>
		<ul id="export" style="display:none;">
			<li><a href="">xml</a></li>
			<li><a href="">rtf</a></li>
			<li><a href="">PDF</a></li>
			<li><a href="">xhtml</a></li>
		</ul>
	
	</li>
</ul>

{/if}