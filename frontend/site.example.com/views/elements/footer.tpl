<div class="footer">
	
	<ul class="footel" style="width:140px; border:0px; margin-top:2px;">
		{foreach from=$conf->frontendLangs item="g" key="k"}
			<li>
				<a {if $currLang == $k}style="color:white;"{/if} title="{$g}" href="{$html->url('/')}lang/{$k}">{$g}</a>
			</li>
		{/foreach}

	</ul>
	
	<ul class="footel" style="border:0px; width:140px;">
		<li>{$publication.public_name|default:$publication.title}<br />{$beFront->year($smarty.now)}</li>
	</ul>
</div>
